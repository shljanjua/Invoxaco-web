<?php
/** @var array $template */
/** @var array $fields */
/** @var array $data */
/** @var array $document */
/** @var array $user */
/** @var array|null $client */
/** @var bool $watermark */
/** @var bool $forBrowser */

$documentNumberFields = ['invoice_number', 'receipt_number', 'quote_number', 'po_number'];
$primaryDateFields = ['invoice_date', 'receipt_date', 'quote_date', 'po_date', 'agreement_date', 'contract_date', 'letter_date', 'proposal_date'];

$documentNumber = null;
foreach ($documentNumberFields as $nf) {
    if (!empty($data[$nf])) { $documentNumber = $data[$nf]; break; }
}

$primaryDateField = null;
$primaryDate = null;
foreach ($primaryDateFields as $df) {
    if (!empty($data[$df])) { $primaryDateField = $df; $primaryDate = $data[$df]; break; }
}

$structuredFieldNames = [];
$partyFields = [];
$signatureFields = [];
foreach ($fields as $f) {
    $ftype = $f['type'] ?? '';
    if (in_array($ftype, ['line_items', 'group_list'], true)) {
        $structuredFieldNames[] = $f['name'];
    } elseif ($ftype === 'party') {
        $structuredFieldNames[] = $f['name'];
        $partyFields[] = $f;
    } elseif ($ftype === 'signature') {
        $structuredFieldNames[] = $f['name'];
        $signatureFields[] = $f;
    }
}

$skipInHeader = array_merge($documentNumberFields, $structuredFieldNames, ['from_name', 'to_name', 'ship_to', 'notes'], $primaryDateField ? [$primaryDateField] : []);

$accent = preg_match('/^#[0-9a-fA-F]{6}$/', (string) ($document['accent_color'] ?? '')) ? $document['accent_color'] : '#2563eb';
$styleWhitelist = ['modern', 'classic', 'minimal', 'bold', 'corporate', 'construction', 'freelancer', 'consulting'];
$style = in_array($document['template_style'] ?? '', $styleWhitelist, true) ? $document['template_style'] : 'modern';
$showLogo = $document['show_logo'] ?? 1;
$currency = $user['currency'] ?? 'USD';

$logoFile = $showLogo ? ($user['company_logo'] ?? null) : null;
$signatureFile = $user['signature_path'] ?? null;
$logoExists = $logoFile && file_exists(__DIR__ . '/../../../public/uploads/logos/' . $logoFile);
$stampFile = $user['company_stamp_path'] ?? null;
$stampExists = $stampFile && file_exists(__DIR__ . '/../../../public/uploads/stamps/' . $stampFile);

$fontFamily = $style === 'classic' ? "'Times New Roman', Georgia, serif" : 'Arial, Helvetica, sans-serif';
if ($style === 'corporate') {
    $fontFamily = "Georgia, 'Times New Roman', serif";
}

// Per-document typography & colour controls (override the style defaults).
$fontMap = [
    'sans' => 'Arial, Helvetica, sans-serif',
    'serif' => "'Times New Roman', Georgia, serif",
    'modern' => "'DejaVu Sans', Arial, sans-serif",
    'classic' => "Georgia, 'Times New Roman', serif",
    'mono' => "'Courier New', Courier, monospace",
];
$docFont = $document['font_family'] ?? '';
if (isset($fontMap[$docFont])) {
    $fontFamily = $fontMap[$docFont];
}
$scaleMap = ['compact' => 12, 'normal' => 13, 'large' => 15];
$baseFont = $scaleMap[$document['font_scale'] ?? 'normal'] ?? 13;
$headingColor = preg_match('/^#[0-9a-fA-F]{6}$/', (string) ($document['heading_color'] ?? '')) ? $document['heading_color'] : '#111827';
$bodyColor = preg_match('/^#[0-9a-fA-F]{6}$/', (string) ($document['body_color'] ?? '')) ? $document['body_color'] : '#1f2937';

// Helper to render a chart field's stored data into inline SVG for the PDF.
$renderDocChart = static function ($chartData, string $accent): string {
    if (!is_array($chartData) || empty($chartData['rows'])) {
        return '';
    }
    return \App\Services\ChartSvgService::render($chartData, $accent);
};

$displayTitle = doc_title($template['name']);
$showLegalDisclaimer = \App\Services\GeneratorEngine::isLegalAgreement($template, $fields);

$senderName = trim((string) ($user['company_name'] ?? ''));

if (!function_exists('invoxaco_hex_to_rgba')) {
function invoxaco_hex_to_rgba(string $hex, float $alpha): string
{
    $hex = ltrim($hex, '#');
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    return "rgba($r, $g, $b, $alpha)";
}
}

