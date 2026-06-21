<?php

namespace App\Services\Gateways;

use App\Services\PaymentGatewayInterface;

/**
 * Stub adapter. Wire up the `stripe/stripe-php` SDK and complete the three
 * methods below when enabling Stripe from Admin > Payment Settings.
 */
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
        throw new \RuntimeException('Stripe gateway is not yet configured. Add STRIPE_SECRET_KEY and complete StripeGateway::createCheckoutSession().');
    }

    public function handleWebhook(string $rawPayload, array $headers): bool
    {
        return false;
    }
}
