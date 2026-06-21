<h3 class="fw-bold mb-1">Create your account</h3>
<p class="text-secondary mb-4">Start generating professional documents for free.</p>
<form method="POST" action="<?= url('register') ?>">
  <?= csrf_field() ?>
  <div class="mb-3">
    <label class="form-label">Full Name</label>
    <input type="text" name="name" class="form-control" value="<?= old('name') ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Email Address</label>
    <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Password</label>
    <input type="password" name="password" class="form-control" minlength="8" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Confirm Password</label>
    <input type="password" name="password_confirmation" class="form-control" minlength="8" required>
  </div>
  <button type="submit" class="btn btn-primary w-100">Create Free Account</button>
</form>
<p class="text-center text-secondary small mt-4 mb-0">
  Already have an account? <a href="<?= url('login') ?>">Log in</a>
</p>
<p class="text-center text-secondary small mt-2 mb-0">
  By signing up you agree to our <a href="<?= url('legal/terms-of-service') ?>">Terms</a> and <a href="<?= url('legal/privacy-policy') ?>">Privacy Policy</a>.
</p>
