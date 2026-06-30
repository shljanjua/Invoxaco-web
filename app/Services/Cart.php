<?php

namespace App\Services;

use App\Core\Session;
use App\Models\DigitalProduct;

/**
 * Session-backed cart for digital products. Stored as a map of
 * productId => chosenAmount, where chosenAmount is the buyer's
 * "pay what you want" amount (or null for normal fixed-price items).
 */
class Cart
{
    private const KEY = 'cart';

    /** @return array<int,float|null> productId => chosen amount (or null) */
    private static function map(): array
    {
        $raw = Session::get(self::KEY, []);
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $id => $amount) {
            // Back-compat: an old cart was a plain list of ids.
            if (is_int($id) && !is_numeric($amount) && $amount !== null) {
                $out[(int) $amount] = null;
            } else {
                $out[(int) $id] = ($amount === null) ? null : (float) $amount;
            }
        }
        return $out;
    }

    /** @return int[] */
    public static function ids(): array
    {
        return array_map('intval', array_keys(self::map()));
    }

    public static function add(int $productId, ?float $amount = null): void
    {
        $map = self::map();
        $map[$productId] = $amount;
        Session::put(self::KEY, $map);
    }

    public static function remove(int $productId): void
    {
        $map = self::map();
        unset($map[$productId]);
        Session::put(self::KEY, $map);
    }

    public static function clear(): void
    {
        Session::remove(self::KEY);
    }

    public static function count(): int
    {
        return count(self::map());
    }

    public static function has(int $productId): bool
    {
        return array_key_exists($productId, self::map());
    }

    /**
     * Resolves the cart to live, active products and computes the total.
     * Drops any product that has gone inactive or been deleted. Honours
     * pay-what-you-want amounts, clamped to each product's minimum.
     *
     * @return array{items:array<int,array>,subtotal:float,total:float,currency:string}
     */
    public static function resolve(): array
    {
        $items = [];
        $subtotal = 0.0;
        $currency = 'USD';

        foreach (self::map() as $id => $chosen) {
            $product = DigitalProduct::find((int) $id);
            if (!$product || (int) $product['is_active'] !== 1) {
                continue;
            }

            if (DigitalProduct::isPayWhatYouWant($product)) {
                $price = DigitalProduct::resolvePwywAmount($product, $chosen);
                $product['is_pwyw'] = true;
            } else {
                $price = DigitalProduct::effectivePrice($product);
                $product['is_pwyw'] = false;
            }

            $product['effective_price'] = $price;
            $items[] = $product;
            $subtotal += $price;
            $currency = $product['currency'] ?: $currency;
        }

        return [
            'items' => $items,
            'subtotal' => round($subtotal, 2),
            'total' => round($subtotal, 2),
            'currency' => $currency,
        ];
    }
}
