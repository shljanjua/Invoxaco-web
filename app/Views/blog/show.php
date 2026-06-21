<div class="container py-5" style="max-width:760px;">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb small">
      <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
      <li class="breadcrumb-item"><a href="<?= url('blog') ?>">Blog</a></li>
      <li class="breadcrumb-item active"><?= e($post['title']) ?></li>
    </ol>
  </nav>

  <?php if ($post['category_name']): ?><span class="badge bg-primary-subtle text-primary mb-2"><?= e($post['category_name']) ?></span><?php endif; ?>
  <h1 class="fw-bold mb-2"><?= e($post['title']) ?></h1>
  <p class="text-secondary mb-4"><?= e(date('F j, Y', strtotime($post['published_at']))) ?> &middot; <?= e($post['author_name'] ?? 'Invoxaco Team') ?></p>

  <?php if (!empty($post['featured_image'])): ?>
    <img src="<?= url('uploads/blog/' . $post['featured_image']) ?>" class="img-fluid rounded-4 mb-4" alt="<?= e($post['title']) ?>">
  <?php endif; ?>

  <div class="blog-content">
    <?= $post['content'] ?>
  </div>

  <hr class="my-5">
  <p class="text-secondary small">Want to streamline your own paperwork? <a href="<?= url('generators') ?>">Browse Invoxaco generators</a>.</p>
</div>
