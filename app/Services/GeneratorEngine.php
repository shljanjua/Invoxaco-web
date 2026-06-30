<?php

namespace App\Services;

class GeneratorEngine
{
    private const PARTY_SUBFIELDS = ['full_name', 'id_no', 'address', 'phone', 'email'];
    private const OPTIONAL_CHARGE_FIELDS = ['shipping_cost', 'installation_charges', 'delivery_charges', 'training_charges', 'support_charges'];

    /**
     * Decides whether a generated document is a binding legal agreement that
     * should carry the "may not constitute legal advice" disclaimer.
     *
     * The trigger is the legal CONTENT of the document, not merely the presence
     * of a signature line: every template in the Legal Documents category, or
     * any template that contains formal contract clauses (a "*_clause" field)
     * or a governing-law field, is treated as a legal agreement. Routine signed
     * paperwork — invoices, receipts, financial statements, reports, salary
     * slips, plain business letters — is not, so it does not get the disclaimer.
     */
    public static function isLegalAgreement(array $template, array $fields): bool
    {
        if ((int) ($template['category_id'] ?? 0) === 3) {
            return true;
        }

        foreach ($fields as $field) {
            $name = (string) ($field['name'] ?? '');
            if ($name === 'governing_law' || str_ends_with($name, '_clause')) {
                return true;
            }
        }

        return false;
    }

    public static function calculateLineItems(array $items, float $taxRate = 0): array
    {
        $subtotal = 0;
        $itemDiscountTotal = 0;
        $itemTaxTotal = 0;

        foreach ($items as &$item) {
            $qty = (float) ($item['qty'] ?? 1);
            $price = (float) ($item['price'] ?? 0);
            $lineSubtotal = round($qty * $price, 2);

            $discountPct = (float) ($item['discount_percent'] ?? 0);
            $taxPct = (float) ($item['tax_percent'] ?? 0);
            $discountAmt = round($lineSubtotal * $discountPct / 100, 2);
            $taxableAmt = $lineSubtotal - $discountAmt;
            $taxAmt = round($taxableAmt * $taxPct / 100, 2);

            $item['total'] = round($taxableAmt + $taxAmt, 2);

            $subtotal += $lineSubtotal;
            $itemDiscountTotal += $discountAmt;
            $itemTaxTotal += $taxAmt;
        }

        $taxAmount = round(($subtotal - $itemDiscountTotal) * ($taxRate / 100), 2);

        return [
            'items' => $items,
            'subtotal' => round($subtotal, 2),
            'item_discount_total' => round($itemDiscountTotal, 2),
            'item_tax_total' => round($itemTaxTotal, 2),
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'grand_total' => round($subtotal - $itemDiscountTotal + $itemTaxTotal + $taxAmount, 2),
        ];
    }

    /**
     * Folds document-level discount (percent or fixed) and optional named
     * charges (shipping, installation, delivery, training, support) into the
     * grand total produced by calculateLineItems(). Only has an effect when
     * the generator's schema defines these well-known field names.
     */
    public static function applyDocumentLevelAdjustments(array $data): array
    {
        $base = (float) ($data['grand_total'] ?? 0);

        $discountAmount = 0.0;
        $discountValue = (float) ($data['discount_value'] ?? 0);
        if ($discountValue > 0) {
            $discountAmount = ($data['discount_type'] ?? 'fixed') === 'percent'
                ? round($base * $discountValue / 100, 2)
                : round($discountValue, 2);
        }

        $chargesTotal = 0.0;
        foreach (self::OPTIONAL_CHARGE_FIELDS as $chargeField) {
            if (isset($data[$chargeField])) {
                $chargesTotal += (float) $data[$chargeField];
            }
        }

        $data['document_discount_amount'] = round($discountAmount, 2);
        $data['extra_charges_total'] = round($chargesTotal, 2);
        $data['grand_total'] = round($base - $discountAmount + $chargesTotal, 2);

        return $data;
    }

