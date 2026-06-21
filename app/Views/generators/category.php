<div class="container py-5">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb small">
      <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
      <li class="breadcrumb-item"><a href="<?= url('generators') ?>">Generators</a></li>
      <li class="breadcrumb-item active"><?= e($category['name']) ?></li>
    </ol>
  </nav>
  <h1 class="fw-bold"><i class="bi <?= e($category['icon']) ?> text-primary me-2"></i><?= e($category['name']) ?></h1>
  <p class="text-secondary mb-4"><?= e($category['description']) ?></p>

  <div class="row g-3">
    <?php foreach ($templates as $t): ?>
    <div class="col-md-4 col-6">
      <a href="<?= url('generators/' . $t['slug']) ?>" class="card border-0 shadow-sm rounded-4 h-100 text-decoration-none">
        <div class="card-body">
          <h6 class="fw-bold mb-1"><?= e($t['name']) ?></h6>
          <?php if (!$t['is_built']): ?><span class="badge bg-secondary mb-2">Coming Soon</span><?php else: ?><span class="badge bg-success mb-2">Available</span><?php endif; ?>
          <p class="small text-secondary mb-0"><?= e($t['short_description']) ?></p>
        </div>
      </a>
    </div>
    <?php endforeach; ?>
  </div>
</div>
