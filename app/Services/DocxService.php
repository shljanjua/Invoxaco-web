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
    public static function fromDocument(string $title, array $fields, array $data, ?string $logoPath = null): string
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
            if ($field['type'] === 'line_items') {
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

        $items = $data['items'] ?? null;
        if (is_array($items) && !empty($items)) {
            $table = $section->addTable(['borderSize' => 6, 'borderColor' => 'CCCCCC', 'cellMargin' => 80]);
            $table->addRow();
            foreach (['Description', 'Qty', 'Price', 'Total'] as $heading) {
                $table->addCell(2500)->addText($heading, ['bold' => true]);
            }

            $grandTotal = 0;
            foreach ($items as $item) {
                $qty = (float) ($item['qty'] ?? 1);
                $price = (float) ($item['price'] ?? 0);
                $lineTotal = $qty * $price;
                $grandTotal += $lineTotal;

                $table->addRow();
                $table->addCell(2500)->addText((string) ($item['description'] ?? ''));
                $table->addCell(2500)->addText((string) $qty);
                $table->addCell(2500)->addText(number_format($price, 2));
                $table->addCell(2500)->addText(number_format($lineTotal, 2));
            }

            $section->addTextBreak(1);
            $section->addText('Total: $' . number_format($grandTotal, 2), ['bold' => true, 'size' => 12]);
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'invoxaco_docx_');
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        $contents = file_get_contents($tempFile);
        unlink($tempFile);

        return $contents;
    }
}
