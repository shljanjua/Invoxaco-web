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
            $type = $field['type'] ?? 'text';

            if ($type === 'line_items') {
                $descriptions = $_POST[$name . '_description'] ?? [];
                $qtys = $_POST[$name . '_qty'] ?? [];
                $prices = $_POST[$name . '_price'] ?? [];
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

            if ($type === 'group_list') {
                $columns = $field['columns'] ?? [];
                $colValues = [];
                foreach ($columns as $col) {
                    $colValues[$col['name']] = $_POST[$name . '_' . $col['name']] ?? [];
                }

                $rowCount = $colValues === [] ? 0 : max(array_map('count', $colValues));
                $rows = [];

                for ($i = 0; $i < $rowCount; $i++) {
                    $row = [];
                    $hasValue = false;
                    foreach ($columns as $col) {
                        $val = trim((string) ($colValues[$col['name']][$i] ?? ''));
                        $row[$col['name']] = $val;
                        $hasValue = $hasValue || $val !== '';
                    }
                    if ($hasValue) {
                        $rows[] = $row;
                    }
                }

                $data[$name] = $rows;

                continue;
            }

            $data[$name] = trim((string) ($_POST[$name] ?? ''));
        }

        if (isset($data['tax_rate'])) {
            $data['tax_rate'] = (float) $data['tax_rate'];
        }

        foreach ($fields as $field) {
            $name = $field['name'];

            if (($field['type'] ?? '') !== 'line_items' || !isset($data[$name]) || !is_array($data[$name])) {
                continue;
            }

            if ($name === 'items') {
                $data = array_merge($data, self::calculateLineItems($data['items'], $data['tax_rate'] ?? 0));
                continue;
            }

            $calculated = self::calculateLineItems($data[$name]);
            $data[$name] = $calculated['items'];
            $data[$name . '_subtotal'] = $calculated['subtotal'];
        }

        return $data;
    }
}