if (!function_exists('invoxaco_doc_asset')) {
function invoxaco_doc_asset(string $relative, bool $forBrowser): string
{
    // For PDF rendering (Dompdf) use a local filesystem path so the
    // single-process dev/prod server never has to HTTP-fetch its own
    // upload, which can deadlock a single-threaded server.
    if ($forBrowser) {
        return url($relative);
    }
    $real = realpath(__DIR__ . '/../../../public/' . $relative);

    return $real !== false ? $real : '';
}
}
?>
<?php if ($forBrowser): ?><!DOCTYPE html><html><head><meta charset="UTF-8"><title><?= e($document['title']) ?></title>
<style>
@media print { .no-print { display: none !important; } }
</style>
</head><body>
<div class="no-print" style="background:#1f2a44;padding:10px 20px;color:#fff;display:flex;justify-content:space-between;align-items:center;font-family:Arial;">
  <span>Invoxaco Document Preview</span>
  <button onclick="window.print()" style="background:<?= e($accent) ?>;color:#fff;border:0;padding:8px 16px;border-radius:6px;">Print / Save as PDF</button>
</div>
<?php endif; ?>

<div style="font-family: <?= $fontFamily ?>; font-size:<?= (int) $baseFont ?>px; color:<?= e($bodyColor) ?>; padding:0; max-width:800px; margin:0 auto; position:relative;">

<?php if ($watermark): ?>
<div style="position:fixed; top:40%; left:10%; transform:rotate(-30deg); font-size:80px; color:rgba(0,0,0,0.08); font-weight:bold; z-index:0;">INVOXACO FREE</div>
<?php endif; ?>

<?php if ($style === 'bold'): ?>
<table style="width:100%; background:<?= e($accent) ?>; margin-bottom:30px;">
<tr>
<td style="vertical-align:top; padding:24px 30px;">
<?php if ($logoExists): ?>
<img src="<?= invoxaco_doc_asset('uploads/logos/' . $logoFile, $forBrowser) ?>" style="max-height:50px; margin-bottom:8px; background:#fff; padding:4px; border-radius:4px;">
<?php endif; ?>
<?php if ($senderName !== ''): ?><div style="font-size:18px; font-weight:bold; color:#fff;"><?= e($senderName) ?></div><?php endif; ?>
</td>
<td style="vertical-align:top; text-align:right; padding:24px 30px;">
<div style="font-size:24px; font-weight:bold; text-transform:uppercase; color:#fff;"><?= e($displayTitle) ?></div>
<?php if ($documentNumber): ?><div style="font-size:13px; color:rgba(255,255,255,0.85);">#<?= e($documentNumber) ?></div><?php endif; ?>
<?php if ($primaryDate): ?><div style="font-size:13px; color:rgba(255,255,255,0.85);"><?= e(date('F j, Y', strtotime($primaryDate))) ?></div><?php endif; ?>
</td>
</tr>
</table>
<div style="padding:0 30px;">

<?php elseif ($style === 'minimal'): ?>
<div style="padding:30px;">
<table style="width:100%; margin-bottom:14px;">
<tr>
<td style="vertical-align:top;">
<?php if ($logoExists): ?>
<img src="<?= invoxaco_doc_asset('uploads/logos/' . $logoFile, $forBrowser) ?>" style="max-height:50px; margin-bottom:8px;">
<?php endif; ?>
<?php if ($senderName !== ''): ?><div style="font-size:16px; font-weight:bold; color:#1f2937;"><?= e($senderName) ?></div><?php endif; ?>
</td>
<td style="vertical-align:top; text-align:right;">
<div style="font-size:18px; font-weight:600; text-transform:uppercase; letter-spacing:1px; color:#1f2937;"><?= e($displayTitle) ?></div>
<?php if ($documentNumber): ?><div style="font-size:12px; color:#9ca3af;">#<?= e($documentNumber) ?></div><?php endif; ?>
<?php if ($primaryDate): ?><div style="font-size:12px; color:#9ca3af;"><?= e(date('F j, Y', strtotime($primaryDate))) ?></div><?php endif; ?>
</td>
</tr>
</table>
<div style="border-bottom:2px solid <?= e($accent) ?>; margin-bottom:24px;"></div>

