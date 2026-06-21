<h3 class="fw-bold mb-1">Reset your password</h3>
<p class="text-secondary mb-4">Choose a new password for your account.</p>
<form method="POST" action="<?= url('reset-password') ?>">
  <?= csrf_field() ?>
  <input type="hidden" name="token" value="<?= e($token) ?>">
  <div class="mb-3">
    <label class="form-label">Email Address</label>
    <input type="email" name="email" class="form-control" value="<?= e($email) ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">New Password</label>
    <input type="password" name="password" class="form-control" minlength="8" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Confirm New Password</label>
    <input type="password" name="password_confirmation" class="form-control" minlength="8" required>
  </div>
  <button type="submit" class="btn btn-primary w-100">Reset Password</button>
</form>
