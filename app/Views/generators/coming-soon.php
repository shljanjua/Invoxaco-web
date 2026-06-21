<div class="container py-5" style="max-width:760px;">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb small">
      <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
      <li class="breadcrumb-item"><a href="<?= url('generators') ?>">Generators</a></li>
      <?php if ($category): ?><li class="breadcrumb-item"><a href="<?= url('generators/category/' . $category['slug']) ?>"><?= e($category['name']) ?></a></li><?php endif; ?>
      <li class="breadcrumb-item active"><?= e($template['name']) ?></li>
    </ol>
  </nav>

  <span class="badge bg-secondary mb-3">Coming Soon</span>
  <h1 class="fw-bold"><?= e($template['name']) ?></h1>
  <p class="text-secondary fs-5"><?= e($template['description'] ?: $template['short_description']) ?></p>

  <div class="card border-0 shadow-sm rounded-4 mt-4">
    <div class="card-body p-4">
      <h5 class="fw-bold">Get notified when it's ready</h5>
      <p class="text-secondary small">We're rolling out new generators every week. Leave your email and we'll let you know the moment the <?= e($template['name']) ?> goes live.</p>
      <form method="POST" action="<?= url('generators/' . $template['slug'] . '/notify') ?>" class="d-flex flex-wrap gap-2">
        <?= csrf_field() ?>
        <input type="email" name="email" required class="form-control" style="max-width:320px;" placeholder="you@company.com">
        <button class="btn btn-primary">Notify Me</button>
      </form>
    </div>
  </div>

  <div class="mt-4">
    <p class="text-secondary small mb-2">In the meantime, these generators are ready to use right now:</p>
    <div class="d-flex flex-wrap gap-2">
      <?php foreach (\App\Models\DocumentTemplate::builtTemplates() as $built): ?>
        <a href="<?= url('generators/' . $built['slug']) ?>" class="btn btn-sm btn-outline-primary"><?= e($built['name']) ?></a>
      <?php endforeach; ?>
    </div>
  </div>

  <?php if (!empty($faqs)): ?>
  <div class="mt-5">
    <h5 class="fw-bold mb-3">Frequently Asked Questions</h5>
    <div class="accordion" id="faqAccordion">
      <?php foreach ($faqs as $i => $faq): ?>
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button <?= $i > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?= $i ?>">
            <?= e($faq['q']) ?>
          </button>
        </h2>
        <div id="faq<?= $i ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordion">
          <div class="accordion-body small text-secondary"><?= e($faq['a']) ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
