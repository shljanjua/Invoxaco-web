<div class="bg-primary bg-gradient text-white py-5 mb-4">
  <div class="container">
    <h1 class="fw-bold">All Document Generators</h1>
    <p class="mb-4 opacity-90">110+ free generators across finance, sales, legal, HR, construction, real estate, freelancing, operations, marketing, and startups.</p>
    <form method="GET" action="<?= url('generators') ?>" class="d-flex" style="max-width:480px;">
      <input type="text" name="search" value="<?= e($search) ?>" class="form-control me-2" placeholder="Search generators (e.g. invoice, NDA, offer letter)">
      <button class="btn btn-light">Search</button>
    </form>
  </div>
</div>

<div class="container pb-5">
  <?php if ($search !== ''): ?>
    <h5 class="mb-3">Results for "<?= e($search) ?>"</h5>
    <div class="row g-3">
      <?php foreach ($templates as $t): ?>
      <div class="col-md-4">
        <a href="<?= url('generators/' . $t['slug']) ?>" class="card border-0 shadow-sm rounded-4 h-100 text-decoration-none">
          <div class="card-body">
            <h6 class="fw-bold mb-1"><?= e($t['name']) ?></h6>
            <?php if (!$t['is_built']): ?><span class="badge bg-secondary mb-2">Coming Soon</span><?php endif; ?>
            <p class="small text-secondary mb-0"><?= e($t['short_description']) ?></p>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
      <?php if (empty($templates)): ?><p class="text-secondary">No generators matched your search.</p><?php endif; ?>
    </div>
  <?php else: ?>
    <?php foreach ($categories as $cat): ?>
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
      <h4 class="fw-bold mb-0"><i class="bi <?= e($cat['icon']) ?> text-primary me-2"></i><?= e($cat['name']) ?></h4>
      <a href="<?= url('generators/category/' . $cat['slug']) ?>" class="small">View all (<?= (int) $cat['template_count'] ?>)</a>
    </div>
    <p class="text-secondary small mb-3"><?= e($cat['description']) ?></p>
    <div class="row g-3 mb-3">
      <?php
        $catTemplates = array_filter($templates, fn ($t) => (int) $t['category_id'] === (int) $cat['id']);
        $catTemplates = array_slice($catTemplates, 0, 4);
      ?>
      <?php foreach ($catTemplates as $t): ?>
      <div class="col-md-3 col-6">
        <a href="<?= url('generators/' . $t['slug']) ?>" class="card border-0 shadow-sm rounded-4 h-100 text-decoration-none">
          <div class="card-body">
            <h6 class="fw-bold mb-1 small"><?= e($t['name']) ?></h6>
            <?php if (!$t['is_built']): ?><span class="badge bg-secondary">Coming Soon</span><?php else: ?><span class="badge bg-success">Available</span><?php endif; ?>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
