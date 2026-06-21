<?php

namespace App\Services;

use App\Services\Gateways\StripeGateway;

class PaymentGatewayFactory
{
    private const GATEWAYS = [
        'stripe' => StripeGateway::class,
        // 'paypal' => Gateways\PayPalGateway::class,
        // 'paddle' => Gateways\PaddleGateway::class,
        // 'lemonsqueezy' => Gateways\LemonSqueezyGateway::class,
    ];

    public static function make(string $gateway): ?PaymentGatewayInterface
    {
        $class = self::GATEWAYS[$gateway] ?? null;

        return $class ? new $class() : null;
    }

    public static function available(): array
    {
        return array_keys(self::GATEWAYS);
    }
}
