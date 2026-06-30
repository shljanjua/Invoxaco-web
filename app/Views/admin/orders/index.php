<?php
/** @var array $orders */
/** @var float $revenue */
$badge = ['paid' => 'success', 'free' => 'info', 'pending' => 'warning text-dark', 'failed' => 'danger', 'refunded' => 'secondary'];
?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <h3 class="fw-bold mb-0">Store Orders</h3>
  <div class="text-end">
    <div class="text-secondary small">Store revenue (paid)</div>
    <div class="fs-4 fw-bold"><?= money($revenue, 'USD') ?></div>
  </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
  <div class="card-body">
    <?php if (empty($orders)): ?>
      <p class="text-secondary mb-0">No orders yet.</p>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead><tr><th>#</th><th>Customer</th><th>Items</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
          <tr>
            <td>#<?= (int) $o['id'] ?></td>
            <td><?= e($o['customer_name'] ?: '—') ?><br><span class="text-secondary small"><?= e($o['customer_email']) ?></span></td>
            <td><?= (int) $o['item_count'] ?></td>
            <td><?= money((float) $o['total'], $o['currency']) ?></td>
            <td><span class="badge bg-<?= $badge[$o['status']] ?? 'secondary' ?> text-capitalize"><?= e($o['status']) ?></span></td>
            <td class="text-secondary small"><?= e(date('M j, Y H:i', strtotime($o['created_at']))) ?></td>
            <td class="text-end"><a href="<?= url('admin/orders/' . $o['id']) ?>" class="btn btn-sm btn-outline-primary">View</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
