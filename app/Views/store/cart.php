<?php
/** @var array $cart */
?>
<div class="container py-5" style="max-width:900px;">
  <h1 class="fw-bold mb-4">Your Cart</h1>

  <?php if (empty($cart['items'])): ?>
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body text-center py-5">
        <i class="bi bi-cart-x display-4 text-secondary"></i>
        <p class="text-secondary mt-3 mb-3">Your cart is empty.</p>
        <a href="<?= url('store') ?>" class="btn btn-primary">Browse the Store</a>
      </div>
    </div>
  <?php else: ?>
    <div class="card border-0 shadow-sm rounded-4 mb-4">
      <div class="card-body p-0">
        <table class="table align-middle mb-0">
          <tbody>
          <?php foreach ($cart['items'] as $p): ?>
            <tr>
              <td style="width:70px;">
                <?php if (!empty($p['cover_image'])): ?>
                  <img src="<?= asset('uploads/products/' . $p['cover_image']) ?>" style="width:54px;height:54px;object-fit:cover;border-radius:8px;">
                <?php else: ?>
                  <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:54px;height:54px;"><i class="bi bi-file-earmark-richtext text-secondary"></i></div>
                <?php endif; ?>
              </td>
              <td>
                <a href="<?= url('store/product/' . $p['slug']) ?>" class="fw-semibold text-dark text-decoration-none"><?= e($p['name']) ?></a>
                <div class="text-secondary small text-uppercase" style="font-size:.65rem;"><?= e($p['type']) ?></div>
              </td>
              <td class="fw-bold text-end"><?= ($p['effective_price'] ?? 0) <= 0 ? 'Free' : money((float) $p['effective_price'], $p['currency']) ?></td>
              <td class="text-end" style="width:60px;">
                <form method="POST" action="<?= url('store/cart/remove') ?>" onsubmit="return confirm('Remove this item?');">
                  <?= csrf_field() ?>
                  <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                  <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="d-flex justify-content-between align-items-center">
      <a href="<?= url('store') ?>" class="btn btn-link text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Continue shopping</a>
      <div class="text-end">
        <div class="text-secondary small">Total</div>
        <div class="display-6 fw-bold mb-2"><?= $cart['total'] <= 0 ? 'Free' : money($cart['total'], $cart['currency']) ?></div>
        <a href="<?= url('store/checkout') ?>" class="btn btn-primary btn-lg"><i class="bi bi-lock me-1"></i>Proceed to Checkout</a>
      </div>
    </div>
  <?php endif; ?>
</div>