<?php elseif ($style === 'classic'): ?>
<div style="padding:30px;">
<div style="text-align:center; border-bottom:3px double #1f2937; padding-bottom:14px; margin-bottom:24px;">
<?php if ($logoExists): ?>
<img src="<?= invoxaco_doc_asset('uploads/logos/' . $logoFile, $forBrowser) ?>" style="max-height:55px; margin-bottom:8px;"><br>
<?php endif; ?>
<?php if ($senderName !== ''): ?><div style="font-size:20px; font-weight:bold; letter-spacing:0.5px;"><?= e($senderName) ?></div><?php endif; ?>
<div style="font-size:16px; text-transform:uppercase; letter-spacing:2px; color:<?= e($accent) ?>; margin-top:6px;"><?= e($displayTitle) ?></div>
<?php if ($documentNumber || $primaryDate): ?>
<div style="font-size:12px; color:#6b7280; margin-top:4px;">
<?= $documentNumber ? '#' . e($documentNumber) : '' ?><?= ($documentNumber && $primaryDate) ? ' &middot; ' : '' ?><?= $primaryDate ? e(date('F j, Y', strtotime($primaryDate))) : '' ?>
</div>
<?php endif; ?>
</div>

<?php elseif ($style === 'corporate'): ?>
<table style="width:100%; margin-bottom:0;">
<tr>
<td style="width:8px; background:<?= e($accent) ?>;">&nbsp;</td>
<td style="padding:26px 30px; background:#f8f7f4;">
<table style="width:100%;">
<tr>
<td style="vertical-align:top;">
<?php if ($logoExists): ?>
<img src="<?= invoxaco_doc_asset('uploads/logos/' . $logoFile, $forBrowser) ?>" style="max-height:55px; margin-bottom:8px;">
<?php endif; ?>
<?php if ($senderName !== ''): ?><div style="font-size:18px; font-weight:bold; color:#1f2937; letter-spacing:0.3px;"><?= e($senderName) ?></div><?php endif; ?>
</td>
<td style="vertical-align:top; text-align:right;">
<div style="font-size:21px; font-weight:bold; letter-spacing:1px; color:#1f2937;"><?= e(mb_strtoupper($displayTitle)) ?></div>
<?php if ($documentNumber): ?><div style="font-size:12px; color:<?= e($accent) ?>; margin-top:4px;">#<?= e($documentNumber) ?></div><?php endif; ?>
<?php if ($primaryDate): ?><div style="font-size:12px; color:#6b7280;"><?= e(date('F j, Y', strtotime($primaryDate))) ?></div><?php endif; ?>
</td>
</tr>
</table>
</td>
</tr>
</table>
<div style="border-bottom:1px solid #d6d3cb; margin-bottom:4px;"></div>
<div style="border-bottom:1px solid #d6d3cb; margin-bottom:24px;"></div>
<div style="padding:0 30px 30px;">

<?php elseif ($style === 'construction'): ?>
<table style="width:100%; background:#1f2937; margin-bottom:0;">
<tr>
<td style="vertical-align:top; padding:22px 30px;">
<?php if ($logoExists): ?>
<img src="<?= invoxaco_doc_asset('uploads/logos/' . $logoFile, $forBrowser) ?>" style="max-height:50px; margin-bottom:8px; background:#fff; padding:4px;">
<?php endif; ?>
<?php if ($senderName !== ''): ?><div style="font-size:17px; font-weight:bold; color:#fff; text-transform:uppercase;"><?= e($senderName) ?></div><?php endif; ?>
</td>
<td style="vertical-align:top; text-align:right; padding:22px 30px;">
<div style="font-size:23px; font-weight:bold; text-transform:uppercase; letter-spacing:1px; color:#fbbf24;"><?= e($displayTitle) ?></div>
<?php if ($documentNumber): ?><div style="font-size:13px; color:#e5e7eb;">#<?= e($documentNumber) ?></div><?php endif; ?>
<?php if ($primaryDate): ?><div style="font-size:13px; color:#e5e7eb;"><?= e(date('F j, Y', strtotime($primaryDate))) ?></div><?php endif; ?>
</td>
</tr>
</table>
<div style="height:6px; background:<?= e($accent) ?>;"></div>
<div style="padding:30px;">

