<?php
/** @var array $product */
/** @var array|null $category */
/** @var array $related */
/** @var bool $inCart */
use App\Models\DigitalProduct;
$price = DigitalProduct::effectivePrice($product);
$onSale = DigitalProduct::isOnSale($product);
$sizeMb = $product['file_size'] ? round($product['file_size'] / 1048576, 2) : null;
?>
<div class="container py-5">
  <nav class="small mb-4">
    <a href="<?= url('store') ?>" class="text-decoration-none">Store</a>
    <?php if ($category): ?> / <a href="<?= url('store/category/' . $category['slug']) ?>" class="text-decoration-none"><?= e($category['name']) ?></a><?php endif; ?>
    / <span class="text-secondary"><?= e($product['name']) ?></span>
  </nav>

  <div class="row g-5">
    <div class="col-lg-5">
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-top" style="top:90px;">
        <div class="ratio ratio-4x3 bg-light">
          <?php if (!empty($product['cover_image'])): ?>
            <img src="<?= asset('uploads/products/' . $product['cover_image']) ?>" alt="<?= e($product['name']) ?>" style="object-fit:cover;width:100%;height:100%;">
          <?php else: ?>
            <div class="d-flex align-items-center justify-content-center"><i class="bi bi-file-earmark-richtext display-1 text-secondary"></i></div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <span class="badge bg-light text-secondary text-uppercase mb-2"><?= e($product['type']) ?></span>
      <h1 class="fw-bold mb-3"><?= e($product['name']) ?></h1>

      <div class="mb-3">
        <?php if ($price <= 0): ?>
          <span class="display-6 fw-bold text-success">Free</span>
        <?php else: ?>
          <span class="display-6 fw-bold"><?= money($price, $product['currency']) ?></span>
          <?php if ($onSale): ?><span class="text-secondary text-decoration-line-through fs-4 ms-2"><?= money((float) $product['price'], $product['currency']) ?></span><?php endif; ?>
        <?php endif; ?>
      </div>

      <?php if (!empty($product['short_description'])): ?>
        <p class="fs-5 text-secondary"><?= e($product['short_description']) ?></p>
      <?php endif; ?>

      <div class="d-flex gap-2 flex-wrap my-4">
        <?php if ($inCart): ?>
          <a href="<?= url('store/cart') ?>" class="btn btn-outline-primary btn-lg"><i class="bi bi-cart-check me-1"></i>In Cart — View</a>
        <?php else: ?>
          <form method="POST" action="<?= url('store/cart/add') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
            <button class="btn btn-outline-primary btn-lg"><i class="bi bi-cart-plus me-1"></i>Add to Cart</button>
          </form>
        <?php endif; ?>
        <form method="POST" action="<?= url('store/cart/add') ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
          <input type="hidden" name="buy_now" value="1">
          <button class="btn btn-primary btn-lg" formaction="<?= url('store/cart/add') ?>"><i class="bi bi-lightning-charge me-1"></i><?= $price <= 0 ? 'Get it Free' : 'Buy Now' ?></button>
        </form>
      </div>

      <ul class="list-unstyled small text-secondary mb-4">
        <li class="mb-1"><i class="bi bi-download me-2"></i>Instant digital download after checkout</li>
        <li class="mb-1"><i class="bi bi-shield-check me-2"></i>Secure payment &amp; private download link</li>
        <?php if ($sizeMb): ?><li class="mb-1"><i class="bi bi-hdd me-2"></i>File size: <?= e((string) $sizeMb) ?> MB</li><?php endif; ?>
        <li class="mb-1"><i class="bi bi-infinity me-2"></i>Lifetime access from your account library</li>
      </ul>

      <?php if (!empty($product['description'])): ?>
        <h5 class="fw-bold mb-2">Description</h5>
        <div class="text-body" style="white-space:pre-line;"><?= nl2br(e($product['description'])) ?></div>
      <?php endif; ?>
    </div>
  </div>

  <?php if (!empty($related)): ?>
  <hr class="my-5">
  <h4 class="fw-bold mb-4">You may also like</h4>
  <div class="row g-4">
    <?php foreach ($related as $p): ?>
      <?php $rp = DigitalProduct::effectivePrice($p); ?>
      <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm rounded-4">
          <div class="card-body">
            <span class="badge bg-light text-secondary text-uppercase mb-2" style="font-size:.65rem;"><?= e($p['type']) ?></span>
            <h6 class="fw-bold"><a class="text-dark text-decoration-none" href="<?= url('store/product/' . $p['slug']) ?>"><?= e($p['name']) ?></a></h6>
            <div class="fw-bold"><?= $rp <= 0 ? 'Free' : money($rp, $p['currency']) ?></div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
