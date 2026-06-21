<div class="container py-5" style="max-width:760px;">
  <h1 class="fw-bold mb-1"><?= e($page['title']) ?></h1>
  <p class="text-secondary small mb-5">Last updated: <?= date('F j, Y') ?></p>

  <?php foreach ($page['sections'] as $section): ?>
    <h5 class="fw-bold mt-4"><?= e($section['heading']) ?></h5>
    <p class="text-secondary"><?= $section['body'] ?></p>
  <?php endforeach; ?>

  <p class="text-secondary small mt-5">Questions about this page? Contact <a href="mailto:support@invoxaco.com">support@invoxaco.com</a>.</p>
</div>
