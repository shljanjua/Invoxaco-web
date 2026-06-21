<div class="container py-5">
  <div class="text-center mb-5">
    <h1 class="fw-bold">Invoxaco Blog</h1>
    <p class="text-secondary fs-5">Tips on invoicing, contracts, and running your business smarter.</p>
  </div>

  <?php if (!empty($categories)): ?>
  <div class="d-flex flex-wrap gap-2 justify-content-center mb-5">
    <?php foreach ($categories as $cat): ?>
      <a href="<?= url('blog/category/' . $cat['slug']) ?>" class="btn btn-sm btn-outline-primary"><?= e($cat['name']) ?></a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if (empty($posts)): ?>
    <p class="text-secondary text-center">No posts published yet. Check back soon!</p>
  <?php else: ?>
  <div class="row g-4">
    <?php foreach ($posts as $post): ?>
    <div class="col-md-4">
      <a href="<?= url('blog/' . $post['slug']) ?>" class="card border-0 shadow-sm rounded-4 h-100 text-decoration-none">
        <div class="card-body">
          <?php if ($post['category_name']): ?><span class="badge bg-primary-subtle text-primary mb-2"><?= e($post['category_name']) ?></span><?php endif; ?>
          <h5 class="fw-bold"><?= e($post['title']) ?></h5>
          <p class="text-secondary small mb-2"><?= e($post['excerpt'] ?? '') ?></p>
          <p class="text-secondary small mb-0"><?= e(date('M j, Y', strtotime($post['published_at']))) ?> &middot; <?= e($post['author_name'] ?? 'Invoxaco Team') ?></p>
        </div>
      </a>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div class="d-flex justify-content-center gap-2 mt-5">
    <?php if ($page > 1): ?><a href="<?= url('blog') ?>?page=<?= $page - 1 ?>" class="btn btn-outline-secondary btn-sm">Previous</a><?php endif; ?>
    <?php if (count($posts) >= 9): ?><a href="<?= url('blog') ?>?page=<?= $page + 1 ?>" class="btn btn-outline-secondary btn-sm">Next</a><?php endif; ?>
  </div>
</div>
