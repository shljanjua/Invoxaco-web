<div class="mb-4">
  <h3 class="fw-bold mb-0">Dashboard</h3>
  <p class="text-secondary">Overview of your Invoxaco platform.</p>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body">
        <p class="text-secondary small mb-1">Total Users</p>
        <h3 class="fw-bold mb-0"><?= number_format($totalUsers) ?></h3>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body">
        <p class="text-secondary small mb-1">Total Documents</p>
        <h3 class="fw-bold mb-0"><?= number_format($totalDocuments) ?></h3>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body">
        <p class="text-secondary small mb-1">Total Revenue</p>
        <h3 class="fw-bold mb-0"><?= money($totalRevenue) ?></h3>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body">
        <p class="text-secondary small mb-1">Active Subscriptions</p>
        <h3 class="fw-bold mb-0"><?= number_format($activeSubscriptions) ?></h3>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-6">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <p class="text-secondary small mb-1">Open Support Tickets</p>
          <h4 class="fw-bold mb-0"><?= number_format($openTickets) ?></h4>
        </div>
        <a href="<?= url('admin/support-tickets') ?>" class="btn btn-outline-primary btn-sm">View</a>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <p class="text-secondary small mb-1">New Contact Messages</p>
          <h4 class="fw-bold mb-0"><?= number_format($newMessages) ?></h4>
        </div>
        <a href="<?= url('admin/contact-messages') ?>" class="btn btn-outline-primary btn-sm">View</a>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-md-6">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body">
        <h5 class="fw-bold mb-3">Recent Signups</h5>
        <?php if (empty($recentUsers)): ?>
          <p class="text-secondary mb-0">No users yet.</p>
        <?php else: ?>
        <ul class="list-unstyled mb-0">
          <?php foreach ($recentUsers as $u): ?>
            <li class="d-flex justify-content-between border-bottom py-2">
              <span><?= e($u['name']) ?> <span class="text-secondary small">(<?= e($u['email']) ?>)</span></span>
              <span class="badge bg-secondary text-capitalize"><?= e($u['plan']) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body">
        <h5 class="fw-bold mb-3">Recent Payments</h5>
        <?php if (empty($recentPayments)): ?>
          <p class="text-secondary mb-0">No payments yet.</p>
        <?php else: ?>
        <ul class="list-unstyled mb-0">
          <?php foreach ($recentPayments as $p): ?>
            <li class="d-flex justify-content-between border-bottom py-2">
              <span><?= e($p['user_name']) ?></span>
              <span><?= money((float) $p['amount']) ?> <span class="badge bg-<?= $p['status'] === 'completed' ? 'success' : 'secondary' ?>"><?= e($p['status']) ?></span></span>
            </li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body">
        <h5 class="fw-bold mb-3">Recent Activity</h5>
        <?php if (empty($recentActivity)): ?>
          <p class="text-secondary mb-0">No activity recorded yet.</p>
        <?php else: ?>
        <ul class="list-unstyled mb-0">
          <?php foreach ($recentActivity as $a): ?>
            <li class="d-flex justify-content-between border-bottom py-2 small">
              <span><?= e($a['user_name'] ?? 'System') ?> &middot; <?= e($a['action']) ?> <?= e($a['description'] ?? '') ?></span>
              <span class="text-secondary"><?= e(date('M j, g:i A', strtotime($a['created_at']))) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
