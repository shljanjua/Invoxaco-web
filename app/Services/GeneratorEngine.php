<?php

namespace App\Services;

class GeneratorEngine
{
    public static function calculateLineItems(array $items, float $taxRate = 0): array
    {
        $subtotal = 0;

        foreach ($items as &$item) {
            $qty = (float) ($item['qty'] ?? 1);
            $price = (float) ($item['price'] ?? 0);
            $item['total'] = round($qty * $price, 2);
            $subtotal += $item['total'];
        }

        $taxAmount = round($subtotal * ($taxRate / 100), 2);

        return [
            'items' => $items,
            'subtotal' => round($subtotal, 2),
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'grand_total' => round($subtotal + $taxAmount, 2),
        ];
    }

    public static function collectFromRequest(array $fields): array
    {
        $data = [];

        foreach ($fields as $field) {
            $name = $field['name'];

            if ($field['type'] === 'line_items') {
                $descriptions = $_POST['items_description'] ?? [];
                $qtys = $_POST['items_qty'] ?? [];
                $prices = $_POST['items_price'] ?? [];
                $items = [];

                foreach ($descriptions as $i => $desc) {
                    if (trim((string) $desc) === '') {
                        continue;
                    }
                    $items[] = [
                        'description' => trim((string) $desc),
                        'qty' => (float) ($qtys[$i] ?? 1),
                        'price' => (float) ($prices[$i] ?? 0),
                    ];
                }

                $data[$name] = $items;

                continue;
            }

            $data[$name] = trim((string) ($_POST[$name] ?? ''));
        }

        if (isset($data['tax_rate'])) {
            $data['tax_rate'] = (float) $data['tax_rate'];
        }

        if (isset($data['items']) && is_array($data['items'])) {
            $calculated = self::calculateLineItems($data['items'], $data['tax_rate'] ?? 0);
            $data = array_merge($data, $calculated);
        }

        return $data;
    }
}
