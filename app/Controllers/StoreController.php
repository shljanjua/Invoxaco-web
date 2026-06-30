<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\DigitalProduct;
use App\Models\DownloadGrant;
use App\Models\Order;
use App\Models\StoreCategory;
use App\Services\Cart;
use App\Services\OrderService;
use App\Services\PaymentGatewayFactory;
use App\Models\Setting;

class StoreController extends Controller
{
    /** Storefront catalog (optionally filtered by ?type=). */
    public function index(): void
    {
        $type = Request::string('type') ?: null;

        $this->view('store/index', [
            'pageTitle' => 'Digital Store',
            'metaTitle' => 'Digital Store - E-books, Templates & Business Documents | Invoxaco',
            'metaDescription' => 'Buy professional business e-books, document templates, spreadsheets and guides. Instant secure download after checkout.',
            'categories' => StoreCategory::ordered(),
            'products' => DigitalProduct::published(null, $type),
            'activeCategory' => null,
            'activeType' => $type,
            'cartCount' => Cart::count(),
        ], 'layouts/app');
    }

    public function category(string $slug): void
    {
        $category = StoreCategory::findBySlug($slug);
        if (!$category) {
            Response::abort(404, 'Category not found');
        }

        $this->view('store/index', [
            'pageTitle' => $category['name'],
            'metaTitle' => $category['name'] . ' - Invoxaco Digital Store',
            'metaDescription' => $category['description'] ?: ('Browse ' . $category['name'] . ' in the Invoxaco digital store.'),
            'categories' => StoreCategory::ordered(),
            'products' => DigitalProduct::published((int) $category['id']),
            'activeCategory' => $category,
            'activeType' => null,
            'cartCount' => Cart::count(),
        ], 'layouts/app');
    }

    public function show(string $slug): void
    {
        $product = DigitalProduct::findBySlug($slug);
        if (!$product || (int) $product['is_active'] !== 1) {
            Response::abort(404, 'Product not found');
        }

        $category = $product['category_id'] ? StoreCategory::find((int) $product['category_id']) : null;

        $this->view('store/show', [
            'pageTitle' => $product['name'],
            'metaTitle' => $product['meta_title'] ?: ($product['name'] . ' | Invoxaco Store'),
            'metaDescription' => $product['meta_description'] ?: $product['short_description'],
            'product' => $product,
            'category' => $category,
            'related' => array_slice(array_filter(
                DigitalProduct::published($product['category_id'] ? (int) $product['category_id'] : null),
                fn ($p) => (int) $p['id'] !== (int) $product['id']
            ), 0, 3),
            'inCart' => Cart::has((int) $product['id']),
            'cartCount' => Cart::count(),
        ], 'layouts/app');
    }

    public function cartAdd(): void
    {
        $this->validateCsrf();
        $id = (int) Request::input('product_id');
        $product = DigitalProduct::find($id);

        if (!$product || (int) $product['is_active'] !== 1) {
            $this->flashAndRedirect('error', 'That product is not available.', url('store'));
        }

        Cart::add($id);

        if (Request::input('buy_now')) {
            $this->redirect(url('store/checkout'));
        }

        $this->flashAndRedirect('success', 'Added to cart.', url('store/cart'));
    }

    public function cartRemove(): void
    {
        $this->validateCsrf();
        Cart::remove((int) Request::input('product_id'));
        $this->flashAndRedirect('success', 'Removed from cart.', url('store/cart'));
    }

    public function cart(): void
    {
        $cart = Cart::resolve();

        $this->view('store/cart', [
            'pageTitle' => 'Your Cart',
            'cart' => $cart,
            'cartCount' => count($cart['items']),
        ], 'layouts/app');
    }

    public function checkout(): void
    {
        $cart = Cart::resolve();

        if (empty($cart['items'])) {
            $this->flashAndRedirect('error', 'Your cart is empty.', url('store'));
        }

        $user = Auth::user();

        $this->view('store/checkout', [
            'pageTitle' => 'Checkout',
            'cart' => $cart,
            'user' => $user,
            'stripeEnabled' => Setting::get('stripe_enabled', '0') === '1',
            'cartCount' => count($cart['items']),
        ], 'layouts/app');
    }

    public function checkoutProcess(): void
    {
        $this->validateCsrf();
        $cart = Cart::resolve();

        if (empty($cart['items'])) {
            $this->flashAndRedirect('error', 'Your cart is empty.', url('store'));
        }

        $user = Auth::user();
        $name = $user['name'] ?? trim(Request::string('customer_name'));
        $email = $user['email'] ?? trim(Request::string('customer_email'));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flashAndRedirect('error', 'Please enter a valid email address to receive your downloads.', url('store/checkout'));
        }

        $order = OrderService::createFromCart($cart, ['name' => $name, 'email' => $email], $user['id'] ?? null);

