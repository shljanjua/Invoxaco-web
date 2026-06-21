<?php
$isEdit = $post !== null;
$formAction = $isEdit ? url('admin/blog/' . $post['id']) : url('admin/blog');
?>
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
  <h3 class="fw-bold mb-0"><?= $isEdit ? 'Edit' : 'New' ?> Post</h3>
  <a href="<?= url('admin/blog') ?>" class="btn btn-outline-secondary btn-sm">Back to Blog</a>
</div>

<div class="card border-0 shadow-sm rounded-4">
  <div class="card-body p-4">
    <form method="POST" action="<?= $formAction ?>">
      <?= csrf_field() ?>
      <div class="row g-3">
        <div class="col-md-8">
          <label class="form-label">Title</label>
          <input type="text" name="title" class="form-control" value="<?= e($post['title'] ?? '') ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Slug (optional)</label>
          <input type="text" name="slug" class="form-control" value="<?= e($post['slug'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Category</label>
          <select name="category_id" class="form-select">
            <option value="">— None —</option>
            <?php foreach ($categories as $c): ?>
              <option value="<?= $c['id'] ?>" <?= ($post['category_id'] ?? null) == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="draft" <?= ($post['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
            <option value="published" <?= ($post['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label">Excerpt</label>
          <input type="text" name="excerpt" class="form-control" value="<?= e($post['excerpt'] ?? '') ?>">
        </div>
        <div class="col-12">
          <label class="form-label">Content (HTML)</label>
          <textarea name="content" class="form-control" rows="14"><?= e($post['content'] ?? '') ?></textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Featured Image URL</label>
          <input type="text" name="featured_image" class="form-control" value="<?= e($post['featured_image'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Tags (comma separated)</label>
          <input type="text" name="tags" class="form-control" value="<?= e($post['tags'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Meta Title</label>
          <input type="text" name="meta_title" class="form-control" value="<?= e($post['meta_title'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Meta Description</label>
          <input type="text" name="meta_description" class="form-control" value="<?= e($post['meta_description'] ?? '') ?>">
        </div>
      </div>
      <button class="btn btn-primary mt-4"><?= $isEdit ? 'Save Changes' : 'Create Post' ?></button>
    </form>
  </div>
</div>
