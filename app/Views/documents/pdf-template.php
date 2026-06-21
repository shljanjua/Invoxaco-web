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
foreach ($fields as $f) {
    if (in_array($f['type'] ?? '', ['line_items', 'group_list'], true)) {
        $structuredFieldNames[] = $f['name'];
    }
}

$skipInHeader = array_merge($documentNumberFields, $structuredFieldNames, ['from_name', 'to_name', 'ship_to', 'notes'], $primaryDateField ? [$primaryDateField] : []);

$accent = preg_match('/^#[0-9a-fA-F]{6}$/', (string) ($document['accent_color'] ?? '')) ? $document['accent_color'] : '#2563eb';
$style = in_array($document['template_style'] ?? '', ['modern', 'classic', 'minimal', 'bold'], true) ? $document['template_style'] : 'modern';
$showLogo = $document['show_logo'] ?? 1;
$currency = $user['currency'] ?? 'USD';

$logoFile = $showLogo ? ($user['company_logo'] ?? null) : null;
$signatureFile = $user['signature_path'] ?? null;
$logoExists = $logoFile && file_exists(__DIR__ . '/../../../public/uploads/logos/' . $logoFile);

$fontFamily = $style === 'classic' ? "'Times New Roman', Georgia, serif" : 'Arial, Helvetica, sans-serif';

$displayTitle = doc_title($template['name']);

$senderName = trim((string) ($user['company_name'] ?? ''));
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

<div style="font-family: <?= $fontFamily ?>; color:#1f2937; padding:0; max-width:800px; margin:0 auto; position:relative;">

<?php if ($watermark): ?>
<div style="position:fixed; top:40%; left:10%; transform:rotate(-30deg); font-size:80px; color:rgba(0,0,0,0.08); font-weight:bold; z-index:0;">INVOXACO FREE</div>
<?php endif; ?>

<?php if ($style === 'bold'): ?>
<table style="width:100%; background:<?= e($accent) ?>; margin-bottom:30px;">
<tr>
<td style="vertical-align:top; padding:24px 30px;">
<?php if ($logoExists): ?>
<img src="<?= url('uploads/logos/' . $logoFile) ?>" style="max-height:50px; margin-bottom:8px; background:#fff; padding:4px; border-radius:4px;">
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
<img src="<?= url('uploads/logos/' . $logoFile) ?>" style="max-height:50px; margin-bottom:8px;">
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
<img src="<?= url('uploads/logos/' . $logoFile) ?>" style="max-height:55px; margin-bottom:8px;"><br>
<?php endif; ?>
<?php if ($senderName !== ''): ?><div style="font-size:20px; font-weight:bold; letter-spacing:0.5px;"><?= e($senderName) ?></div><?php endif; ?>
<div style="font-size:16px; text-transform:uppercase; letter-spacing:2px; color:<?= e($accent) ?>; margin-top:6px;"><?= e($displayTitle) ?></div>
<?php if ($documentNumber || $primaryDate): ?>
<div style="font-size:12px; color:#6b7280; margin-top:4px;">
<?= $documentNumber ? '#' . e($documentNumber) : '' ?><?= ($documentNumber && $primaryDate) ? ' &middot; ' : '' ?><?= $primaryDate ? e(date('F j, Y', strtotime($primaryDate))) : '' ?>
</div>
<?php endif; ?>
</div>

<?php else: ?>
<div style="padding:30px;">
<table style="width:100%; margin-bottom:24px; border-bottom:4px solid <?= e($accent) ?>; padding-bottom:18px;">
<tr>
<td style="vertical-align:top;">
<?php if ($logoExists): ?>
<img src="<?= url('uploads/logos/' . $logoFile) ?>" style="max-height:60px; margin-bottom:8px;">
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

<?php if (!empty($data['from_name']) || !empty($data['to_name'])): ?>
<table style="width:100%; margin-bottom:24px;">
<tr>
<?php if (!empty($data['from_name'])): ?>
<td style="vertical-align:top; width:50%;">
<div style="font-size:11px; color:#9ca3af; text-transform:uppercase; margin-bottom:4px;">From</div>
<div style="font-size:13px; white-space:pre-line;"><?= e($data['from_name']) ?></div>
</td>
<?php endif; ?>
<?php if (!empty($data['to_name'])): ?>
<td style="vertical-align:top; width:50%;">
<div style="font-size:11px; color:#9ca3af; text-transform:uppercase; margin-bottom:4px;">To</div>
<div style="font-size:13px; white-space:pre-line;"><?= e($data['to_name']) ?></div>
</td>
<?php endif; ?>
</tr>
</table>
<?php endif; ?>

