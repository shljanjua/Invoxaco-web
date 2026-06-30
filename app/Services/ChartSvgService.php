<?php

namespace App\Services;

/**
 * Renders simple, professional charts as self-contained SVG markup from
 * label/value data. SVG is used (instead of a JavaScript chart library)
 * because it renders identically in the browser preview AND inside the
 * Dompdf-generated PDF, which cannot run JavaScript.
 *
 * Supported types: bar, column, line, pie, donut.
 */
class ChartSvgService
{
    private const PALETTE = [
        '#2563eb', '#16a34a', '#f59e0b', '#dc2626', '#7c3aed',
        '#0891b2', '#db2777', '#65a30d', '#ea580c', '#4f46e5',
    ];

    /**
     * @param array  $chart  ['type'=>..,'title'=>..,'rows'=>[['label'=>..,'value'=>..],..]]
     * @param string $accent Accent color used as the primary series colour.
     */
    public static function render(array $chart, string $accent = '#2563eb'): string
    {
        $type = strtolower((string) ($chart['type'] ?? 'bar'));
        $rows = [];
        foreach (($chart['rows'] ?? []) as $r) {
            $label = trim((string) ($r['label'] ?? ''));
            $value = (float) ($r['value'] ?? 0);
            if ($label === '' && $value == 0.0) {
                continue;
            }
            $rows[] = ['label' => $label, 'value' => $value];
        }
        if (empty($rows)) {
            return '';
        }

        $palette = array_merge([$accent], self::PALETTE);

        $svg = match ($type) {
            'pie'   => self::pie($rows, $palette, false),
            'donut' => self::pie($rows, $palette, true),
            'line'  => self::line($rows, $accent),
            default => self::bars($rows, $accent),
        };

        return $svg;
    }

    private static function esc(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    }

    private static function fmt(float $v): string
    {
        if (floor($v) == $v) {
            return number_format($v, 0);
        }
        return number_format($v, 2);
    }

