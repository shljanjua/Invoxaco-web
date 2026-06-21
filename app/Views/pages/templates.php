<div class="container py-5">
  <div class="text-center mb-5">
    <h1 class="fw-bold">Document Templates</h1>
    <p class="text-secondary fs-5">110+ templates across 10 categories, ready whenever you need them.</p>
  </div>

  <div class="row g-3">
    <?php foreach ($categories as $cat): ?>
    <div class="col-md-6">
      <a href="<?= url('generators/category/' . $cat['slug']) ?>" class="card border-0 shadow-sm rounded-4 h-100 text-decoration-none">
        <div class="card-body d-flex align-items-center gap-3">
          <i class="bi <?= e($cat['icon']) ?> fs-1 text-primary"></i>
          <div>
            <h5 class="fw-bold mb-1"><?= e($cat['name']) ?></h5>
            <p class="text-secondary small mb-0"><?= (int) $cat['template_count'] ?> templates &middot; <?= e($cat['description']) ?></p>
          </div>
        </div>
      </a>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="text-center mt-5">
    <a href="<?= url('generators') ?>" class="btn btn-primary btn-lg">Browse All Generators</a>
  </div>
</div>
