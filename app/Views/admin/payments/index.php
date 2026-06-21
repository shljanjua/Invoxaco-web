<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <h3 class="fw-bold mb-0">Payments</h3>
  <span class="badge bg-success fs-6">Total Revenue: <?= money($totalRevenue) ?></span>
</div>

<div class="card border-0 shadow-sm rounded-4">
  <div class="card-body">
    <?php if (empty($payments)): ?>
      <p class="text-secondary mb-0">No payments yet. Payment gateway integration is not yet wired up — this table will populate once a gateway is connected.</p>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead><tr><th>User</th><th>Amount</th><th>Gateway</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach ($payments as $p): ?>
          <tr>
            <td><?= e($p['user_name']) ?><br><span class="text-secondary small"><?= e($p['user_email']) ?></span></td>
            <td><?= money((float) $p['amount']) ?> <?= e($p['currency']) ?></td>
            <td><?= e($p['gateway']) ?></td>
            <td><span class="badge bg-<?= $p['status'] === 'completed' ? 'success' : ($p['status'] === 'pending' ? 'warning' : 'danger') ?> text-capitalize"><?= e($p['status']) ?></span></td>
            <td class="text-secondary small"><?= e(date('M j, Y g:i A', strtotime($p['created_at']))) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
