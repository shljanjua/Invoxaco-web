<h3 class="fw-bold mb-4">Support Tickets</h3>

<div class="card border-0 shadow-sm rounded-4">
  <div class="card-body">
    <?php if (empty($tickets)): ?>
      <p class="text-secondary mb-0">No support tickets yet.</p>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead><tr><th>Subject</th><th>User</th><th>Priority</th><th>Status</th><th>Updated</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($tickets as $t): ?>
          <tr>
            <td><?= e($t['subject']) ?></td>
            <td><?= e($t['user_name']) ?><br><span class="text-secondary small"><?= e($t['user_email']) ?></span></td>
            <td><span class="badge bg-<?= $t['priority'] === 'high' ? 'danger' : ($t['priority'] === 'medium' ? 'warning' : 'secondary') ?> text-capitalize"><?= e($t['priority']) ?></span></td>
            <td><span class="badge bg-<?= $t['status'] === 'closed' ? 'secondary' : ($t['status'] === 'pending' ? 'warning' : 'success') ?> text-capitalize"><?= e($t['status']) ?></span></td>
            <td class="text-secondary small"><?= e(date('M j, Y', strtotime($t['updated_at']))) ?></td>
            <td class="text-end"><a href="<?= url('admin/support-tickets/' . $t['id']) ?>" class="btn btn-sm btn-outline-primary">View</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