<?php elseif ($style === 'freelancer'): ?>
<div style="padding:30px;">
<table style="width:100%; margin-bottom:24px; background:<?= invoxaco_hex_to_rgba($accent, 0.07) ?>; border-radius:14px;">
<tr>
<td style="vertical-align:top; padding:20px 24px;">
<?php if ($logoExists): ?>
<img src="<?= invoxaco_doc_asset('uploads/logos/' . $logoFile, $forBrowser) ?>" style="max-height:48px; margin-bottom:8px; border-radius:8px;">
<?php endif; ?>
<?php if ($senderName !== ''): ?><div style="font-size:17px; font-weight:bold; color:#1f2937;"><?= e($senderName) ?></div><?php endif; ?>
</td>
<td style="vertical-align:top; text-align:right; padding:20px 24px;">
<div style="font-size:20px; font-weight:bold; color:<?= e($accent) ?>;"><?= e($displayTitle) ?></div>
<?php if ($documentNumber): ?><div style="font-size:12px; color:#6b7280;">#<?= e($documentNumber) ?></div><?php endif; ?>
<?php if ($primaryDate): ?><div style="font-size:12px; color:#6b7280;"><?= e(date('F j, Y', strtotime($primaryDate))) ?></div><?php endif; ?>
</td>
</tr>
</table>

<?php elseif ($style === 'consulting'): ?>
<div style="padding:36px 30px;">
<table style="width:100%; margin-bottom:6px;">
<tr>
<td style="vertical-align:top;">
<?php if ($logoExists): ?>
<img src="<?= invoxaco_doc_asset('uploads/logos/' . $logoFile, $forBrowser) ?>" style="max-height:42px; margin-bottom:10px;">
<?php endif; ?>
<?php if ($senderName !== ''): ?><div style="font-size:15px; font-weight:600; color:#1f2937; letter-spacing:0.5px;"><?= e($senderName) ?></div><?php endif; ?>
</td>
<td style="vertical-align:top; text-align:right;">
<div style="font-size:13px; font-weight:600; text-transform:uppercase; letter-spacing:3px; color:#9ca3af;"><?= e($displayTitle) ?></div>
<?php if ($documentNumber): ?><div style="font-size:12px; color:#6b7280; margin-top:6px;">#<?= e($documentNumber) ?></div><?php endif; ?>
<?php if ($primaryDate): ?><div style="font-size:12px; color:#6b7280;"><?= e(date('F j, Y', strtotime($primaryDate))) ?></div><?php endif; ?>
</td>
</tr>
</table>
<div style="border-bottom:1px solid <?= e($accent) ?>; margin-bottom:30px;"></div>

<?php else: ?>
<div style="padding:30px;">
<table style="width:100%; margin-bottom:24px; border-bottom:4px solid <?= e($accent) ?>; padding-bottom:18px;">
<tr>
<td style="vertical-align:top;">
<?php if ($logoExists): ?>
<img src="<?= invoxaco_doc_asset('uploads/logos/' . $logoFile, $forBrowser) ?>" style="max-height:60px; margin-bottom:8px;">
<?php endif; ?>
<?php if ($senderName !== ''): ?><div style="font-size:18px; font-weight:bold; color:#1f2a44;"><?= e($senderName) ?></div><?php endif; ?>
</td>
<td style="vertical-align:top; text-align:right;">
<div style="font-size:22px; font-weight:bold; text-transform:uppercase; color:<?= e($accent) ?>;"><?= e($displayTitle) ?></div>
<?php if ($documentNumber): ?><div style="font-size:13px; color:#6b7280;">#<?= e($documentNumber) ?></div><?php endif; ?>
<?php if ($primaryDate): ?><div style="font-size:13px; color:#6b7280;"><?= e(date('F j, Y', strtotime($primaryDate))) ?></div><?php endif; ?>
</td>
</tr>
</table>
<?php endif; ?>

<?php $fromIsParty = in_array('from_name', $structuredFieldNames, true); $toIsParty = in_array('to_name', $structuredFieldNames, true); ?>
<?php if ((!$fromIsParty && !empty($data['from_name'])) || (!$toIsParty && !empty($data['to_name']))): ?>
<table style="width:100%; margin-bottom:24px;">
<tr>
<?php if (!$fromIsParty && !empty($data['from_name'])): ?>
<td style="vertical-align:top; width:50%;">
<div style="font-size:11px; color:#9ca3af; text-transform:uppercase; margin-bottom:4px;">From</div>
<div style="font-size:13px; white-space:pre-line;"><?= e($data['from_name']) ?></div>
</td>
<?php endif; ?>
<?php if (!$toIsParty && !empty($data['to_name'])): ?>
<td style="vertical-align:top; width:50%;">
<div style="font-size:11px; color:#9ca3af; text-transform:uppercase; margin-bottom:4px;">To</div>
<div style="font-size:13px; white-space:pre-line;"><?= e($data['to_name']) ?></div>
</td>
<?php endif; ?>
</tr>
</table>
<?php endif; ?>