    private static function open(int $w, int $h): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $w . ' ' . $h . '" '
            . 'style="width:100%;max-width:' . $w . 'px;height:auto;font-family:Arial,Helvetica,sans-serif;">';
    }

    private static function bars(array $rows, string $accent): string
    {
        $w = 520; $h = 300; $padL = 44; $padB = 46; $padT = 16; $padR = 16;
        $plotW = $w - $padL - $padR; $plotH = $h - $padT - $padB;
        $max = max(array_map(fn ($r) => $r['value'], $rows));
        $max = $max <= 0 ? 1 : $max;
        $n = count($rows);
        $gap = 14;
        $bw = ($plotW - $gap * ($n - 1)) / $n;

        $s = self::open($w, $h);
        // gridlines + y labels (4 steps)
        for ($i = 0; $i <= 4; $i++) {
            $y = $padT + $plotH - ($plotH * $i / 4);
            $val = $max * $i / 4;
            $s .= '<line x1="' . $padL . '" y1="' . round($y, 1) . '" x2="' . ($w - $padR) . '" y2="' . round($y, 1) . '" stroke="#e5e7eb" stroke-width="1"/>';
            $s .= '<text x="' . ($padL - 6) . '" y="' . round($y + 3, 1) . '" font-size="10" fill="#9ca3af" text-anchor="end">' . self::esc(self::fmt($val)) . '</text>';
        }
        $x = $padL;
        foreach ($rows as $r) {
            $bh = $plotH * ($r['value'] / $max);
            $y = $padT + $plotH - $bh;
            $s .= '<rect x="' . round($x, 1) . '" y="' . round($y, 1) . '" width="' . round($bw, 1) . '" height="' . round($bh, 1) . '" rx="3" fill="' . self::esc($accent) . '"/>';
            $s .= '<text x="' . round($x + $bw / 2, 1) . '" y="' . round($y - 5, 1) . '" font-size="10" fill="#374151" text-anchor="middle">' . self::esc(self::fmt($r['value'])) . '</text>';
            $s .= '<text x="' . round($x + $bw / 2, 1) . '" y="' . ($h - $padB + 16) . '" font-size="10" fill="#6b7280" text-anchor="middle">' . self::esc(mb_substr($r['label'], 0, 14)) . '</text>';
            $x += $bw + $gap;
        }
        return $s . '</svg>';
    }

    private static function line(array $rows, string $accent): string
    {
        $w = 520; $h = 300; $padL = 44; $padB = 46; $padT = 16; $padR = 16;
        $plotW = $w - $padL - $padR; $plotH = $h - $padT - $padB;
        $max = max(array_map(fn ($r) => $r['value'], $rows));
        $max = $max <= 0 ? 1 : $max;
        $n = count($rows);
        $stepX = $n > 1 ? $plotW / ($n - 1) : 0;

        $s = self::open($w, $h);
        for ($i = 0; $i <= 4; $i++) {
            $y = $padT + $plotH - ($plotH * $i / 4);
            $val = $max * $i / 4;
            $s .= '<line x1="' . $padL . '" y1="' . round($y, 1) . '" x2="' . ($w - $padR) . '" y2="' . round($y, 1) . '" stroke="#e5e7eb" stroke-width="1"/>';
            $s .= '<text x="' . ($padL - 6) . '" y="' . round($y + 3, 1) . '" font-size="10" fill="#9ca3af" text-anchor="end">' . self::esc(self::fmt($val)) . '</text>';
        }
        $pts = [];
        $i = 0;
        foreach ($rows as $r) {
            $x = $padL + $stepX * $i;
            $y = $padT + $plotH - $plotH * ($r['value'] / $max);
            $pts[] = [round($x, 1), round($y, 1), $r];
            $i++;
        }
        // area + line
        $poly = implode(' ', array_map(fn ($p) => $p[0] . ',' . $p[1], $pts));
        $area = $padL . ',' . ($padT + $plotH) . ' ' . $poly . ' ' . ($padL + $stepX * ($n - 1)) . ',' . ($padT + $plotH);
        $s .= '<polygon points="' . $area . '" fill="' . self::esc($accent) . '" fill-opacity="0.10"/>';
        $s .= '<polyline points="' . $poly . '" fill="none" stroke="' . self::esc($accent) . '" stroke-width="2.5"/>';
        foreach ($pts as $p) {
            $s .= '<circle cx="' . $p[0] . '" cy="' . $p[1] . '" r="3.5" fill="#fff" stroke="' . self::esc($accent) . '" stroke-width="2"/>';
            $s .= '<text x="' . $p[0] . '" y="' . ($h - $padB + 16) . '" font-size="10" fill="#6b7280" text-anchor="middle">' . self::esc(mb_substr($p[2]['label'], 0, 12)) . '</text>';
        }
        return $s . '</svg>';
    }

    private static function pie(array $rows, array $palette, bool $donut): string
    {
        $w = 520; $h = 300; $cx = 150; $cy = 150; $r = 120;
        $total = array_sum(array_map(fn ($x) => max(0, $x['value']), $rows));
        if ($total <= 0) {
            return '';
        }
        $s = self::open($w, $h);
        $angle = -90; // start at top
        $i = 0;
        foreach ($rows as $row) {
            $val = max(0, $row['value']);
            $frac = $val / $total;
            $sweep = $frac * 360;
            $color = $palette[$i % count($palette)];
            if ($frac > 0) {
                if ($frac >= 0.9999) {
                    $s .= '<circle cx="' . $cx . '" cy="' . $cy . '" r="' . $r . '" fill="' . self::esc($color) . '"/>';
                } else {
                    $a1 = deg2rad($angle); $a2 = deg2rad($angle + $sweep);
                    $x1 = $cx + $r * cos($a1); $y1 = $cy + $r * sin($a1);
                    $x2 = $cx + $r * cos($a2); $y2 = $cy + $r * sin($a2);
                    $large = $sweep > 180 ? 1 : 0;
                    $s .= '<path d="M' . round($cx, 1) . ',' . round($cy, 1) . ' L' . round($x1, 1) . ',' . round($y1, 1)
                        . ' A' . $r . ',' . $r . ' 0 ' . $large . ',1 ' . round($x2, 1) . ',' . round($y2, 1) . ' Z" fill="' . self::esc($color) . '"/>';
                }
            }
            $angle += $sweep;
            // legend
            $ly = 40 + $i * 26;
            $s .= '<rect x="320" y="' . ($ly - 10) . '" width="14" height="14" rx="3" fill="' . self::esc($color) . '"/>';
            $pct = round($frac * 100);
            $s .= '<text x="342" y="' . ($ly + 1) . '" font-size="12" fill="#374151">' . self::esc(mb_substr($row['label'], 0, 18)) . ' — ' . $pct . '%</text>';
            $i++;
        }
        if ($donut) {
            $s .= '<circle cx="' . $cx . '" cy="' . $cy . '" r="' . round($r * 0.58) . '" fill="#ffffff"/>';
        }
        return $s . '</svg>';
    }
}
