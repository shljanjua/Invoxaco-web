<?php

namespace App\Services\Gateways;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Services\PaymentGatewayInterface;
use Stripe\BillingPortal\Session as PortalSession;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Customer;
use Stripe\Stripe;
use Stripe\Subscription as StripeSubscription;
use Stripe\Webhook;

class StripeGateway implements PaymentGatewayInterface
{
    public function name(): string
    {
        return 'stripe';
    }

    public function isConfigured(): bool
    {
        return !empty($_ENV['STRIPE_SECRET_KEY']);
    }

    public function createCheckoutSession(array $user, string $plan, string $billingCycle): string
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException('Stripe is not configured. Add STRIPE_SECRET_KEY in .env.');
        }

        $planConfig = config('plans')[$plan] ?? null;

        if (!$planConfig || $plan === 'free') {
            throw new \InvalidArgumentException('Invalid plan: ' . $plan);
        }

        $billingCycle = $billingCycle === 'yearly' ? 'yearly' : 'monthly';
        $amount = $billingCycle === 'yearly' ? $planConfig['price_yearly'] : $planConfig['price_monthly'];
        $interval = $billingCycle === 'yearly' ? 'year' : 'month';

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $customerId = $user['stripe_customer_id'] ?? null;

        if (!$customerId) {
            $customer = Customer::create([
                'email' => $user['email'],
                'name' => $user['name'],
                'metadata' => ['user_id' => (string) $user['id']],
            ]);
            $customerId = $customer->id;
            User::setStripeCustomerId((int) $user['id'], $customerId);
        }

        $metadata = [
            'user_id' => (string) $user['id'],
            'plan' => $plan,
            'billing_cycle' => $billingCycle,
        ];

        $session = CheckoutSession::create([
            'mode' => 'subscription',
            'customer' => $customerId,
            'client_reference_id' => (string) $user['id'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => $planConfig['name'] . ' Plan'],
                    'unit_amount' => (int) round($amount * 100),
                    'recurring' => ['interval' => $interval],
                ],
                'quantity' => 1,
            ]],
            'metadata' => $metadata,
            'subscription_data' => ['metadata' => $metadata],
            'success_url' => url('billing/success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => url('billing/cancel'),
        ]);

        return $session->url;
    }

    public function createBillingPortalSession(array $user): string
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException('Stripe is not configured.');
        }

        if (empty($user['stripe_customer_id'])) {
            throw new \RuntimeException('No billing account found for this user yet.');
        }

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $portalSession = PortalSession::create([
            'customer' => $user['stripe_customer_id'],
            'return_url' => url('dashboard'),
        ]);

        return $portalSession->url;
    }

    public function handleWebhook(string $rawPayload, array $headers): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $webhookSecret = $_ENV['STRIPE_WEBHOOK_SECRET'] ?? '';
        $signature = $headers['Stripe-Signature'] ?? '';

        try {
            if ($webhookSecret !== '') {
                $event = Webhook::constructEvent($rawPayload, $signature, $webhookSecret);
            } else {
                $event = \Stripe\Event::constructFrom(json_decode($rawPayload, true, flags: JSON_THROW_ON_ERROR));
            }
        } catch (\Throwable) {
            return false;
        }

        return match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutCompleted($event->data->object),
            'customer.subscription.updated' => $this->handleSubscriptionUpdated($event->data->object),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event->data->object),
            'invoice.paid' => $this->handleInvoicePaid($event->data->object),
            'invoice.payment_failed' => $this->handleInvoiceFailed($event->data->object),
            default => true,
        };
    }

    private function handleCheckoutCompleted(object $session): bool
    {
        $userId = (int) ($session->metadata->user_id ?? $session->client_reference_id ?? 0);

        if (!$userId) {
            return false;
        }

        $user = User::find($userId);

        if (!$user) {
            return false;
        }

        if (!empty($session->customer) && empty($user['stripe_customer_id'])) {
            User::setStripeCustomerId($userId, (string) $session->customer);
        }

        $plan = (string) ($session->metadata->plan ?? '');
        $billingCycle = (string) ($session->metadata->billing_cycle ?? 'monthly');

        if ($plan === '') {
            return true;
        }

        $expiresAt = null;
        $subscriptionId = $session->subscription ?? null;

        if ($subscriptionId) {
            $stripeSub = StripeSubscription::retrieve((string) $subscriptionId);
            $expiresAt = date('Y-m-d H:i:s', $stripeSub->current_period_end);

            Subscription::upsertForUser($userId, [
                'plan' => $plan,
                'billing_cycle' => $billingCycle,
                'status' => 'active',
                'gateway' => 'stripe',
                'gateway_subscription_id' => (string) $subscriptionId,
                'current_period_end' => $expiresAt,
            ]);
        }

        User::applyPlan($userId, $plan, $billingCycle, $expiresAt);

        return true;
    }

    private function handleSubscriptionUpdated(object $sub): bool
    {
        $existing = Subscription::findByGatewaySubscriptionId((string) $sub->id);
        $userId = (int) ($sub->metadata->user_id ?? $existing['user_id'] ?? 0);

        if (!$userId) {
            return false;
        }

        $plan = (string) ($sub->metadata->plan ?? $existing['plan'] ?? '');
        $billingCycle = (string) ($sub->metadata->billing_cycle ?? $existing['billing_cycle'] ?? 'monthly');

        if ($plan === '') {
            return true;
        }

        $status = match (true) {
            in_array($sub->status, ['active', 'trialing'], true) => 'active',
            $sub->status === 'canceled' => 'cancelled',
            default => 'active',
        };
        $expiresAt = date('Y-m-d H:i:s', $sub->current_period_end);

        Subscription::upsertForUser($userId, [
            'plan' => $plan,
            'billing_cycle' => $billingCycle,
            'status' => $status,
            'gateway' => 'stripe',
            'gateway_subscription_id' => (string) $sub->id,
            'current_period_end' => $expiresAt,
        ]);

        if ($status === 'active') {
            User::applyPlan($userId, $plan, $billingCycle, $expiresAt);
        }

        return true;
    }

    private function handleSubscriptionDeleted(object $sub): bool
    {
        $existing = Subscription::findByGatewaySubscriptionId((string) $sub->id);
        $userId = (int) ($sub->metadata->user_id ?? $existing['user_id'] ?? 0);

        if (!$userId) {
            return false;
        }

        Subscription::upsertForUser($userId, [
            'plan' => (string) ($existing['plan'] ?? $sub->metadata->plan ?? 'free'),
            'billing_cycle' => $existing['billing_cycle'] ?? null,
            'status' => 'cancelled',
            'gateway' => 'stripe',
            'gateway_subscription_id' => (string) $sub->id,
            'current_period_end' => null,
        ]);

        User::applyPlan($userId, 'free', null, null);

        return true;
    }

    private function handleInvoicePaid(object $invoice): bool
    {
        $paymentId = (string) ($invoice->payment_intent ?? $invoice->id);

        if (Payment::findByGatewayPaymentId($paymentId)) {
            return true;
        }

        [$userId, $subscriptionRowId] = $this->resolveUserForInvoice($invoice);

        if (!$userId) {
            return false;
        }

        Payment::create([
            'user_id' => $userId,
            'subscription_id' => $subscriptionRowId,
            'gateway' => 'stripe',
            'gateway_payment_id' => $paymentId,
            'amount' => round((float) ($invoice->amount_paid ?? 0) / 100, 2),
            'currency' => strtoupper((string) ($invoice->currency ?? 'usd')),
            'status' => 'completed',
        ]);

        return true;
    }

    private function handleInvoiceFailed(object $invoice): bool
    {
        $paymentId = (string) ($invoice->payment_intent ?? $invoice->id);

        if (Payment::findByGatewayPaymentId($paymentId)) {
            return true;
        }

        [$userId, $subscriptionRowId] = $this->resolveUserForInvoice($invoice);

        if (!$userId) {
            return false;
        }

        Payment::create([
            'user_id' => $userId,
            'subscription_id' => $subscriptionRowId,
            'gateway' => 'stripe',
            'gateway_payment_id' => $paymentId,
            'amount' => round((float) ($invoice->amount_due ?? 0) / 100, 2),
            'currency' => strtoupper((string) ($invoice->currency ?? 'usd')),
            'status' => 'failed',
        ]);

        return true;
    }

    private function resolveUserForInvoice(object $invoice): array
    {
        $subscriptionId = $invoice->subscription ?? null;
        $subRow = $subscriptionId ? Subscription::findByGatewaySubscriptionId((string) $subscriptionId) : null;

        if ($subRow) {
            return [(int) $subRow['user_id'], (int) $subRow['id']];
        }

        $customerId = $invoice->customer ?? null;
        $user = $customerId ? User::findBy('stripe_customer_id', (string) $customerId) : null;

        return [$user ? (int) $user['id'] : null, null];
    }
}