<?php if (!empty($partyFields)): ?>
<table style="width:100%; margin-bottom:24px;">
<tr>
<?php foreach ($partyFields as $pf): ?>
  <?php $party = $data[$pf['name']] ?? []; if (!is_array($party)) { $party = []; } ?>
  <td style="vertical-align:top; width:<?= (int) floor(100 / count($partyFields)) ?>%; padding-right:14px;">
  <div style="font-size:11px; color:#9ca3af; text-transform:uppercase; margin-bottom:4px;"><?= e($pf['label']) ?></div>
  <?php if (!empty($party['full_name'])): ?><div style="font-size:14px; font-weight:bold; color:#1f2937;"><?= e($party['full_name']) ?></div><?php endif; ?>
  <?php if (!empty($party['id_no'])): ?><div style="font-size:12px; color:#374151;">ID/Reg No: <?= e($party['id_no']) ?></div><?php endif; ?>
  <?php if (!empty($party['address'])): ?><div style="font-size:12px; color:#374151; white-space:pre-line; margin-top:2px;"><?= e($party['address']) ?></div><?php endif; ?>
  <?php if (!empty($party['phone'])): ?><div style="font-size:12px; color:#374151; margin-top:2px;"><?= e($party['phone']) ?></div><?php endif; ?>
  <?php if (!empty($party['email'])): ?><div style="font-size:12px; color:#374151;"><?= e($party['email']) ?></div><?php endif; ?>
  </td>
<?php endforeach; ?>
</tr>
</table>
<?php endif; ?>

<?php foreach ($fields as $field): ?>
  <?php $ftype = $field['type'] ?? 'text'; ?>
  <?php if (in_array($field['name'], $skipInHeader, true)) continue; ?>
  <?php $value = $data[$field['name']] ?? ''; ?>

  <?php if ($ftype === 'image'): ?>
    <?php $imgFile = is_string($value) ? trim($value) : ''; if ($imgFile === '' || !file_exists(__DIR__ . '/../../../public/uploads/doc-images/' . $imgFile)) continue; ?>
    <div style="margin-bottom:18px;">
      <div style="font-size:11px; color:#9ca3af; text-transform:uppercase; margin-bottom:6px;"><?= e($field['label']) ?></div>
      <img src="<?= invoxaco_doc_asset('uploads/doc-images/' . $imgFile, $forBrowser) ?>" style="max-width:100%; max-height:340px; border-radius:6px;">
    </div>

  <?php elseif ($ftype === 'gallery'): ?>
    <?php
    $gal = [];
    if (is_string($value) && $value !== '') { $d = json_decode($value, true); $gal = is_array($d) ? $d : []; }
    $gal = array_values(array_filter($gal, fn ($g) => is_string($g) && file_exists(__DIR__ . '/../../../public/uploads/doc-images/' . $g)));
    if (empty($gal)) continue;
    ?>
    <div style="margin-bottom:18px;">
      <div style="font-size:11px; color:#9ca3af; text-transform:uppercase; margin-bottom:6px;"><?= e($field['label']) ?></div>
      <table style="width:100%; border-collapse:collapse;"><tr>
      <?php foreach ($gal as $i => $g): ?>
        <td style="width:<?= (int) floor(100 / min(count($gal), 3)) ?>%; padding:3px; vertical-align:top;">
          <img src="<?= invoxaco_doc_asset('uploads/doc-images/' . $g, $forBrowser) ?>" style="width:100%; border-radius:6px;">
        </td>
        <?php if (($i + 1) % 3 === 0 && $i + 1 < count($gal)): ?></tr><tr><?php endif; ?>
      <?php endforeach; ?>
      </tr></table>
    </div>

  <?php elseif ($ftype === 'chart'): ?>
    <?php $svg = $renderDocChart($value, $accent); if ($svg === '') continue; ?>
    <div style="margin-bottom:20px;">
      <?php $ct = is_array($value) ? trim((string) ($value['title'] ?? '')) : ''; ?>
      <div style="font-size:13px; font-weight:bold; color:<?= e($headingColor) ?>; margin-bottom:6px;"><?= e($ct !== '' ? $ct : $field['label']) ?></div>
      <div style="width:100%; max-width:520px;"><?= $svg ?></div>
    </div>

  <?php elseif ($ftype === 'table'): ?>
    <?php $rows = is_array($value) ? $value : []; if (empty($rows)) continue; $cols = $field['columns'] ?? []; ?>
    <div style="margin-bottom:6px; font-size:11px; color:#9ca3af; text-transform:uppercase;"><?= e($field['label']) ?></div>
    <table style="width:100%; border-collapse:collapse; margin:0 0 20px;">
      <thead><tr style="background:<?= e($accent) ?>;">
        <?php foreach ($cols as $c): ?><th style="text-align:left; padding:8px 10px; font-size:12px; color:#fff;"><?= e($c['label']) ?></th><?php endforeach; ?>
      </tr></thead>
      <tbody>
      <?php foreach ($rows as $ri => $row): ?>
        <tr style="background:<?= $ri % 2 ? '#f9fafb' : '#fff' ?>;">
          <?php foreach ($cols as $c): ?><td style="padding:8px 10px; font-size:12px; border-bottom:1px solid #eef0f3;"><?= e((string) ($row[$c['name']] ?? '')) ?></td><?php endforeach; ?>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>

  <?php else: ?>
    <?php if ($value === '' || $value === null || is_array($value)) continue; ?>
    <?php if ($ftype === 'date' && strtotime((string) $value) !== false) { $value = date('F j, Y', strtotime((string) $value)); } ?>
    <div style="margin-bottom:16px;">
      <div style="font-size:11px; color:#9ca3af; text-transform:uppercase; margin-bottom:4px;"><?= e($field['label']) ?></div>
      <div style="white-space:pre-line; color:<?= e($bodyColor) ?>;"><?= e((string) $value) ?></div>
    </div>
  <?php endif; ?>
