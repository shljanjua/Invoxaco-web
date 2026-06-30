<?php
/** @var array $categories */
/** @var array $products */
/** @var array|null $activeCategory */
/** @var string|null $activeType */
use App\Models\DigitalProduct;
?>
<div class="hero-banner text-white py-5">
  <div class="container py-3">
    <span class="badge rounded-pill bg-white text-primary fw-semibold px-3 py-2 mb-3"><i class="bi bi-bag me-1"></i>Invoxaco Digital Store</span>
    <h1 class="fw-bold display-5 mb-2"><?= e($activeCategory['name'] ?? 'Business E-books, Templates &amp; Tools') ?></h1>
    <p class="fs-5 opacity-90 mb-0"><?= e($activeCategory['description'] ?? 'Professionally crafted digital products. Buy once, download instantly, keep forever.') ?></p>
  </div>
</div>

<div class="container py-5">
  <div class="row g-4">
    <aside class="col-lg-3">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">Categories</h6>
        <a href="<?= url('store/cart') ?>" class="btn btn-sm btn-outline-primary position-relative">
          <i class="bi bi-cart"></i>
          <?php if (($cartCount ?? 0) > 0): ?><span class="badge bg-primary ms-1"><?= (int) $cartCount ?></span><?php endif; ?>
        </a>
      </div>
      <div class="list-group shadow-sm rounded-4 overflow-hidden">
        <a href="<?= url('store') ?>" class="list-group-item list-group-item-action <?= !$activeCategory ? 'active' : '' ?>">All Products</a>
        <?php foreach ($categories as $c): ?>
          <a href="<?= url('store/category/' . $c['slug']) ?>" class="list-group-item list-group-item-action <?= ($activeCategory['id'] ?? null) == $c['id'] ? 'active' : '' ?>"><?= e($c['name']) ?></a>
        <?php endforeach; ?>
      </div>
    </aside>

    <div class="col-lg-9">
      <?php if (empty($products)): ?>
        <div class="text-center py-5">
          <i class="bi bi-bag-x display-4 text-secondary"></i>
          <p class="text-secondary mt-3">No products here yet. Check back soon.</p>
        </div>
      <?php else: ?>
      <div class="row g-4">
        <?php foreach ($products as $p): ?>
          <?php $price = DigitalProduct::effectivePrice($p); $onSale = DigitalProduct::isOnSale($p); ?>
          <div class="col-md-6 col-xl-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
              <a href="<?= url('store/product/' . $p['slug']) ?>" class="text-decoration-none">
                <div class="ratio ratio-16x9 bg-light d-flex align-items-center justify-content-center">
                  <?php if (!empty($p['cover_image'])): ?>
                    <img src="<?= asset('uploads/products/' . $p['cover_image']) ?>" alt="<?= e($p['name']) ?>" style="object-fit:cover;width:100%;height:100%;">
                  <?php else: ?>
                    <div class="d-flex align-items-center justify-content-center w-100 h-100"><i class="bi bi-file-earmark-richtext display-4 text-secondary"></i></div>
                  <?php endif; ?>
                </div>
              </a>
              <div class="card-body d-flex flex-column">
                <span class="badge bg-light text-secondary text-uppercase align-self-start mb-2" style="font-size:.65rem;"><?= e($p['type']) ?></span>
                <h6 class="fw-bold mb-1"><a class="text-dark text-decoration-none" href="<?= url('store/product/' . $p['slug']) ?>"><?= e($p['name']) ?></a></h6>
                <p class="text-secondary small flex-grow-1 mb-2"><?= e($p['short_description'] ?: '') ?></p>
                <div class="d-flex align-items-center justify-content-between">
                  <div>
                    <?php if ($price <= 0): ?>
                      <span class="fw-bold text-success">Free</span>
                    <?php else: ?>
                      <span class="fw-bold fs-5"><?= money($price, $p['currency']) ?></span>
                      <?php if ($onSale): ?><span class="text-secondary text-decoration-line-through small ms-1"><?= money((float) $p['price'], $p['currency']) ?></span><?php endif; ?>
                    <?php endif; ?>
                  </div>
                  <a href="<?= url('store/product/' . $p['slug']) ?>" class="btn btn-sm btn-primary">View</a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
