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

$skipInHeader = array_merge($documentNumberFields, ['items', 'from_name', 'to_name', 'ship_to'], $primaryDateField ? [$primaryDateField] : []);
$logoFile = $user['company_logo'] ?? null;
$signatureFile = $user['signature_path'] ?? null;
?>
<?php if ($forBrowser): ?><!DOCTYPE html><html><head><meta charset="UTF-8"><title><?= e($document['title']) ?></title>
<style>
@media print { .no-print { display: none !important; } }
</style>
</head><body>
<div class="no-print" style="background:#1f2a44;padding:10px 20px;color:#fff;display:flex;justify-content:space-between;align-items:center;font-family:Arial;">
  <span>Invoxaco Document Preview</span>
  <button onclick="window.print()" style="background:#2563eb;color:#fff;border:0;padding:8px 16px;border-radius:6px;">Print / Save as PDF</button>
</div>
<?php endif; ?>

<div style="font-family: Arial, Helvetica, sans-serif; color:#1f2937; padding:30px; max-width:800px; margin:0 auto; position:relative;">

<?php if ($watermark): ?>
<div style="position:fixed; top:40%; left:10%; transform:rotate(-30deg); font-size:80px; color:rgba(0,0,0,0.08); font-weight:bold; z-index:0;">INVOXACO FREE</div>
<?php endif; ?>

<table style="width:100%; margin-bottom:30px;">
<tr>
<td style="vertical-align:top;">
<?php if ($logoFile && file_exists(__DIR__ . '/../../../public/uploads/logos/' . $logoFile)): ?>
<img src="<?= url('uploads/logos/' . $logoFile) ?>" style="max-height:60px; margin-bottom:8px;">
<?php endif; ?>
<div style="font-size:18px; font-weight:bold; color:#1f2a44;"><?= e($user['company_name'] ?? $user['name']) ?></div>
</td>
<td style="vertical-align:top; text-align:right;">
<div style="font-size:22px; font-weight:bold; text-transform:uppercase; color:#2563eb;"><?= e($template['name']) ?></div>
<?php if ($documentNumber): ?><div style="font-size:13px; color:#6b7280;">#<?= e($documentNumber) ?></div><?php endif; ?>
<?php if ($primaryDate): ?><div style="font-size:13px; color:#6b7280;"><?= e(date('F j, Y', strtotime($primaryDate))) ?></div><?php endif; ?>
</td>
</tr>
</table>

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

<?php if (!empty($data['items']) && is_array($data['items'])): ?>
<table style="width:100%; border-collapse:collapse; margin:20px 0;">
<thead>
<tr style="background:#f3f4f6;">
<th style="text-align:left; padding:8px; font-size:12px; border-bottom:2px solid #e5e7eb;">Description</th>
<th style="text-align:right; padding:8px; font-size:12px; border-bottom:2px solid #e5e7eb;">Qty</th>
<th style="text-align:right; padding:8px; font-size:12px; border-bottom:2px solid #e5e7eb;">Price</th>
<th style="text-align:right; padding:8px; font-size:12px; border-bottom:2px solid #e5e7eb;">Total</th>
</tr>
</thead>
<tbody>
<?php foreach ($data['items'] as $item): ?>
<tr>
<td style="padding:8px; font-size:13px; border-bottom:1px solid #f3f4f6;"><?= e($item['description'] ?? '') ?></td>
<td style="padding:8px; font-size:13px; text-align:right; border-bottom:1px solid #f3f4f6;"><?= e((string) ($item['qty'] ?? '')) ?></td>
<td style="padding:8px; font-size:13px; text-align:right; border-bottom:1px solid #f3f4f6;"><?= money((float) ($item['price'] ?? 0)) ?></td>
<td style="padding:8px; font-size:13px; text-align:right; border-bottom:1px solid #f3f4f6;"><?= money((float) ($item['total'] ?? 0)) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<table style="width:100%;">
<tr><td style="text-align:right; padding:4px; font-size:13px; width:80%;">Subtotal</td><td style="text-align:right; padding:4px; font-size:13px;"><?= money((float) ($data['subtotal'] ?? 0)) ?></td></tr>
<?php if (!empty($data['tax_rate'])): ?>
<tr><td style="text-align:right; padding:4px; font-size:13px;">Tax (<?= e((string) $data['tax_rate']) ?>%)</td><td style="text-align:right; padding:4px; font-size:13px;"><?= money((float) ($data['tax_amount'] ?? 0)) ?></td></tr>
<?php endif; ?>
<tr><td style="text-align:right; padding:8px; font-size:16px; font-weight:bold; border-top:2px solid #1f2a44;">Total</td><td style="text-align:right; padding:8px; font-size:16px; font-weight:bold; border-top:2px solid #1f2a44;"><?= money((float) ($data['grand_total'] ?? 0)) ?></td></tr>
</table>
<?php endif; ?>

<?php if ($signatureFile && file_exists(__DIR__ . '/../../../public/uploads/signatures/' . $signatureFile)): ?>
<div style="margin-top:40px;">
<div style="font-size:11px; color:#9ca3af; text-transform:uppercase; margin-bottom:4px;">Authorized Signature</div>
<img src="<?= url('uploads/signatures/' . $signatureFile) ?>" style="max-height:70px;">
</div>
<?php endif; ?>

<div style="margin-top:50px; padding-top:16px; border-top:1px solid #e5e7eb; font-size:11px; color:#9ca3af; text-align:center;">
Generated with Invoxaco &middot; <?= e(url()) ?>
</div>

</div>
<?php if ($forBrowser): ?></body></html><?php endif; ?>
