<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    public static function fromHtml(string $html, string $filename = 'document.pdf', bool $stream = true): string
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultPaperSize', 'A4');

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();

        if ($stream) {
            $dompdf->stream($filename, ['Attachment' => true]);
            exit;
        }

        return $dompdf->output();
    }
}
