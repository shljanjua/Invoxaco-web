<?php
/** @var array $def */
/** @var string $slug */
/** @var array $input */
/** @var array $values */
/** @var string $currency */
/** @var string $generatedAt */
$accent = '#2563eb';

if (!function_exists('fmt_calc_value')) {
    function fmt_calc_value($value, string $format, string $currency): string
    {
        if ($value === null) {
            return 'N/A';
        }
        return match ($format) {
            'currency' => money((float) $value, $currency),
            'percent' => number_format((float) $value, 2) . '%',
            'ratio' => number_format((float) $value, 2) . 'x',
            'months' => number_format((float) $value, 1) . ' months',
            default => number_format((float) $value, 2),
        };
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?= e($def['name']) ?> Result</title>
</head>
<body>
<div style="font-family: Arial, Helvetica, sans-serif; color:#1f2937; padding:30px; max-width:800px; margin:0 auto;">

  <table style="width:100%; margin-bottom:24px; border-bottom:4px solid <?= e($accent) ?>; padding-bottom:18px;">
    <tr>
      <td style="vertical-align:top;">
        <div style="font-size:18px; font-weight:bold; color:#1f2a44;">Invoxaco</div>
        <div style="font-size:12px; color:#6b7280;"><?= e(url()) ?></div>
      </td>
      <td style="vertical-align:top; text-align:right;">
        <div style="font-size:20px; font-weight:bold; color:<?= e($accent) ?>;"><?= e($def['name']) ?></div>
        <div style="font-size:12px; color:#6b7280;">Generated <?= e($generatedAt) ?></div>
      </td>
    </tr>
  </table>

  <h3 style="font-size:14px; color:#1f2937; margin-bottom:10px;">Inputs</h3>
  <table style="width:100%; border-collapse:collapse; margin-bottom:24px;">
    <?php foreach ($def['fields'] as $field): ?>
      <tr>
        <td style="padding:6px 0; font-size:12px; color:#6b7280; border-bottom:1px solid #f3f4f6;"><?= e($field['label']) ?></td>
        <td style="padding:6px 0; font-size:13px; text-align:right; border-bottom:1px solid #f3f4f6;">
          <?php
          $val = $input[$field['name']] ?? '';
          if (($field['type'] ?? 'number') === 'select') {
              echo e((string) ($field['options'][$val] ?? $val));
          } else {
              echo e(number_format((float) $val, 2));
          }
          ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>

  <h3 style="font-size:14px; color:#1f2937; margin-bottom:10px;">Results</h3>
  <table style="width:100%; border-collapse:collapse; margin-bottom:24px;">
    <?php foreach ($def['results'] as $rd): ?>
      <tr>
        <td style="padding:8px 10px; font-size:13px; background:#f8f9fc;"><?= e($rd['label']) ?></td>
        <td style="padding:8px 10px; font-size:14px; font-weight:bold; text-align:right; background:#f8f9fc; color:<?= e($accent) ?>;">
          <?= e(fmt_calc_value($values[$rd['key']] ?? null, $rd['format'], $currency)) ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>

  <?php if (!empty($def['hasAmortization']) && !empty($values['schedule'])): ?>
  <h3 style="font-size:14px; color:#1f2937; margin-bottom:10px;">Amortization Schedule</h3>
  <table style="width:100%; border-collapse:collapse;">
    <thead>
      <tr style="background:<?= e($accent) ?>;">
        <th style="text-align:left; padding:6px 8px; font-size:11px; color:#fff;">Month</th>
        <th style="text-align:right; padding:6px 8px; font-size:11px; color:#fff;">Payment</th>
        <th style="text-align:right; padding:6px 8px; font-size:11px; color:#fff;">Principal</th>
        <th style="text-align:right; padding:6px 8px; font-size:11px; color:#fff;">Interest</th>
        <th style="text-align:right; padding:6px 8px; font-size:11px; color:#fff;">Balance</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($values['schedule'] as $row): ?>
      <tr>
        <td style="padding:5px 8px; font-size:11px; border-bottom:1px solid #f3f4f6;"><?= (int) $row['month'] ?></td>
        <td style="padding:5px 8px; font-size:11px; text-align:right; border-bottom:1px solid #f3f4f6;"><?= money((float) $row['payment'], $currency) ?></td>
        <td style="padding:5px 8px; font-size:11px; text-align:right; border-bottom:1px solid #f3f4f6;"><?= money((float) $row['principal'], $currency) ?></td>
        <td style="padding:5px 8px; font-size:11px; text-align:right; border-bottom:1px solid #f3f4f6;"><?= money((float) $row['interest'], $currency) ?></td>
        <td style="padding:5px 8px; font-size:11px; text-align:right; border-bottom:1px solid #f3f4f6;"><?= money((float) $row['balance'], $currency) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>

  <div style="margin-top:24px; padding-top:14px; border-top:1px solid #e5e7eb; font-size:11px; color:#9ca3af; text-align:center;">
    Generated with Invoxaco &middot; <?= e(url()) ?>
  </div>

</div>
</body>
</html>
