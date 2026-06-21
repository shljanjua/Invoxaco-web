<h3 class="fw-bold mb-4">Subscriptions</h3>

<div class="card border-0 shadow-sm rounded-4">
  <div class="card-body">
    <?php if (empty($subscriptions)): ?>
      <p class="text-secondary mb-0">No subscriptions yet.</p>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead><tr><th>User</th><th>Plan</th><th>Billing Cycle</th><th>Status</th><th>Gateway</th><th>Period Ends</th><th>Created</th></tr></thead>
        <tbody>
        <?php foreach ($subscriptions as $s): ?>
          <tr>
            <td><?= e($s['user_name']) ?><br><span class="text-secondary small"><?= e($s['user_email']) ?></span></td>
            <td class="text-capitalize"><?= e($s['plan']) ?></td>
            <td class="text-capitalize"><?= e($s['billing_cycle'] ?? '—') ?></td>
            <td><span class="badge bg-<?= $s['status'] === 'active' ? 'success' : ($s['status'] === 'cancelled' ? 'secondary' : 'danger') ?> text-capitalize"><?= e($s['status']) ?></span></td>
            <td><?= e($s['gateway'] ?? '—') ?></td>
            <td class="text-secondary small"><?= e($s['current_period_end'] ? date('M j, Y', strtotime($s['current_period_end'])) : '—') ?></td>
            <td class="text-secondary small"><?= e(date('M j, Y', strtotime($s['created_at']))) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
