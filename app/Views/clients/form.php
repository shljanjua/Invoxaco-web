<?php $isEdit = $client !== null; ?>
<div class="container py-4" style="max-width:700px;">
  <h3 class="fw-bold mb-4"><?= $isEdit ? 'Edit' : 'Add' ?> Client</h3>

  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
      <form method="POST" action="<?= e($formAction) ?>">
        <?= csrf_field() ?>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="<?= e($client['name'] ?? '') ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= e($client['email'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= e($client['phone'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Company</label>
            <input type="text" name="company" class="form-control" value="<?= e($client['company'] ?? '') ?>">
          </div>
          <div class="col-12">
            <label class="form-label">Address</label>
            <textarea name="address" rows="2" class="form-control"><?= e($client['address'] ?? '') ?></textarea>
          </div>
          <div class="col-12">
            <label class="form-label">Notes</label>
            <textarea name="notes" rows="3" class="form-control"><?= e($client['notes'] ?? '') ?></textarea>
          </div>
        </div>
        <button class="btn btn-primary mt-4"><?= $isEdit ? 'Save Changes' : 'Add Client' ?></button>
      </form>
    </div>
  </div>
</div>
