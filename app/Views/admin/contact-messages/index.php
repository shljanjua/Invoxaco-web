<h3 class="fw-bold mb-4">Contact Messages</h3>

<div class="card border-0 shadow-sm rounded-4">
  <div class="card-body">
    <?php if (empty($messages)): ?>
      <p class="text-secondary mb-0">No messages yet.</p>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead><tr><th>Name</th><th>Email</th><th>Subject</th><th>Status</th><th>Received</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($messages as $m): ?>
          <tr class="<?= $m['status'] === 'new' ? 'fw-bold' : '' ?>">
            <td><?= e($m['name']) ?></td>
            <td><?= e($m['email']) ?></td>
            <td><?= e($m['subject'] ?? '—') ?></td>
            <td><span class="badge bg-<?= $m['status'] === 'new' ? 'primary' : ($m['status'] === 'replied' ? 'success' : 'secondary') ?> text-capitalize"><?= e($m['status']) ?></span></td>
            <td class="text-secondary small"><?= e(date('M j, Y', strtotime($m['created_at']))) ?></td>
            <td class="text-end"><a href="<?= url('admin/contact-messages/' . $m['id']) ?>" class="btn btn-sm btn-outline-primary">View</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
