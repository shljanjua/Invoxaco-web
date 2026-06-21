<h3 class="fw-bold mb-1">Welcome back</h3>
<p class="text-secondary mb-4">Log in to manage your documents.</p>
<form method="POST" action="<?= url('login') ?>">
  <?= csrf_field() ?>
  <div class="mb-3">
    <label class="form-label">Email Address</label>
    <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Password</label>
    <input type="password" name="password" class="form-control" required>
  </div>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
      <label class="form-check-label small" for="remember">Remember me</label>
    </div>
    <a href="<?= url('forgot-password') ?>" class="small">Forgot password?</a>
  </div>
  <button type="submit" class="btn btn-primary w-100">Log In</button>
</form>
<p class="text-center text-secondary small mt-4 mb-0">
  Don't have an account? <a href="<?= url('register') ?>">Sign up free</a>
</p>
