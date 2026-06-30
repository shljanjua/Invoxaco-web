<?php
/** @var array $cart */
/** @var array|null $user */
/** @var bool $stripeEnabled */
$isFree = $cart['total'] <= 0;
?>
<div class="container py-5" style="max-width:960px;">
  <h1 class="fw-bold mb-4">Checkout</h1>

  <form method="POST" action="<?= url('store/checkout') ?>">
    <?= csrf_field() ?>
    <div class="row g-4">
      <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
          <div class="card-body">
            <h5 class="fw-bold mb-3">Your details</h5>
            <?php if ($user): ?>
              <p class="mb-1"><i class="bi bi-person-circle me-2"></i><?= e($user['name']) ?></p>
              <p class="text-secondary mb-0"><i class="bi bi-envelope me-2"></i><?= e($user['email']) ?></p>
              <p class="text-secondary small mt-2 mb-0">Your downloads will be saved to your account library.</p>
            <?php else: ?>
              <div class="mb-3">
                <label class="form-label">Full name</label>
                <input type="text" name="customer_name" class="form-control" value="<?= old('customer_name') ?>" placeholder="Jane Doe">
              </div>
              <div class="mb-2">
                <label class="form-label">Email address <span class="text-danger">*</span></label>
                <input type="email" name="customer_email" class="form-control" value="<?= old('customer_email') ?>" required placeholder="you@example.com">
                <div class="form-text">Your download links will be emailed here. <a href="<?= url('login') ?>">Log in</a> to save them to an account.</div>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <?php if (!$isFree): ?>
        <div class="card border-0 shadow-sm rounded-4">
          <div class="card-body">
            <h5 class="fw-bold mb-3">Payment</h5>
            <?php if ($stripeEnabled): ?>
              <p class="text-secondary mb-2"><i class="bi bi-credit-card me-2"></i>You'll be redirected to our secure Stripe checkout to complete payment.</p>
              <div class="d-flex gap-2 align-items-center text-secondary"><i class="bi bi-shield-lock"></i><span class="small">256-bit SSL encrypted. We never store your card details.</span></div>
            <?php else: ?>
              <div class="alert alert-warning mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Online card payment isn't enabled yet. Please contact <a href="<?= url('contact') ?>">support</a> to complete your purchase.</div>
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top:90px;">
          <div class="card-body">
            <h5 class="fw-bold mb-3">Order summary</h5>
            <?php foreach ($cart['items'] as $p): ?>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-truncate me-2"><?= e($p['name']) ?></span>
                <span class="fw-semibold text-nowrap"><?= ($p['effective_price'] ?? 0) <= 0 ? 'Free' : money((float) $p['effective_price'], $p['currency']) ?></span>
              </div>
            <?php endforeach; ?>
            <hr>
            <div class="d-flex justify-content-between fw-bold fs-5">
              <span>Total</span>
              <span><?= $isFree ? 'Free' : money($cart['total'], $cart['currency']) ?></span>
            </div>
            <button class="btn btn-primary btn-lg w-100 mt-3" <?= (!$isFree && !$stripeEnabled) ? 'disabled' : '' ?>>
              <i class="bi bi-lock me-1"></i><?= $isFree ? 'Get my downloads' : 'Pay ' . money($cart['total'], $cart['currency']) ?>
            </button>
            <p class="text-secondary small text-center mt-3 mb-0">By completing this order you agree to our <a href="<?= url('legal/terms') ?>">Terms</a>.</p>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
