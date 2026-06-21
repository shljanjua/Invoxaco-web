<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Setting;
use App\Services\PaymentGatewayFactory;

class PaymentSettingController extends Controller
{
    public function index(): void
    {
        $gateways = PaymentGatewayFactory::available();
        $enabled = [];
        $configured = [];

        foreach ($gateways as $gateway) {
            $enabled[$gateway] = Setting::get($gateway . '_enabled', '0') === '1';
            $instance = PaymentGatewayFactory::make($gateway);
            $configured[$gateway] = $instance !== null && $instance->isConfigured();
        }

        $this->view('admin/payment-settings/index', [
            'pageTitle' => 'Payment Settings',
            'gateways' => $gateways,
            'enabled' => $enabled,
            'configured' => $configured,
        ], 'layouts/admin');
    }

    public function update(): void
    {
        $this->validateCsrf();

        foreach (PaymentGatewayFactory::available() as $gateway) {
            $value = Request::input($gateway . '_enabled') ? '1' : '0';
            Setting::set($gateway . '_enabled', $value);
        }

        $this->flashAndRedirect('success', 'Payment settings updated.', url('admin/payment-settings'));
    }
}
