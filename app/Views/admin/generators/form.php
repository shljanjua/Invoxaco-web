<?php
$isEdit = $template !== null;
$formAction = $isEdit ? url('admin/generators/' . $template['id']) : url('admin/generators');
?>
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
  <h3 class="fw-bold mb-0"><?= $isEdit ? 'Edit' : 'New' ?> Generator</h3>
  <a href="<?= url('admin/generators') ?>" class="btn btn-outline-secondary btn-sm">Back to Generators</a>
</div>

<div class="card border-0 shadow-sm rounded-4">
  <div class="card-body p-4">
    <form method="POST" action="<?= $formAction ?>">
      <?= csrf_field() ?>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Name</label>
          <input type="text" name="name" class="form-control" value="<?= e($template['name'] ?? '') ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Slug (optional, auto-generated from name)</label>
          <input type="text" name="slug" class="form-control" value="<?= e($template['slug'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Category</label>
          <select name="category_id" class="form-select" required>
            <?php foreach ($categories as $c): ?>
              <option value="<?= $c['id'] ?>" <?= ($template['category_id'] ?? null) == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Plan Required</label>
          <select name="plan_required" class="form-select">
            <?php foreach (['free', 'pro', 'premium'] as $p): ?>
              <option value="<?= $p ?>" <?= ($template['plan_required'] ?? 'free') === $p ? 'selected' : '' ?>><?= ucfirst($p) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label">Short Description</label>
          <input type="text" name="short_description" class="form-control" value="<?= e($template['short_description'] ?? '') ?>">
        </div>
        <div class="col-12">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="3"><?= e($template['description'] ?? '') ?></textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Icon (Bootstrap Icon class)</label>
          <input type="text" name="icon" class="form-control" value="<?= e($template['icon'] ?? '') ?>" placeholder="bi-file-earmark-text">
        </div>
        <div class="col-md-6">
          <label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" class="form-control" value="<?= (int) ($template['sort_order'] ?? 0) ?>">
        </div>
        <div class="col-12">
          <label class="form-label">Fields Schema (JSON)</label>
          <textarea name="fields_schema" class="form-control font-monospace small" rows="8" placeholder='{"fields":[{"name":"client_name","label":"Client Name","type":"text","required":true}]}'><?= e($template['fields_schema'] ?? '') ?></textarea>
          <div class="form-text">Defines the form fields rendered for this generator. Supported types: text, textarea, date, number, line_items.</div>
        </div>
        <div class="col-12">
          <label class="form-label">FAQs (JSON array, optional)</label>
          <textarea name="faqs" class="form-control font-monospace small" rows="4" placeholder='[{"question":"...","answer":"..."}]'><?= e($template['faqs'] ?? '') ?></textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Meta Title</label>
          <input type="text" name="meta_title" class="form-control" value="<?= e($template['meta_title'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Meta Description</label>
          <input type="text" name="meta_description" class="form-control" value="<?= e($template['meta_description'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <div class="form-check mt-2">
            <input type="checkbox" name="is_built" class="form-check-input" id="is_built" value="1" <?= (int) ($template['is_built'] ?? 0) === 1 ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_built">Built (form is functional)</label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-check mt-2">
            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" <?= (int) ($template['is_active'] ?? 1) === 1 ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_active">Active (visible to users)</label>
          </div>
        </div>
      </div>
      <button class="btn btn-primary mt-4"><?= $isEdit ? 'Save Changes' : 'Create Generator' ?></button>
    </form>
  </div>
</div>