<?php endforeach; ?>

<?php foreach ($fields as $field): ?>
  <?php if (($field['type'] ?? '') !== 'line_items') continue; ?>
  <?php $name = $field['name']; $rows = $data[$name] ?? []; if (empty($rows) || !is_array($rows)) continue; ?>
  <?php $isPrimary = $name === 'items'; ?>
  <?php $hasUnit = !empty($field['has_unit']); ?>
  <?php $hasItemDiscount = !empty($field['per_item_discount']); ?>
  <?php $hasItemTax = !empty($field['per_item_tax']); ?>
  <div style="margin-bottom:6px; font-size:11px; color:#9ca3af; text-transform:uppercase;"><?= e($field['label']) ?></div>
  <table style="width:100%; border-collapse:collapse; margin:0 0 20px;">
  <thead>
  <tr style="background:<?= e($accent) ?>;">
  <th style="text-align:left; padding:10px; font-size:12px; color:#fff;">Description</th>
  <?php if ($hasUnit): ?><th style="text-align:right; padding:10px; font-size:12px; color:#fff;">Unit</th><?php endif; ?>
  <th style="text-align:right; padding:10px; font-size:12px; color:#fff;">Qty</th>
  <th style="text-align:right; padding:10px; font-size:12px; color:#fff;">Price</th>
  <?php if ($hasItemDiscount): ?><th style="text-align:right; padding:10px; font-size:12px; color:#fff;">Disc %</th><?php endif; ?>
  <?php if ($hasItemTax): ?><th style="text-align:right; padding:10px; font-size:12px; color:#fff;">Tax %</th><?php endif; ?>
  <th style="text-align:right; padding:10px; font-size:12px; color:#fff;">Total</th>
  </tr>
  </thead>
  <tbody>
  <?php foreach ($rows as $item): ?>
  <tr>
  <td style="padding:8px; font-size:13px; border-bottom:1px solid #f3f4f6;"><?= e($item['description'] ?? '') ?></td>
  <?php if ($hasUnit): ?><td style="padding:8px; font-size:13px; text-align:right; border-bottom:1px solid #f3f4f6;"><?= e((string) ($item['unit'] ?? '')) ?></td><?php endif; ?>
  <td style="padding:8px; font-size:13px; text-align:right; border-bottom:1px solid #f3f4f6;"><?= e((string) ($item['qty'] ?? '')) ?></td>
  <td style="padding:8px; font-size:13px; text-align:right; border-bottom:1px solid #f3f4f6;"><?= money((float) ($item['price'] ?? 0), $currency) ?></td>
  <?php if ($hasItemDiscount): ?><td style="padding:8px; font-size:13px; text-align:right; border-bottom:1px solid #f3f4f6;"><?= e((string) ($item['discount_percent'] ?? 0)) ?>%</td><?php endif; ?>
  <?php if ($hasItemTax): ?><td style="padding:8px; font-size:13px; text-align:right; border-bottom:1px solid #f3f4f6;"><?= e((string) ($item['tax_percent'] ?? 0)) ?>%</td><?php endif; ?>
  <td style="padding:8px; font-size:13px; text-align:right; border-bottom:1px solid #f3f4f6;"><?= money((float) ($item['total'] ?? 0), $currency) ?></td>
  </tr>
  <?php endforeach; ?>
  </tbody>
  </table>
  <?php
  $showGrandTotal = $isPrimary && (
      !empty($data['tax_rate'])
      || !empty($data['document_discount_amount'])
      || !empty($data['extra_charges_total'])
      || !empty($data['item_discount_total'])
  );
  ?>
  <table style="width:100%; margin-bottom:20px;">
  <tr><td style="text-align:right; padding:4px; font-size:13px; width:80%;">Subtotal</td><td style="text-align:right; padding:4px; font-size:13px;"><?= money((float) ($data[$isPrimary ? 'subtotal' : $name . '_subtotal'] ?? 0), $currency) ?></td></tr>
  <?php if ($isPrimary && !empty($data['item_discount_total'])): ?>
  <tr><td style="text-align:right; padding:4px; font-size:13px;">Line Item Discounts</td><td style="text-align:right; padding:4px; font-size:13px;">-<?= money((float) $data['item_discount_total'], $currency) ?></td></tr>
  <?php endif; ?>
  <?php if ($isPrimary && !empty($data['tax_rate'])): ?>
  <tr><td style="text-align:right; padding:4px; font-size:13px;">Tax (<?= e((string) $data['tax_rate']) ?>%)</td><td style="text-align:right; padding:4px; font-size:13px;"><?= money((float) ($data['tax_amount'] ?? 0), $currency) ?></td></tr>
  <?php endif; ?>
  <?php if ($isPrimary && !empty($data['document_discount_amount'])): ?>
  <tr><td style="text-align:right; padding:4px; font-size:13px;">Discount</td><td style="text-align:right; padding:4px; font-size:13px;">-<?= money((float) $data['document_discount_amount'], $currency) ?></td></tr>
  <?php endif; ?>
  <?php if ($isPrimary && !empty($data['extra_charges_total'])): ?>
  <tr><td style="text-align:right; padding:4px; font-size:13px;">Extra Charges</td><td style="text-align:right; padding:4px; font-size:13px;"><?= money((float) $data['extra_charges_total'], $currency) ?></td></tr>
  <?php endif; ?>
  <?php if ($showGrandTotal): ?>
  <tr><td style="text-align:right; padding:10px; font-size:16px; font-weight:bold; border-top:2px solid <?= e($accent) ?>;">Total</td><td style="text-align:right; padding:10px; font-size:16px; font-weight:bold; border-top:2px solid <?= e($accent) ?>; color:<?= e($accent) ?>;"><?= money((float) ($data['grand_total'] ?? 0), $currency) ?></td></tr>
  <?php endif; ?>
  </table>
