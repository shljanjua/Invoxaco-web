<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h3 class="fw-bold mb-0">Support Tickets</h3>
    <a href="<?= url('support/create') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Ticket</a>
  </div>

  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">
      <?php if (empty($tickets)): ?>
        <p class="text-secondary mb-0">You haven't submitted any support tickets yet.</p>
      <?php else: ?>
      <div class="table-responsive">
        <table class="table align-middle">
          <thead><tr><th>Subject</th><th>Priority</th><th>Status</th><th>Updated</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($tickets as $t): ?>
            <tr>
              <td><?= e($t['subject']) ?></td>
              <td class="text-capitalize"><?= e($t['priority']) ?></td>
              <td><span class="badge bg-<?= $t['status'] === 'closed' ? 'secondary' : ($t['status'] === 'pending' ? 'warning' : 'success') ?>"><?= e($t['status']) ?></span></td>
              <td class="text-secondary small"><?= e(date('M j, Y', strtotime($t['updated_at']))) ?></td>
              <td class="text-end"><a href="<?= url('support/' . $t['id']) ?>" class="btn btn-sm btn-outline-primary">View</a></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
