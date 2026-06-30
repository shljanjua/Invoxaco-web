<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Response;
use App\Models\DownloadGrant;
use App\Models\Order;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function index(): void
    {
        $this->view('admin/orders/index', [
            'pageTitle' => 'Store Orders',
            'orders' => Order::recent(200),
            'revenue' => Order::storeRevenue(),
        ], 'layouts/admin');
    }

    public function show(int $id): void
    {
        $order = Order::find($id);
        if (!$order) {
            Response::abort(404, 'Order not found');
        }

        $this->view('admin/orders/show', [
            'pageTitle' => 'Order #' . $id,
            'order' => $order,
            'items' => Order::items($id),
            'grants' => DownloadGrant::forOrder($id),
        ], 'layouts/admin');
    }

    /** Manually release a pending order (e.g. offline / bank-transfer payment). */
    public function markPaid(int $id): void
    {
        $this->validateCsrf();

        $order = Order::find($id);
        if (!$order) {
            Response::abort(404, 'Order not found');
        }

        OrderService::markPaid($id, $order['gateway'] ?: 'manual', $order['gateway_payment_id']);
        $this->flashAndRedirect('success', 'Order marked as paid and downloads released.', url('admin/orders/' . $id));
    }

    public function refund(int $id): void
    {
        $this->validateCsrf();

        if (!Order::find($id)) {
            Response::abort(404, 'Order not found');
        }

        Order::update($id, ['status' => 'refunded']);
        $this->flashAndRedirect('success', 'Order marked as refunded.', url('admin/orders/' . $id));
    }

    public function destroy(int $id): void
    {
        $this->validateCsrf();
        Order::delete($id);
        $this->flashAndRedirect('success', 'Order deleted.', url('admin/orders'));
    }
}
