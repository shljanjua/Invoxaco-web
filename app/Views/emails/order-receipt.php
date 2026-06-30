<?php
/** @var array $order */
/** @var array $downloads */
/** @var string $manageUrl */
?>
<h2 style="margin-top:0;">Your order is ready to download</h2>
<p>Hi <?= e($order['customer_name'] ?: 'there') ?>,</p>
<p>Thank you for your purchase from Invoxaco. Your order <strong>#<?= (int) $order['id'] ?></strong> has been confirmed and your files are ready below.</p>

<table width="100%" cellpadding="0" cellspacing="0" style="margin:20px 0;">
  <?php foreach ($downloads as $d): ?>
  <tr>
    <td style="padding:10px 0;border-bottom:1px solid #eee;font-size:15px;"><?= e($d['name']) ?></td>
    <td style="padding:10px 0;border-bottom:1px solid #eee;text-align:right;">
      <a href="<?= e($d['url']) ?>" style="background:#2563eb;color:#fff;padding:8px 18px;border-radius:6px;text-decoration:none;font-size:14px;">Download</a>
    </td>
  </tr>
  <?php endforeach; ?>
</table>

<p style="color:#6b7280;font-size:13px;">These download links are private to you. Please don't share this email.</p>
<p style="margin-top:24px;">
  <a href="<?= e($manageUrl) ?>" style="color:#2563eb;">View your order online</a>
</p>
<p style="color:#9ca3af;font-size:12px;margin-top:24px;">If a button doesn't work, copy and paste its link into your browser. Need help? Reply to this email or contact support@invoxaco.com.</p>
