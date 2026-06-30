<?php
/** @var array $order */
/** @var array $grants */
/** @var bool $paid */
?>
<div class="container py-5" style="max-width:760px;">
  <?php if ($paid): ?>
    <div class="text-center mb-4">
      <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 mb-3" style="width:72px;height:72px;">
        <i class="bi bi-check-lg text-success" style="font-size:2.4rem;"></i>
      </div>
      <h1 class="fw-bold mb-1">Thank you! Your order is ready</h1>
      <p class="text-secondary">A copy of your download links has also been emailed to <strong><?= e($order['customer_email']) ?></strong>.</p>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
      <div class="card-header bg-white border-0 pt-3"><h5 class="fw-bold mb-0">Your downloads</h5></div>
      <div class="card-body">
        <?php if (empty($grants)): ?>
          <p class="text-secondary mb-0">No downloadable files are attached to this order.</p>
        <?php else: ?>
          <?php foreach ($grants as $g): ?>
            <div class="d-flex align-items-center justify-content-between border-bottom py-3">
              <div class="d-flex align-items-center gap-3">
                <i class="bi bi-file-earmark-arrow-down fs-3 text-primary"></i>
                <div>
                  <div class="fw-semibold"><?= e($g['product_name'] ?? 'Download') ?></div>
                  <div class="text-secondary small text-uppercase" style="font-size:.65rem;"><?= e($g['product_type'] ?? '') ?></div>
                </div>
              </div>
              <a href="<?= url('store/download/' . $g['token']) ?>" class="btn btn-primary"><i class="bi bi-download me-1"></i>Download</a>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <?php if (empty($order['user_id'])): ?>
      <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i><a href="<?= url('register') ?>">Create a free account</a> with this email to keep all your purchases in one download library.</div>
    <?php else: ?>
      <div class="text-center"><a href="<?= url('downloads') ?>" class="btn btn-outline-primary">Go to My Downloads</a></div>
    <?php endif; ?>

  <?php else: ?>
    <div class="text-center">
      <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-10 mb-3" style="width:72px;height:72px;">
        <i class="bi bi-hourglass-split text-warning" style="font-size:2.2rem;"></i>
      </div>
      <h1 class="fw-bold mb-2">Payment is processing</h1>
      <p class="text-secondary">Your order <strong>#<?= (int) $order['id'] ?></strong> is awaiting payment confirmation. This page will show your downloads as soon as payment clears — we'll also email them to <strong><?= e($order['customer_email']) ?></strong>.</p>
      <a href="<?= url('store/order/' . $order['token']) ?>" class="btn btn-outline-primary mt-2"><i class="bi bi-arrow-clockwise me-1"></i>Refresh status</a>
    </div>
  <?php endif; ?>
</div>
