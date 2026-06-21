<?php
/** @var array $user */
$currencies = ['USD', 'EUR', 'GBP', 'INR', 'PKR', 'AUD', 'CAD', 'AED', 'SAR', 'JPY', 'CNY', 'ZAR', 'NGN', 'BRL', 'SGD'];
?>
<div class="container py-4" style="max-width:900px;">
  <h3 class="fw-bold mb-1">Company Settings</h3>
  <p class="text-secondary mb-4">This information appears on every invoice, quote, and document you generate.</p>

  <form method="POST" action="<?= url('settings') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="card border-0 shadow-sm rounded-4 mb-3">
      <div class="card-body p-4">
        <h5 class="fw-bold mb-3">Branding</h5>
        <div class="row g-3 align-items-center">
          <div class="col-md-4 text-center">
            <?php if ($user['company_logo']): ?>
              <img src="<?= url('uploads/logos/' . e($user['company_logo'])) ?>" alt="Company logo" class="img-fluid mb-2" style="max-height:80px;">
            <?php else: ?>
              <div class="border rounded-3 d-flex align-items-center justify-content-center mb-2" style="height:80px; background:#f8f9fa;"><i class="bi bi-image text-secondary fs-3"></i></div>
            <?php endif; ?>
            <input type="file" name="company_logo" accept="image/png,image/jpeg,image/webp" class="form-control form-control-sm">
            <div class="form-text">PNG, JPG, WEBP &middot; max 2MB</div>
          </div>
          <div class="col-md-4 text-center">
            <?php if ($user['signature_path']): ?>
              <img src="<?= url('uploads/signatures/' . e($user['signature_path'])) ?>" alt="Signature" class="img-fluid mb-2" style="max-height:80px;">
            <?php else: ?>
              <div class="border rounded-3 d-flex align-items-center justify-content-center mb-2" style="height:80px; background:#f8f9fa;"><i class="bi bi-pen text-secondary fs-3"></i></div>
            <?php endif; ?>
            <input type="file" name="signature" accept="image/png,image/jpeg,image/webp" class="form-control form-control-sm">
            <div class="form-text">Authorized signature image</div>
          </div>
          <div class="col-md-4">
            <label class="form-label">Default Currency</label>
            <select name="currency" class="form-select">
              <?php foreach ($currencies as $code): ?>
                <option value="<?= $code ?>" <?= $user['currency'] === $code ? 'selected' : '' ?>><?= $code ?> (<?= currency_symbol($code) ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-3">
      <div class="card-body p-4">
        <h5 class="fw-bold mb-3">Company Info</h5>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Company Name</label>
            <input type="text" name="company_name" class="form-control" value="<?= e($user['company_name'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Website</label>
            <input type="text" name="website" class="form-control" placeholder="https://" value="<?= e($user['website'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Tax / VAT Number</label>
            <input type="text" name="tax_number" class="form-control" value="<?= e($user['tax_number'] ?? '') ?>">
          </div>
          <div class="col-12">
            <label class="form-label">Address</label>
            <textarea name="address" rows="2" class="form-control"><?= e($user['address'] ?? '') ?></textarea>
          </div>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-3">
      <div class="card-body p-4">
        <h5 class="fw-bold mb-3">Banking Details</h5>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Bank Name</label>
            <input type="text" name="bank_name" class="form-control" value="<?= e($user['bank_name'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Account Number / IBAN</label>
            <input type="text" name="bank_account_no" class="form-control" value="<?= e($user['bank_account_no'] ?? '') ?>">
          </div>
        </div>
        <p class="text-secondary small mb-0 mt-2">Shown on invoices and receipts so clients know where to pay you.</p>
      </div>
    </div>

    <button class="btn btn-primary px-4">Save Settings</button>
  </form>
</div>
