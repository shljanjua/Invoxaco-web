<?php
/** @var array|null $product */
/** @var array $categories */
/** @var array $types */
$isEdit = $product !== null;
$action = $isEdit ? url('admin/products/' . $product['id']) : url('admin/products');
if (!function_exists('pv')) { function pv(?array $p, string $k, $default = '') { return e((string) ($p[$k] ?? $default)); } }
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h3 class="fw-bold mb-0"><?= $isEdit ? 'Edit Product' : 'New Product' ?></h3>
  <a href="<?= url('admin/products') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<form method="POST" action="<?= $action ?>" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Product name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="<?= pv($product, 'name') ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Slug <span class="text-secondary small">(leave blank to auto-generate)</span></label>
            <input type="text" name="slug" class="form-control" value="<?= pv($product, 'slug') ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Short description</label>
            <input type="text" name="short_description" class="form-control" maxlength="255" value="<?= pv($product, 'short_description') ?>" placeholder="One-line summary shown on cards and listings">
          </div>
          <div class="mb-0">
            <label class="form-label">Full description</label>
            <textarea name="description" class="form-control" rows="8" placeholder="What's inside, who it's for, what they'll get..."><?= pv($product, 'description') ?></textarea>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
          <h6 class="fw-bold mb-3">Files</h6>
          <div class="mb-3">
            <label class="form-label">Product file <?= $isEdit ? '' : '<span class="text-danger">*</span>' ?></label>
            <input type="file" name="product_file" class="form-control">
            <div class="form-text">PDF, EPUB, MOBI, ZIP, Word, Excel, PowerPoint, TXT, CSV, PNG, JPG. Max 100MB. Stored privately — only buyers can download it.</div>
            <?php if ($isEdit && !empty($product['file_name'])): ?>
              <div class="mt-2 small"><i class="bi bi-check-circle text-success"></i> Current file: <strong><?= e($product['file_name']) ?></strong> <?= $product['file_size'] ? '(' . round($product['file_size']/1048576, 2) . ' MB)' : '' ?>. Upload a new file to replace it.</div>
            <?php endif; ?>
          </div>
          <div class="mb-0">
            <label class="form-label">Cover image</label>
            <input type="file" name="cover_image" class="form-control" accept="image/png,image/jpeg,image/webp">
            <div class="form-text">PNG, JPG or WEBP, max 2MB. Shown on storefront cards and the product page.</div>
            <?php if ($isEdit && !empty($product['cover_image'])): ?>
              <img src="<?= asset('uploads/products/' . $product['cover_image']) ?>" class="mt-2 rounded" style="max-height:90px;">
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
          <h6 class="fw-bold mb-3">SEO</h6>
          <div class="mb-3">
            <label class="form-label">Meta title</label>
            <input type="text" name="meta_title" class="form-control" maxlength="190" value="<?= pv($product, 'meta_title') ?>">
          </div>
          <div class="mb-0">
            <label class="form-label">Meta description</label>
            <textarea name="meta_description" class="form-control" rows="2" maxlength="255"><?= pv($product, 'meta_description') ?></textarea>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
          <h6 class="fw-bold mb-3">Pricing</h6>
          <div class="mb-3">
            <label class="form-label">Price</label>
            <div class="input-group">
              <input type="number" step="0.01" min="0" name="price" class="form-control" value="<?= pv($product, 'price', '0.00') ?>">
            </div>
            <div class="form-text">Set 0 for a free lead-magnet download.</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Sale price <span class="text-secondary small">(optional)</span></label>
            <input type="number" step="0.01" min="0" name="sale_price" class="form-control" value="<?= $product && $product['sale_price'] !== null ? pv($product, 'sale_price') : '' ?>">
          </div>
          <div class="mb-0">
            <label class="form-label">Currency</label>
            <input type="text" name="currency" class="form-control text-uppercase" maxlength="10" value="<?= pv($product, 'currency', 'USD') ?>">
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
          <h6 class="fw-bold mb-3">Organization</h6>
          <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select">
              <option value="">— None —</option>
              <?php foreach ($categories as $c): ?>
                <option value="<?= (int) $c['id'] ?>" <?= ($product['category_id'] ?? null) == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Type</label>
            <select name="type" class="form-select text-capitalize">
              <?php foreach ($types as $t): ?>
                <option value="<?= e($t) ?>" <?= ($product['type'] ?? 'ebook') === $t ? 'selected' : '' ?>><?= e($t) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-0">
            <label class="form-label">Sort order</label>
            <input type="number" name="sort_order" class="form-control" value="<?= pv($product, 'sort_order', '0') ?>">
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
          <div class="form-check form-switch mb-2">
            <input type="checkbox" class="form-check-input" name="is_active" id="is_active" <?= (!$isEdit || (int) $product['is_active'] === 1) ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_active">Active (visible in store)</label>
          </div>
          <div class="form-check form-switch mb-0">
            <input type="checkbox" class="form-check-input" name="is_featured" id="is_featured" <?= ($isEdit && (int) $product['is_featured'] === 1) ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_featured">Featured</label>
          </div>
        </div>
      </div>

      <button class="btn btn-primary w-100 btn-lg"><i class="bi bi-save me-1"></i><?= $isEdit ? 'Save Changes' : 'Create Product' ?></button>
    </div>
  </div>
</form>
