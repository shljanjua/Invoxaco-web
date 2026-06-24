<?php

namespace App\Services;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\JcTable;

class DocxService
{
    /**
     * Generic DOCX renderer driven by the same field schema used for the PDF/HTML form.
     * Produces an editable Word document: title, key/value fields, and a line-items table when present.
     */
    public static function fromDocument(string $title, array $fields, array $data, ?string $logoPath = null, bool $showLegalDisclaimer = false): string
    {
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Calibri');
        $phpWord->setDefaultFontSize(11);

        $section = $phpWord->addSection(['marginLeft' => 900, 'marginRight' => 900]);

        if ($logoPath && file_exists($logoPath)) {
            $section->addImage($logoPath, ['width' => 120, 'height' => 60, 'alignment' => JcTable::START]);
        }

        $section->addText($title, ['bold' => true, 'size' => 18, 'color' => '1F2A44']);
        $section->addTextBreak(1);

        foreach ($fields as $field) {
            if (in_array($field['type'], ['line_items', 'group_list', 'party', 'signature'], true)) {
                continue;
            }

            $value = $data[$field['name']] ?? '';
            if ($value === '' || $value === null) {
                continue;
            }

            $section->addText($field['label'], ['bold' => true, 'size' => 10, 'color' => '666666']);
            $section->addText(is_array($value) ? implode(', ', $value) : (string) $value);
            $section->addTextBreak(1);
        }

        foreach ($fields as $field) {
            if ($field['type'] !== 'party') {
                continue;
            }

            $party = $data[$field['name']] ?? [];
            if (!is_array($party)) {
                continue;
            }

            $section->addText($field['label'], ['bold' => true, 'size' => 10, 'color' => '666666']);
            if (!empty($party['full_name'])) {
                $section->addText((string) $party['full_name'], ['bold' => true]);
            }
            foreach (['id_no' => 'ID/Reg No', 'address' => null, 'phone' => null, 'email' => null] as $key => $prefix) {
                if (!empty($party[$key])) {
                    $section->addText($prefix ? $prefix . ': ' . $party[$key] : (string) $party[$key]);
                }
            }
            $section->addTextBreak(1);
        }

        foreach ($fields as $field) {
            if ($field['type'] !== 'line_items') {
                continue;
            }

            $name = $field['name'];
            $items = $data[$name] ?? null;
            if (!is_array($items) || empty($items)) {
                continue;
            }

            $hasUnit = !empty($field['has_unit']);
            $hasItemDiscount = !empty($field['per_item_discount']);
            $hasItemTax = !empty($field['per_item_tax']);

            $section->addText($field['label'], ['bold' => true, 'size' => 12, 'color' => '1F2A44']);

            $headings = ['Description'];
            if ($hasUnit) { $headings[] = 'Unit'; }
            $headings[] = 'Qty';
            $headings[] = 'Price';
            if ($hasItemDiscount) { $headings[] = 'Disc %'; }
            if ($hasItemTax) { $headings[] = 'Tax %'; }
            $headings[] = 'Total';

            $table = $section->addTable(['borderSize' => 6, 'borderColor' => 'CCCCCC', 'cellMargin' => 80]);
            $table->addRow();
            foreach ($headings as $heading) {
                $table->addCell(2500)->addText($heading, ['bold' => true]);
            }

            $isPrimary = $name === 'items';
            $subtotal = 0;
            foreach ($items as $item) {
                $qty = (float) ($item['qty'] ?? 1);
                $price = (float) ($item['price'] ?? 0);
                $lineTotal = $isPrimary ? (float) ($item['total'] ?? ($qty * $price)) : $qty * $price;
                $subtotal += $isPrimary ? round($qty * $price, 2) : $lineTotal;

                $table->addRow();
                $table->addCell(2500)->addText((string) ($item['description'] ?? ''));
                if ($hasUnit) {
                    $table->addCell(2500)->addText((string) ($item['unit'] ?? ''));
                }
                $table->addCell(2500)->addText((string) $qty);
                $table->addCell(2500)->addText(number_format($price, 2));
                if ($hasItemDiscount) {
                    $table->addCell(2500)->addText((string) ($item['discount_percent'] ?? 0) . '%');
                }
                if ($hasItemTax) {
                    $table->addCell(2500)->addText((string) ($item['tax_percent'] ?? 0) . '%');
                }
                $table->addCell(2500)->addText(number_format($lineTotal, 2));
            }

            $section->addTextBreak(1);
            $section->addText('Subtotal: ' . number_format($isPrimary ? (float) ($data['subtotal'] ?? $subtotal) : $subtotal, 2), ['bold' => true, 'size' => 12]);

            if ($isPrimary) {
                if (!empty($data['item_discount_total'])) {
                    $section->addText('Line Item Discounts: -' . number_format((float) $data['item_discount_total'], 2));
                }
                if (!empty($data['tax_rate'])) {
                    $section->addText('Tax (' . $data['tax_rate'] . '%): ' . number_format((float) ($data['tax_amount'] ?? 0), 2));
                }
                if (!empty($data['document_discount_amount'])) {
                    $section->addText('Discount: -' . number_format((float) $data['document_discount_amount'], 2));
                }
                if (!empty($data['extra_charges_total'])) {
                    $section->addText('Extra Charges: ' . number_format((float) $data['extra_charges_total'], 2));
                }
                if (!empty($data['tax_rate']) || !empty($data['document_discount_amount']) || !empty($data['extra_charges_total']) || !empty($data['item_discount_total'])) {
                    $section->addText('Grand Total: ' . number_format((float) ($data['grand_total'] ?? 0), 2), ['bold' => true, 'size' => 13]);
                }
            }

            $section->addTextBreak(1);
        }

        foreach ($fields as $field) {
            if ($field['type'] !== 'group_list') {
                continue;
            }

            $rows = $data[$field['name']] ?? null;
            if (!is_array($rows) || empty($rows)) {
                continue;
            }

            $section->addText($field['label'], ['bold' => true, 'size' => 12, 'color' => '1F2A44']);

            $cols = array_values($field['columns'] ?? []);
            $titleCol = $cols[0] ?? null;
            $subCol = $cols[1] ?? null;

            foreach ($rows as $row) {
                if ($titleCol && ($row[$titleCol['name']] ?? '') !== '') {
                    $section->addText((string) $row[$titleCol['name']], ['bold' => true, 'size' => 11]);
                }
                if ($subCol && ($row[$subCol['name']] ?? '') !== '') {
                    $section->addText((string) $row[$subCol['name']], ['color' => '2563EB', 'size' => 10]);
                }
                foreach (array_slice($cols, 2) as $col) {
                    if (($row[$col['name']] ?? '') !== '') {
                        $section->addText((string) $row[$col['name']]);
                    }
                }
                $section->addTextBreak(1);
            }
        }

        if ($showLegalDisclaimer) {
            $section->addTextBreak(1);
            $section->addText(
                'This document is generated automatically and may not constitute legal advice. Users should consult a qualified attorney before relying on this document.',
                ['italic' => true, 'size' => 9, 'color' => '92400E']
            );
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'invoxaco_docx_');
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        $contents = file_get_contents($tempFile);
        unlink($tempFile);

        return $contents;
    }
}
