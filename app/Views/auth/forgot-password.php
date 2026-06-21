<h3 class="fw-bold mb-1">Forgot your password?</h3>
<p class="text-secondary mb-4">Enter your email and we'll send you a reset link.</p>
<form method="POST" action="<?= url('forgot-password') ?>">
  <?= csrf_field() ?>
  <div class="mb-3">
    <label class="form-label">Email Address</label>
    <input type="email" name="email" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
</form>
<p class="text-center text-secondary small mt-4 mb-0">
  <a href="<?= url('login') ?>">Back to login</a>
</p>