<?php foreach ($fields as $field): ?>
  <?php if (in_array($field['name'], $skipInHeader, true)) continue; ?>
  <?php $value = $data[$field['name']] ?? ''; if ($value === '' || $value === null) continue; ?>
  <?php if (($field['type'] ?? '') === 'date' && strtotime((string) $value) !== false) { $value = date('F j, Y', strtotime((string) $value)); } ?>
  <div style="margin-bottom:16px;">
    <div style="font-size:11px; color:#9ca3af; text-transform:uppercase; margin-bottom:4px;"><?= e($field['label']) ?></div>
    <div style="font-size:13px; white-space:pre-line;"><?= e(is_array($value) ? implode(', ', $value) : (string) $value) ?></div>
  </div>
<?php endforeach; ?>

<?php foreach ($fields as $field): ?>
  <?php if (($field['type'] ?? '') !== 'line_items') continue; ?>
  <?php $name = $field['name']; $rows = $data[$name] ?? []; if (empty($rows) || !is_array($rows)) continue; ?>
  <?php $isPrimary = $name === 'items'; ?>
  <div style="margin-bottom:6px; font-size:11px; color:#9ca3af; text-transform:uppercase;"><?= e($field['label']) ?></div>
  <table style="width:100%; border-collapse:collapse; margin:0 0 20px;">
  <thead>
  <tr style="background:<?= e($accent) ?>;">
  <th style="text-align:left; padding:10px; font-size:12px; color:#fff;">Description</th>
  <th style="text-align:right; padding:10px; font-size:12px; color:#fff;">Qty</th>
  <th style="text-align:right; padding:10px; font-size:12px; color:#fff;">Price</th>
  <th style="text-align:right; padding:10px; font-size:12px; color:#fff;">Total</th>
  </tr>
  </thead>
  <tbody>
  <?php foreach ($rows as $item): ?>
  <tr>
  <td style="padding:8px; font-size:13px; border-bottom:1px solid #f3f4f6;"><?= e($item['description'] ?? '') ?></td>
  <td style="padding:8px; font-size:13px; text-align:right; border-bottom:1px solid #f3f4f6;"><?= e((string) ($item['qty'] ?? '')) ?></td>
  <td style="padding:8px; font-size:13px; text-align:right; border-bottom:1px solid #f3f4f6;"><?= money((float) ($item['price'] ?? 0), $currency) ?></td>
  <td style="padding:8px; font-size:13px; text-align:right; border-bottom:1px solid #f3f4f6;"><?= money((float) ($item['total'] ?? 0), $currency) ?></td>
  </tr>
  <?php endforeach; ?>
  </tbody>
  </table>
  <table style="width:100%; margin-bottom:20px;">
  <tr><td style="text-align:right; padding:4px; font-size:13px; width:80%;">Subtotal</td><td style="text-align:right; padding:4px; font-size:13px;"><?= money((float) ($data[$isPrimary ? 'subtotal' : $name . '_subtotal'] ?? 0), $currency) ?></td></tr>
  <?php if ($isPrimary && !empty($data['tax_rate'])): ?>
  <tr><td style="text-align:right; padding:4px; font-size:13px;">Tax (<?= e((string) $data['tax_rate']) ?>%)</td><td style="text-align:right; padding:4px; font-size:13px;"><?= money((float) ($data['tax_amount'] ?? 0), $currency) ?></td></tr>
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

<?php if ($signatureFile && file_exists(__DIR__ . '/../../../public/uploads/signatures/' . $signatureFile)): ?>
<div style="margin-top:40px;">
<div style="font-size:11px; color:#9ca3af; text-transform:uppercase; margin-bottom:4px;">Authorized Signature</div>
<img src="<?= url('uploads/signatures/' . $signatureFile) ?>" style="max-height:70px;">
</div>
<?php endif; ?>

<?php
$footerBits = array_filter([
    $user['address'] ?? null,
    $user['phone'] ?? null,
    $user['website'] ?? null,
    !empty($user['tax_number']) ? 'Tax No: ' . $user['tax_number'] : null,
]);
$bankBits = array_filter([
    !empty($user['bank_name']) ? $user['bank_name'] : null,
    !empty($user['bank_account_no']) ? 'A/C: ' . $user['bank_account_no'] : null,
]);
?>
<?php if (!empty($footerBits) || !empty($bankBits)): ?>
<div style="margin-top:40px; padding-top:14px; border-top:1px solid #e5e7eb; font-size:11px; color:#6b7280;">
<?php if (!empty($footerBits)): ?><div><?= e(implode(' &middot; ', $footerBits)) ?></div><?php endif; ?>
<?php if (!empty($bankBits)): ?><div style="margin-top:4px;">Payment Details: <?= e(implode(' &middot; ', $bankBits)) ?></div><?php endif; ?>
</div>
<?php endif; ?>

<div style="margin-top:24px; padding-top:14px; border-top:1px solid #e5e7eb; font-size:11px; color:#9ca3af; text-align:center;">
Generated with Invoxaco &middot; <?= e(url()) ?>
</div>

</div>
</div>
<?php if ($forBrowser): ?></body></html><?php endif; ?>
