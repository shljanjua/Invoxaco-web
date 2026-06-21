<div class="bg-primary bg-gradient text-white py-5 mb-4">
  <div class="container">
    <h1 class="fw-bold">Free Financial Calculators</h1>
    <p class="mb-0 opacity-90">16 real-time business calculators &mdash; pricing, costing, ROI, loans, runway, and more. Download every result as a PDF.</p>
  </div>
</div>

<div class="container pb-5">
  <?php foreach ($grouped as $category => $items): ?>
    <h4 class="fw-bold mt-4 mb-3"><?= e($category) ?></h4>
    <div class="row g-3 mb-3">
      <?php foreach ($items as $slug => $def): ?>
      <div class="col-md-4 col-6">
        <a href="<?= url('calculators/' . $slug) ?>" class="card border-0 shadow-sm rounded-4 h-100 text-decoration-none generator-card">
          <div class="card-body">
            <i class="bi <?= e($def['icon']) ?> fs-3 text-primary mb-2 d-block"></i>
            <h6 class="fw-bold mb-1"><?= e($def['name']) ?></h6>
            <p class="small text-secondary mb-0"><?= e($def['description']) ?></p>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
</div>
