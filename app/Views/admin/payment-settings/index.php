<h3 class="fw-bold mb-4">Payment Settings</h3>

<div class="card border-0 shadow-sm rounded-4" style="max-width:640px;">
  <div class="card-body p-4">
    <form method="POST" action="<?= url('admin/payment-settings') ?>">
      <?= csrf_field() ?>
      <?php foreach ($gateways as $gateway): ?>
        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
          <div>
            <div class="fw-bold text-capitalize"><?= e($gateway) ?></div>
            <?php if ($configured[$gateway]): ?>
              <span class="badge bg-success-subtle text-success">API keys detected</span>
            <?php else: ?>
              <span class="badge bg-warning-subtle text-warning">No API keys in .env</span>
            <?php endif; ?>
          </div>
          <div class="form-check form-switch">
            <input type="checkbox" name="<?= e($gateway) ?>_enabled" class="form-check-input" id="<?= e($gateway) ?>_enabled" value="1" <?= $enabled[$gateway] ? 'checked' : '' ?>>
            <label class="form-check-label" for="<?= e($gateway) ?>_enabled">Enabled</label>
          </div>
        </div>
      <?php endforeach; ?>
      <p class="text-secondary small">Enabling a gateway without valid API keys will block checkout with an error message instead of failing silently. Add credentials to <code>.env</code> first (<code>STRIPE_SECRET_KEY</code>, <code>STRIPE_PUBLIC_KEY</code>, <code>STRIPE_WEBHOOK_SECRET</code>), then enable it here.</p>
      <button class="btn btn-primary">Save Settings</button>
    </form>
  </div>
</div>
