<div class="bg-primary bg-gradient text-white py-5">
  <div class="container py-4">
    <div class="row align-items-center g-4">
      <div class="col-lg-7">
        <h1 class="fw-bold display-5">Generate Professional Business Documents in Minutes</h1>
        <p class="fs-5 opacity-90 mb-4">Invoices, quotations, contracts, receipts, and 100+ other documents — created, saved, and downloaded as PDF or Word, free to start.</p>
        <div class="d-flex gap-2 flex-wrap">
          <a href="<?= url('register') ?>" class="btn btn-light btn-lg fw-bold">Get Started Free</a>
          <a href="<?= url('generators') ?>" class="btn btn-outline-light btn-lg">Browse Generators</a>
        </div>
        <p class="small opacity-75 mt-3 mb-0">No credit card required &middot; 10 free documents every month</p>
      </div>
      <div class="col-lg-5">
        <div class="card border-0 shadow-lg rounded-4 text-dark">
          <div class="card-body p-4">
            <h6 class="fw-bold mb-3">Popular right now</h6>
            <?php foreach (array_slice($builtTemplates, 0, 5) as $t): ?>
              <a href="<?= url('generators/' . $t['slug']) ?>" class="d-flex justify-content-between align-items-center py-2 border-bottom text-decoration-none text-dark">
                <span><?= e($t['name']) ?></span>
                <i class="bi bi-arrow-right"></i>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container py-5">
  <div class="text-center mb-5">
    <h2 class="fw-bold">Everything you need to get paid and stay organized</h2>
    <p class="text-secondary">One platform for every document your business needs.</p>
  </div>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-body p-4">
          <i class="bi bi-file-earmark-text fs-2 text-primary mb-3"></i>
          <h5 class="fw-bold">110+ Document Types</h5>
          <p class="text-secondary small mb-0">Invoices, contracts, HR letters, construction forms, real estate paperwork, and more — all in one place.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-body p-4">
          <i class="bi bi-file-pdf fs-2 text-primary mb-3"></i>
          <h5 class="fw-bold">PDF &amp; Word Export</h5>
          <p class="text-secondary small mb-0">Download polished, branded documents instantly, or email them straight to your client.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-body p-4">
          <i class="bi bi-shield-check fs-2 text-primary mb-3"></i>
          <h5 class="fw-bold">Secure &amp; Reliable</h5>
          <p class="text-secondary small mb-0">Your documents and client data are encrypted, backed up, and always available when you need them.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="bg-light py-5">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold">Browse by category</h2>
      <p class="text-secondary">Find the right document for your business in seconds.</p>
    </div>
    <div class="row g-3">
      <?php foreach (array_slice($categories, 0, 10) as $cat): ?>
      <div class="col-md-3 col-6">
        <a href="<?= url('generators/category/' . $cat['slug']) ?>" class="card border-0 shadow-sm rounded-4 h-100 text-decoration-none">
          <div class="card-body text-center">
            <i class="bi <?= e($cat['icon']) ?> fs-2 text-primary mb-2 d-block"></i>
            <h6 class="fw-bold mb-1"><?= e($cat['name']) ?></h6>
            <span class="text-secondary small"><?= (int) $cat['template_count'] ?> templates</span>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-4">
      <a href="<?= url('generators') ?>" class="btn btn-primary">View All Generators</a>
    </div>
  </div>
</div>

<div class="container py-5 text-center">
  <h2 class="fw-bold mb-3">Simple, transparent pricing</h2>
  <p class="text-secondary mb-4">Start free. Upgrade anytime as your business grows.</p>
  <a href="<?= url('pricing') ?>" class="btn btn-primary btn-lg">See Pricing Plans</a>
</div>

<?php if (!empty($posts)): ?>
<div class="bg-light py-5">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold mb-0">From the blog</h2>
      <a href="<?= url('blog') ?>" class="small">View all posts</a>
    </div>
    <div class="row g-4">
      <?php foreach ($posts as $post): ?>
      <div class="col-md-4">
        <a href="<?= url('blog/' . $post['slug']) ?>" class="card border-0 shadow-sm rounded-4 h-100 text-decoration-none">
          <div class="card-body">
            <h6 class="fw-bold"><?= e($post['title']) ?></h6>
            <p class="text-secondary small mb-0"><?= e($post['excerpt'] ?? '') ?></p>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>