<?php endforeach; ?>

<?php foreach ($fields as $field): ?>
  <?php if (($field['type'] ?? '') !== 'group_list') continue; ?>
  <?php $rows = $data[$field['name']] ?? []; if (empty($rows) || !is_array($rows)) continue; ?>
  <?php $columns = $field['columns'] ?? []; ?>
  <div style="margin-bottom:10px; font-size:11px; color:#9ca3af; text-transform:uppercase;"><?= e($field['label']) ?></div>
  <?php foreach ($rows as $row): ?>
    <?php $cols = array_values($columns); $titleCol = $cols[0] ?? null; $subCol = $cols[1] ?? null; ?>
    <div style="margin-bottom:14px; padding-bottom:12px; border-bottom:1px solid #f3f4f6;">
      <?php if ($titleCol && ($row[$titleCol['name']] ?? '') !== ''): ?>
      <div style="font-size:14px; font-weight:bold; color:#1f2937;"><?= e($row[$titleCol['name']]) ?></div>
      <?php endif; ?>
      <?php if ($subCol && ($row[$subCol['name']] ?? '') !== ''): ?>
      <div style="font-size:12px; color:<?= e($accent) ?>; margin-bottom:4px;"><?= e($row[$subCol['name']]) ?></div>
      <?php endif; ?>
      <?php foreach (array_slice($cols, 2) as $col): ?>
        <?php if (($row[$col['name']] ?? '') === '') continue; ?>
        <div style="font-size:12px; color:#374151; white-space:pre-line; margin-top:2px;"><?= e($row[$col['name']]) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
<?php endforeach; ?>

<?php $closingNote = trim((string) ($data['notes'] ?? '')); ?>
<?php if ($closingNote !== ''): ?>
<div style="margin-top:30px; padding:14px 16px; background:#f9fafb; border-left:3px solid <?= e($accent) ?>; border-radius:4px;">
<div style="font-size:13px; color:#374151; white-space:pre-line;"><?= e($closingNote) ?></div>
</div>
<?php endif; ?>

