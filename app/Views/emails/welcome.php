<h2 style="margin-top:0;">Welcome to Invoxaco, <?= e($name) ?>!</h2>
<p>Thanks for signing up. You're one step away from generating professional invoices, contracts, and 100+ business documents.</p>
<p>Please confirm your email address to activate full access to your account:</p>
<p style="text-align:center;margin:28px 0;">
  <a href="<?= e($verifyUrl) ?>" style="background:#2563eb;color:#fff;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:bold;">Verify Email Address</a>
</p>
<p style="color:#6b7280;font-size:13px;">If the button doesn't work, copy and paste this link into your browser:<br><?= e($verifyUrl) ?></p>
