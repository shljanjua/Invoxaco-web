<h3 class="fw-bold mb-4">SEO Settings</h3>
<p class="text-secondary mb-4">Override the meta title, description, and robots directive for each key public page. Leave fields blank to fall back to the page's default values.</p>

<div class="accordion" id="seoAccordion">
  <?php $i = 0; foreach ($pages as $key => $page): $i++; ?>
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button <?= $i > 1 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#seo-<?= $key ?>">
        <?= e(ucfirst(str_replace('-', ' ', $key))) ?>
      </button>
    </h2>
    <div id="seo-<?= $key ?>" class="accordion-collapse collapse <?= $i === 1 ? 'show' : '' ?>">
      <div class="accordion-body">
        <form method="POST" action="<?= url('admin/seo/' . $key) ?>">
          <?= csrf_field() ?>
          <div class="mb-3">
            <label class="form-label">Meta Title</label>
            <input type="text" name="meta_title" class="form-control" value="<?= e($page['meta_title'] ?? '') ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Meta Description</label>
            <textarea name="meta_description" class="form-control" rows="2"><?= e($page['meta_description'] ?? '') ?></textarea>
          </div>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">OG Image URL</label>
              <input type="text" name="og_image" class="form-control" value="<?= e($page['og_image'] ?? '') ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">Canonical URL</label>
              <input type="text" name="canonical_url" class="form-control" value="<?= e($page['canonical_url'] ?? '') ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">Robots</label>
              <input type="text" name="robots" class="form-control" value="<?= e($page['robots'] ?? 'index,follow') ?>">
            </div>
          </div>
          <button class="btn btn-primary mt-3 btn-sm">Save</button>
        </form>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