<?php if (!empty($signatureFields)): ?>
<table style="width:100%; margin-top:50px;">
<tr>
<?php foreach ($signatureFields as $sf): ?>
  <?php
  $sig = $data[$sf['name']] ?? [];
  if (!is_array($sig)) { $sig = []; }
  $sigImg = null;
  if (!empty($sig['use_company_stamp']) && $stampExists) {
      $sigImg = invoxaco_doc_asset('uploads/stamps/' . $stampFile, $forBrowser);
  } elseif (!empty($sig['path']) && file_exists(__DIR__ . '/../../../public/uploads/signatures/' . $sig['path'])) {
      $sigImg = invoxaco_doc_asset('uploads/signatures/' . $sig['path'], $forBrowser);
  }
  ?>
  <td style="vertical-align:bottom; width:<?= (int) floor(100 / count($signatureFields)) ?>%; padding-right:14px;">
  <?php if ($sigImg): ?><img src="<?= $sigImg ?>" style="max-height:60px; margin-bottom:4px;"><?php else: ?><div style="height:60px;"></div><?php endif; ?>
  <div style="border-top:1px solid #9ca3af; padding-top:4px; font-size:12px; color:#374151;">
  <?= e($sf['label']) ?>
  <?php if (!empty($sig['id_no'])): ?><br><span style="font-size:11px; color:#9ca3af;">ID/Reg No: <?= e($sig['id_no']) ?></span><?php endif; ?>
  </div>
  </td>
<?php endforeach; ?>
</tr>
</table>
<?php elseif ($signatureFile && file_exists(__DIR__ . '/../../../public/uploads/signatures/' . $signatureFile)): ?>
<div style="margin-top:40px;">
<div style="font-size:11px; color:#9ca3af; text-transform:uppercase; margin-bottom:4px;">Authorized Signature</div>
<img src="<?= invoxaco_doc_asset('uploads/signatures/' . $signatureFile, $forBrowser) ?>" style="max-height:70px;">
<?php if (!empty($document['show_stamp']) && $stampExists): ?>
<img src="<?= invoxaco_doc_asset('uploads/stamps/' . $stampFile, $forBrowser) ?>" style="max-height:70px; margin-left:16px;">
<?php endif; ?>
</div>
<?php elseif (!empty($document['show_stamp']) && $stampExists): ?>
<div style="margin-top:40px;">
<img src="<?= invoxaco_doc_asset('uploads/stamps/' . $stampFile, $forBrowser) ?>" style="max-height:70px;">
</div>
<?php endif; ?>

<?php
$addressBits = array_filter([$user['address'] ?? null, $user['city'] ?? null, $user['state'] ?? null, $user['country'] ?? null]);
$footerBits = array_filter([
    !empty($addressBits) ? implode(', ', $addressBits) : null,
    $user['phone'] ?? null,
    $user['website'] ?? null,
    !empty($user['tax_number']) ? 'Tax No: ' . $user['tax_number'] : null,
    !empty($user['business_registration_number']) ? 'Reg No: ' . $user['business_registration_number'] : null,
]);
$bankBits = array_filter([
    !empty($user['bank_name']) ? $user['bank_name'] : null,
    !empty($user['bank_account_title']) ? $user['bank_account_title'] : null,
    !empty($user['bank_account_no']) ? 'A/C: ' . $user['bank_account_no'] : null,
    !empty($user['bank_swift_code']) ? 'SWIFT: ' . $user['bank_swift_code'] : null,
    !empty($user['bank_branch']) ? $user['bank_branch'] : null,
]);
?>
<?php if (!empty($footerBits) || !empty($bankBits)): ?>
<div style="margin-top:40px; padding-top:14px; border-top:1px solid #e5e7eb; font-size:11px; color:#6b7280;">
<?php if (!empty($footerBits)): ?><div><?= e(implode(' &middot; ', $footerBits)) ?></div><?php endif; ?>
<?php if (!empty($bankBits)): ?><div style="margin-top:4px;">Payment Details: <?= e(implode(' &middot; ', $bankBits)) ?></div><?php endif; ?>
</div>
<?php endif; ?>

<?php if ($showLegalDisclaimer): ?>
<div style="margin-top:24px; padding:12px 14px; background:#fffbeb; border:1px solid #fde68a; border-radius:4px; font-size:10px; color:#92400e; text-align:center;">
This document is generated automatically and may not constitute legal advice. Users should consult a qualified attorney before relying on this document.
</div>
<?php endif; ?>

<div style="margin-top:24px; padding-top:14px; border-top:1px solid #e5e7eb; font-size:11px; color:#9ca3af; text-align:center;">
Generated with Invoxaco &middot; <?= e(url()) ?>
</div>

</div>
</div>
<?php if ($forBrowser): ?></body></html><?php endif; ?>
