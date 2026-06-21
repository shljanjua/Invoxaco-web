<?php

namespace App\Services;

/**
 * Contract every payment gateway adapter must implement.
 * Enable a gateway by: 1) adding API keys to .env, 2) flipping its
 * `{gateway}_enabled` row in `settings` from Admin > Payment Settings,
 * 3) registering it in PaymentGatewayFactory.
 */
interface PaymentGatewayInterface
{
    public function name(): string;

    public function isConfigured(): bool;

    /** Returns a checkout/redirect URL for the given plan + billing cycle. */
    public function createCheckoutSession(array $user, string $plan, string $billingCycle): string;

    /** Verifies and processes an incoming webhook payload, returns true if handled. */
    public function handleWebhook(string $rawPayload, array $headers): bool;
}