        // Free order: already fulfilled, send straight to the success page.
        if ($order['status'] === 'free') {
            Cart::clear();
            $this->redirect(url('store/success') . '?order=' . $order['token']);
        }

        // Paid order: hand off to Stripe one-time checkout.
        if (Setting::get('stripe_enabled', '0') !== '1') {
            $this->flashAndRedirect('error', 'Online payment isn\'t available right now. Please contact support to complete your purchase.', url('store/checkout'));
        }

        $gateway = PaymentGatewayFactory::make('stripe');
        if (!$gateway || !$gateway->isConfigured()) {
            $this->flashAndRedirect('error', 'Payments aren\'t configured yet. Please contact support.', url('store/checkout'));
        }

        try {
            $url = $gateway->createProductCheckoutSession($order, Order::items((int) $order['id']));
        } catch (\Throwable $e) {
            $this->flashAndRedirect('error', 'Could not start checkout: ' . $e->getMessage(), url('store/checkout'));
        }

        Cart::clear();
        $this->redirect($url);
    }

    /** Post-payment landing page; confirms payment if the webhook hasn't fired yet. */
    public function success(): void
    {
        $order = Order::findByToken(Request::string('order'));
        if (!$order) {
            Response::abort(404, 'Order not found');
        }

        // Fallback confirmation via Stripe if still pending and we have a session id.
        if ($order['status'] === 'pending') {
            $sessionId = Request::string('session_id');
            if ($sessionId !== '') {
                $gateway = PaymentGatewayFactory::make('stripe');
                if ($gateway && method_exists($gateway, 'confirmCheckoutSession')) {
                    [$paid, $paymentId] = $gateway->confirmCheckoutSession($sessionId);
                    if ($paid) {
                        OrderService::markPaid((int) $order['id'], 'stripe', $paymentId);
                        $order = Order::find((int) $order['id']);
                    }
                }
            }
        }

        $grants = DownloadGrant::forOrder((int) $order['id']);

        $this->view('store/success', [
            'pageTitle' => 'Order Confirmation',
            'order' => $order,
            'grants' => $grants,
            'paid' => in_array($order['status'], ['paid', 'free'], true),
        ], 'layouts/app');
    }

    /** Re-view an order by its token (e.g. from the receipt email). */
    public function orderView(string $token): void
    {
        $order = Order::findByToken($token);
        if (!$order) {
            Response::abort(404, 'Order not found');
        }

        $this->view('store/success', [
            'pageTitle' => 'Your Order',
            'order' => $order,
            'grants' => DownloadGrant::forOrder((int) $order['id']),
            'paid' => in_array($order['status'], ['paid', 'free'], true),
        ], 'layouts/app');
    }

    /** Authenticated buyer's download library. */
    public function downloads(): void
    {
        $user = Auth::user();

        $this->view('store/downloads', [
            'pageTitle' => 'My Downloads',
            'grants' => DownloadGrant::libraryForUser((int) $user['id']),
        ], 'layouts/app');
    }

    /** Secure file streaming. Token is the unguessable grant secret. */
    public function download(string $token): void
    {
        $grant = DownloadGrant::findByToken($token);
        if (!$grant) {
            Response::abort(404, 'Download not found');
        }

        $order = Order::find((int) $grant['order_id']);
        if (!$order || !in_array($order['status'], ['paid', 'free'], true)) {
            Response::abort(403, 'This download is not available until payment has cleared.');
        }

        if ($grant['expires_at'] !== null && strtotime($grant['expires_at']) < time()) {
            Response::abort(410, 'This download link has expired.');
        }

        if ((int) $grant['max_downloads'] > 0 && (int) $grant['download_count'] >= (int) $grant['max_downloads']) {
            Response::abort(429, 'This download has reached its limit.');
        }

        $product = $grant['product_id'] ? DigitalProduct::find((int) $grant['product_id']) : null;
        if (!$product || empty($product['file_path'])) {
            Response::abort(404, 'The file for this product is no longer available.');
        }

        $path = \App\Services\ProductFileUploader::absolutePath($product['file_path']);
        if (!is_file($path)) {
            Response::abort(404, 'File missing on server. Please contact support.');
        }

        DownloadGrant::incrementCount((int) $grant['id']);
        DigitalProduct::incrementDownloads((int) $product['id']);

        $downloadName = $product['file_name'] ?: ($product['slug'] . '.' . pathinfo($path, PATHINFO_EXTENSION));

        header('Content-Type: ' . ($product['file_mime'] ?: 'application/octet-stream'));
        header('Content-Disposition: attachment; filename="' . str_replace('"', '', $downloadName) . '"');
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: private, no-store');
        header('X-Content-Type-Options: nosniff');

        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        readfile($path);
        exit;
    }
}
