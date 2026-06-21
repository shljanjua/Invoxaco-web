<div class="container py-5">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb small">
      <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
      <li class="breadcrumb-item"><a href="<?= url('blog') ?>">Blog</a></li>
      <li class="breadcrumb-item active"><?= e($category['name']) ?></li>
    </ol>
  </nav>
  <h1 class="fw-bold mb-4"><?= e($category['name']) ?></h1>

  <?php if (empty($posts)): ?>
    <p class="text-secondary">No posts in this category yet.</p>
  <?php else: ?>
  <div class="row g-4">
    <?php foreach ($posts as $post): ?>
    <div class="col-md-4">
      <a href="<?= url('blog/' . $post['slug']) ?>" class="card border-0 shadow-sm rounded-4 h-100 text-decoration-none">
        <div class="card-body">
          <h5 class="fw-bold"><?= e($post['title']) ?></h5>
          <p class="text-secondary small mb-0"><?= e($post['excerpt'] ?? '') ?></p>
        </div>
      </a>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
