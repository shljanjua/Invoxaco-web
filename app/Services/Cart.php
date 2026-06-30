<?php

namespace App\Services;

use App\Core\Session;
use App\Models\DigitalProduct;

/**
 * Simple session-backed cart for digital products. Because every
 * product is a single-purchase digital good, the cart is just a set
 * of product IDs (quantity is always 1 per product).
 */
class Cart
{
    private const KEY = 'cart';

    /** @return int[] */
    public static function ids(): array
    {
        return array_values(array_unique(array_map('intval', Session::get(self::KEY, []))));
    }

    public static function add(int $productId): void
    {
        $ids = self::ids();
        if (!in_array($productId, $ids, true)) {
            $ids[] = $productId;
        }
        Session::put(self::KEY, $ids);
    }

    public static function remove(int $productId): void
    {
        Session::put(self::KEY, array_values(array_filter(self::ids(), fn ($id) => $id !== $productId)));
    }

    public static function clear(): void
    {
        Session::remove(self::KEY);
    }

    public static function count(): int
    {
        return count(self::ids());
    }

    public static function has(int $productId): bool
    {
        return in_array($productId, self::ids(), true);
    }

    /**
     * Resolves the cart to live, active products and computes the total.
     * Drops any product that has gone inactive or been deleted.
     *
     * @return array{items:array<int,array>,subtotal:float,total:float,currency:string}
     */
    public static function resolve(): array
    {
        $items = [];
        $subtotal = 0.0;
        $currency = 'USD';

        foreach (self::ids() as $id) {
            $product = DigitalProduct::find($id);
            if (!$product || (int) $product['is_active'] !== 1) {
                continue;
            }
            $price = DigitalProduct::effectivePrice($product);
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
