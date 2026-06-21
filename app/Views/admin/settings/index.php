<h3 class="fw-bold mb-4">Website Settings</h3>

<div class="card border-0 shadow-sm rounded-4" style="max-width:640px;">
  <div class="card-body p-4">
    <form method="POST" action="<?= url('admin/settings') ?>">
      <?= csrf_field() ?>
      <div class="mb-3">
        <label class="form-label">Site Name</label>
        <input type="text" name="site_name" class="form-control" value="<?= e($settings['site_name']) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Support Email</label>
        <input type="email" name="support_email" class="form-control" value="<?= e($settings['support_email']) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Contact Phone</label>
        <input type="text" name="contact_phone" class="form-control" value="<?= e($settings['contact_phone']) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Company Address</label>
        <input type="text" name="company_address" class="form-control" value="<?= e($settings['company_address']) ?>">
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label">Google Analytics ID</label>
        <input type="text" name="google_analytics_id" class="form-control" value="<?= e($settings['google_analytics_id']) ?>" placeholder="G-XXXXXXXXXX">
      </div>
      <div class="mb-3">
        <label class="form-label">Facebook Pixel ID</label>
        <input type="text" name="facebook_pixel_id" class="form-control" value="<?= e($settings['facebook_pixel_id']) ?>">
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label">Twitter / X URL</label>
        <input type="text" name="social_twitter" class="form-control" value="<?= e($settings['social_twitter']) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Facebook URL</label>
        <input type="text" name="social_facebook" class="form-control" value="<?= e($settings['social_facebook']) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">LinkedIn URL</label>
        <input type="text" name="social_linkedin" class="form-control" value="<?= e($settings['social_linkedin']) ?>">
      </div>
      <hr>
      <div class="form-check mb-3">
        <input type="checkbox" name="maintenance_mode" class="form-check-input" id="maintenance_mode" value="1" <?= $settings['maintenance_mode'] === '1' ? 'checked' : '' ?>>
        <label class="form-check-label" for="maintenance_mode">Maintenance Mode</label>
      </div>
      <button class="btn btn-primary">Save Settings</button>
    </form>
  </div>
</div>
