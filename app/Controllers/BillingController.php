<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Models\Setting;
use App\Services\Gateways\StripeGateway;
use App\Services\PaymentGatewayFactory;

class BillingController extends Controller
{
    public function checkout(string $plan): void
    {
        $this->validateCsrf();

        if (!in_array($plan, ['pro', 'premium'], true)) {
            $this->flashAndRedirect('error', 'Invalid plan selected.', url('pricing'));
        }

        if (Setting::get('stripe_enabled', '0') !== '1') {
            $this->flashAndRedirect('error', 'Online upgrades aren\'t available yet. Please contact support.', url('pricing'));
        }

        $gateway = PaymentGatewayFactory::make('stripe');

        if (!$gateway || !$gateway->isConfigured()) {
            $this->flashAndRedirect('error', 'Payments aren\'t configured yet. Please contact support.', url('pricing'));
        }

        $billingCycle = Request::string('billing_cycle') === 'yearly' ? 'yearly' : 'monthly';
        $user = Auth::user();

        try {
            $checkoutUrl = $gateway->createCheckoutSession($user, $plan, $billingCycle);
        } catch (\Throwable $e) {
            $this->flashAndRedirect('error', 'Could not start checkout: ' . $e->getMessage(), url('pricing'));
        }

        $this->redirect($checkoutUrl);
    }

    public function portal(): void
    {
        $user = Auth::user();
        $gateway = PaymentGatewayFactory::make('stripe');

        if (!$gateway instanceof StripeGateway || !$gateway->isConfigured()) {
            $this->flashAndRedirect('error', 'Billing management isn\'t available yet. Please contact support.', url('dashboard'));
        }

        try {
            $url = $gateway->createBillingPortalSession($user);
        } catch (\Throwable $e) {
            $this->flashAndRedirect('error', $e->getMessage(), url('dashboard'));
        }

        $this->redirect($url);
    }

    public function success(): void
    {
        $this->flashAndRedirect('success', 'Thanks! Your payment is processing - your plan will update within a few seconds.', url('dashboard'));
    }

    public function cancel(): void
    {
        $this->flashAndRedirect('error', 'Checkout was cancelled. No charges were made.', url('pricing'));
    }

    public function webhook(): void
    {
        $payload = file_get_contents('php://input') ?: '';
        $headers = ['Stripe-Signature' => $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? ''];

        $gateway = PaymentGatewayFactory::make('stripe');
        $handled = $gateway !== null && $gateway->handleWebhook($payload, $headers);

        $this->json(['received' => $handled], $handled ? 200 : 400);
    }
}
