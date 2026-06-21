<h3 class="fw-bold mb-4">SMTP Settings</h3>

<div class="card border-0 shadow-sm rounded-4" style="max-width:640px;">
  <div class="card-body p-4">
    <form method="POST" action="<?= url('admin/smtp') ?>">
      <?= csrf_field() ?>
      <div class="row g-3">
        <div class="col-md-8">
          <label class="form-label">SMTP Host</label>
          <input type="text" name="host" class="form-control" value="<?= e($smtp['host'] ?? '') ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Port</label>
          <input type="number" name="port" class="form-control" value="<?= e((string) ($smtp['port'] ?? 587)) ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Encryption</label>
          <select name="encryption" class="form-select">
            <option value="tls" <?= ($smtp['encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS</option>
            <option value="ssl" <?= ($smtp['encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
          </select>
        </div>
        <div class="col-md-8">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" value="<?= e($smtp['username'] ?? '') ?>">
        </div>
        <div class="col-12">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="<?= !empty($smtp['password']) ? '••••••••• (leave blank to keep)' : '' ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">From Address</label>
          <input type="email" name="from_address" class="form-control" value="<?= e($smtp['from_address'] ?? '') ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">From Name</label>
          <input type="text" name="from_name" class="form-control" value="<?= e($smtp['from_name'] ?? 'Invoxaco') ?>" required>
        </div>
      </div>
      <button class="btn btn-primary mt-4">Save SMTP Settings</button>
    </form>
  </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mt-3" style="max-width:640px;">
  <div class="card-body p-4">
    <h6 class="fw-bold">Test Configuration</h6>
    <p class="text-secondary small">Sends a test email to your own admin address using the saved settings above.</p>
    <form method="POST" action="<?= url('admin/smtp/test') ?>">
      <?= csrf_field() ?>
      <button class="btn btn-outline-primary btn-sm">Send Test Email</button>
    </form>
  </div>
</div>
