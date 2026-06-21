<h2 style="margin-top:0;"><?= e($documentTitle) ?></h2>
<p><?= e($companyName) ?> has shared a <?= e($templateName) ?> with you via Invoxaco.</p>
<?php if (!empty($message)): ?>
<p style="background:#f9fafb;border-radius:8px;padding:16px;color:#374151;white-space:pre-line;"><?= e($message) ?></p>
<?php endif; ?>
<p>Please find the document attached to this email.</p>
<p style="color:#6b7280;font-size:13px;">Sent by <?= e($senderName) ?> using Invoxaco.</p>