    public static function collectFromRequest(array $fields): array
    {
        $data = [];

        foreach ($fields as $field) {
            $name = $field['name'];
            $type = $field['type'] ?? 'text';

            if ($type === 'line_items') {
                $data[$name] = self::collectLineItems($name);
                continue;
            }

            if ($type === 'group_list') {
                $data[$name] = self::collectGroupList($name, $field['columns'] ?? []);
                continue;
            }

            if ($type === 'party') {
                $data[$name] = self::collectParty($name);
                continue;
            }

            if ($type === 'signature') {
                $data[$name] = self::collectSignature($name);
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
                $data = self::applyDocumentLevelAdjustments($data);
                continue;
            }

            $calculated = self::calculateLineItems($data[$name]);
            $data[$name] = $calculated['items'];
            $data[$name . '_subtotal'] = $calculated['subtotal'];
        }

        return $data;
    }

    private static function collectLineItems(string $name): array
    {
        $descriptions = $_POST[$name . '_description'] ?? [];
        $units = $_POST[$name . '_unit'] ?? [];
        $qtys = $_POST[$name . '_qty'] ?? [];
        $prices = $_POST[$name . '_price'] ?? [];
        $discounts = $_POST[$name . '_discount_percent'] ?? [];
        $taxes = $_POST[$name . '_tax_percent'] ?? [];
        $items = [];

        foreach ($descriptions as $i => $desc) {
            if (trim((string) $desc) === '') {
                continue;
            }

            $item = [
                'description' => trim((string) $desc),
                'qty' => (float) ($qtys[$i] ?? 1),
                'price' => (float) ($prices[$i] ?? 0),
            ];

            if (isset($units[$i])) {
                $item['unit'] = trim((string) $units[$i]);
            }
            if (isset($discounts[$i]) && $discounts[$i] !== '') {
                $item['discount_percent'] = (float) $discounts[$i];
            }
            if (isset($taxes[$i]) && $taxes[$i] !== '') {
                $item['tax_percent'] = (float) $taxes[$i];
            }

            $items[] = $item;
        }

        return $items;
    }

    private static function collectGroupList(string $name, array $columns): array
    {
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

        return $rows;
    }

    private static function collectParty(string $name): array
    {
        $party = [];
        foreach (self::PARTY_SUBFIELDS as $sub) {
            $party[$sub] = trim((string) ($_POST[$name . '_' . $sub] ?? ''));
        }

        return $party;
    }

    /**
     * Collects a signature block: either a freshly uploaded image, a
     * canvas-drawn signature (base64 data URL), a request to reuse the
     * account's company stamp, or (on edit) the previously stored path
     * when the signer didn't change anything.
     */
    private static function collectSignature(string $name): array
    {
        $idNo = trim((string) ($_POST[$name . '_id_no'] ?? ''));
        $mode = (string) ($_POST[$name . '_mode'] ?? 'upload');
        $path = trim((string) ($_POST[$name . '_existing'] ?? '')) ?: null;
        $useStamp = false;

        if ($mode === 'stamp') {
            $useStamp = true;
            $path = null;
        } elseif ($mode === 'draw') {
            $dataUrl = (string) ($_POST[$name . '_data'] ?? '');
            if ($dataUrl !== '') {
                try {
                    $saved = FileUploader::storeDataUrlImage($dataUrl, 'signatures');
                    if ($saved) {
                        $path = $saved;
                    }
                } catch (\RuntimeException) {
                    // Keep the previously stored signature if the drawing failed to save.
                }
            }
        } else {
            $file = $_FILES[$name . '_file'] ?? null;
            if ($file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                try {
                    $saved = FileUploader::storeImage($file, 'signatures');
                    if ($saved) {
                        $path = $saved;
                    }
                } catch (\RuntimeException) {
                    // Keep the previously stored signature if the upload failed.
                }
            }
        }

        return [
            'path' => $path,
            'use_company_stamp' => $useStamp,
            'id_no' => $idNo,
        ];
    }
}
