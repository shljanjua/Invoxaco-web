<?php
/** @var array $order */
/** @var array $items */
/** @var array $grants */
$badge = ['paid' => 'success', 'free' => 'info', 'pending' => 'warning text-dark', 'failed' => 'danger', 'refunded' => 'secondary'];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h3 class="fw-bold mb-0">Order #<?= (int) $order['id'] ?></h3>
  <a href="<?= url('admin/orders') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
      <div class="card-body">
        <h6 class="fw-bold mb-3">Items</h6>
        <table class="table align-middle mb-0">
          <tbody>
          <?php foreach ($items as $it): ?>
            <tr>
              <td><?= e($it['product_name']) ?><?php if (empty($it['product_id'])): ?> <span class="badge bg-secondary">deleted</span><?php endif; ?></td>
              <td class="text-end fw-semibold"><?= money((float) $it['price'], $order['currency']) ?></td>
            </tr>
          <?php endforeach; ?>
          <tr><td class="fw-bold">Total</td><td class="text-end fw-bold"><?= money((float) $order['total'], $order['currency']) ?></td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body">
        <h6 class="fw-bold mb-3">Download grants</h6>
        <?php if (empty($grants)): ?>
          <p class="text-secondary mb-0">No grants issued yet. They're created automatically once the order is paid.</p>
        <?php else: ?>
          <table class="table align-middle mb-0">
            <thead><tr><th>Product</th><th>Downloads</th><th>Link</th></tr></thead>
            <tbody>
            <?php foreach ($grants as $g): ?>
              <tr>
                <td><?= e($g['product_name'] ?? '—') ?></td>
                <td><?= (int) $g['download_count'] ?><?= (int) $g['max_downloads'] > 0 ? ' / ' . (int) $g['max_downloads'] : '' ?></td>
                <td><a href="<?= url('store/download/' . $g['token']) ?>" target="_blank" class="small">open</a></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
      <div class="card-body">
        <h6 class="fw-bold mb-3">Details</h6>
        <p class="mb-1"><span class="text-secondary">Status:</span> <span class="badge bg-<?= $badge[$order['status']] ?? 'secondary' ?> text-capitalize"><?= e($order['status']) ?></span></p>
        <p class="mb-1"><span class="text-secondary">Customer:</span> <?= e($order['customer_name'] ?: '—') ?></p>
        <p class="mb-1"><span class="text-secondary">Email:</span> <?= e($order['customer_email']) ?></p>
        <p class="mb-1"><span class="text-secondary">Gateway:</span> <?= e($order['gateway'] ?: '—') ?></p>
        <p class="mb-1"><span class="text-secondary">Payment ID:</span> <span class="small"><?= e($order['gateway_payment_id'] ?: '—') ?></span></p>
        <p class="mb-1"><span class="text-secondary">Placed:</span> <?= e(date('M j, Y H:i', strtotime($order['created_at']))) ?></p>
        <p class="mb-0"><span class="text-secondary">Paid:</span> <?= $order['paid_at'] ? e(date('M j, Y H:i', strtotime($order['paid_at']))) : '—' ?></p>
      </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body d-grid gap-2">
        <?php if (!in_array($order['status'], ['paid', 'free'], true)): ?>
          <form method="POST" action="<?= url('admin/orders/' . $order['id'] . '/mark-paid') ?>" onsubmit="return confirm('Mark this order paid and release downloads?');">
            <?= csrf_field() ?>
            <button class="btn btn-success w-100"><i class="bi bi-check-circle me-1"></i>Mark as Paid &amp; Release</button>
          </form>
        <?php endif; ?>
        <?php if ($order['status'] === 'paid'): ?>
          <form method="POST" action="<?= url('admin/orders/' . $order['id'] . '/refund') ?>" onsubmit="return confirm('Mark this order refunded?');">
            <?= csrf_field() ?>
            <button class="btn btn-outline-warning w-100"><i class="bi bi-arrow-counterclockwise me-1"></i>Mark Refunded</button>
          </form>
        <?php endif; ?>
        <form method="POST" action="<?= url('admin/orders/' . $order['id'] . '/delete') ?>" onsubmit="return confirm('Delete this order permanently?');">
          <?= csrf_field() ?>
          <button class="btn btn-outline-danger w-100"><i class="bi bi-trash me-1"></i>Delete Order</button>
        </form>
      </div>
    </div>
  </div>
</div>
