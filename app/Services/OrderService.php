<?php

namespace App\Services;

use App\Core\View;
use App\Models\DigitalProduct;
use App\Models\DownloadGrant;
use App\Models\Order;
use App\Models\OrderItem;

/**
 * Central fulfillment logic for the digital store: turning a resolved
 * cart into an order, granting downloads once payment clears, and
 * emailing the buyer their secure download links. Used by both the
 * checkout controller (free / instant orders) and the Stripe webhook.
 */
class OrderService
{
    /**
     * Creates a pending order (or a 'free' order when total is 0) plus
     * its line items. Does NOT grant downloads yet unless free.
     *
     * @param array $cart   Result of Cart::resolve()
     * @return array        The created order row
     */
    public static function createFromCart(array $cart, array $customer, ?int $userId = null): array
    {
        $isFree = $cart['total'] <= 0.0;

        $orderId = Order::create([
            'user_id' => $userId,
            'token' => bin2hex(random_bytes(24)),
            'customer_name' => $customer['name'] ?? null,
            'customer_email' => $customer['email'],
            'subtotal' => $cart['subtotal'],
            'total' => $cart['total'],
            'currency' => $cart['currency'],
            'status' => $isFree ? 'free' : 'pending',
            'gateway' => $isFree ? 'free' : null,
            'paid_at' => $isFree ? date('Y-m-d H:i:s') : null,
        ]);

        foreach ($cart['items'] as $product) {
            OrderItem::create([
                'order_id' => $orderId,
                'product_id' => $product['id'],
                'product_name' => $product['name'],
                'price' => $product['effective_price'] ?? DigitalProduct::effectivePrice($product),
            ]);
        }

        $order = Order::find($orderId);

        if ($isFree) {
            self::grantDownloads($order);
            self::sendReceipt($order);
        }

        return $order;
    }

    /**
     * Marks an order paid and fulfils it (idempotent — safe to call
     * from both the success redirect and the webhook).
     */
    public static function markPaid(int $orderId, string $gateway, ?string $gatewayPaymentId = null): bool
    {
        $order = Order::find($orderId);
        if (!$order) {
            return false;
        }

        if (in_array($order['status'], ['paid', 'free'], true)) {
            return true; // already fulfilled
        }

        Order::update($orderId, [
            'status' => 'paid',
            'gateway' => $gateway,
            'gateway_payment_id' => $gatewayPaymentId,
            'paid_at' => date('Y-m-d H:i:s'),
        ]);

        $order = Order::find($orderId);
        self::grantDownloads($order);
        self::sendReceipt($order);

        return true;
    }

    /** Creates one download grant per purchased item (idempotent per item). */
    public static function grantDownloads(array $order): void
    {
        $existing = DownloadGrant::where(['order_id' => $order['id']]);
        $grantedItemIds = array_map(fn ($g) => (int) $g['order_item_id'], $existing);

        foreach (Order::items((int) $order['id']) as $item) {
            if (in_array((int) $item['id'], $grantedItemIds, true)) {
                continue;
            }

            DownloadGrant::create([
                'order_id' => $order['id'],
                'order_item_id' => $item['id'],
                'product_id' => $item['product_id'],
                'user_id' => $order['user_id'],
                'email' => $order['customer_email'],
                'token' => bin2hex(random_bytes(24)),
                'max_downloads' => 0, // unlimited; cap here if desired
                'expires_at' => null,
            ]);
        }
    }

    public static function sendReceipt(array $order): void
    {
        $grants = DownloadGrant::forOrder((int) $order['id']);

        if (empty($order['customer_email'])) {
            return;
        }

        $downloads = [];
        foreach ($grants as $g) {
            $downloads[] = [
                'name' => $g['product_name'] ?? 'Your download',
                'url' => url('store/download/' . $g['token']),
            ];
        }

        $body = View::render('emails/order-receipt', [
            'order' => $order,
            'downloads' => $downloads,
            'manageUrl' => url('store/order/' . $order['token']),
        ], 'layouts/email');

        try {
            (new MailService())->send(
                $order['customer_email'],
                $order['customer_name'] ?: $order['customer_email'],
                'Your Invoxaco order is ready to download',
                $body
            );
        } catch (\Throwable $e) {
            \App\Core\Logger::error('Order receipt email failed: ' . $e->getMessage());
        }
    }
}
