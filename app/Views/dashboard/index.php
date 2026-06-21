<?php
$currency = $user['currency'] ?? 'USD';
$changeUp = $monthChange >= 0;
?>
<div class="row g-4 mb-4">
  <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h2 class="fw-bold mb-1">Welcome back, <?= e(explode(' ', $user['name'])[0]) ?></h2>
      <p class="text-secondary mb-0">Plan: <span class="badge bg-primary text-uppercase"><?= e($plan['name']) ?></span>
        <?php if (!$user['email_verified_at']): ?>
          &middot; <span class="text-danger">Email not verified</span>
          <form method="POST" action="<?= url('verify-email/resend') ?>" class="d-inline">
            <?= csrf_field() ?>
            <button class="btn btn-link btn-sm p-0 align-baseline">Resend link</button>
          </form>
        <?php endif; ?>
      </p>
    </div>
    <div class="d-flex gap-2">
      <a href="<?= url('settings') ?>" class="btn btn-outline-secondary"><i class="bi bi-gear me-1"></i>Settings</a>
      <a href="<?= url('generators') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Document</a>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card border-0 shadow-sm rounded-4 h-100 kpi-card animate-in">
      <div class="card-body">
        <div class="text-secondary small">Total Documents</div>
        <div class="fs-3 fw-bold"><?= $totalDocuments ?></div>
        <div class="text-secondary small"><?= $remaining === null ? 'Unlimited on your plan' : $remaining . ' remaining this month' ?></div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm rounded-4 h-100 kpi-card animate-in" style="animation-delay:0.05s;">
      <div class="card-body">
        <div class="text-secondary small">This Month</div>
        <div class="fs-3 fw-bold d-flex align-items-center gap-2">
          <?= $thisMonthCount ?>
          <span class="badge bg-<?= $changeUp ? 'success' : 'danger' ?>-subtle text-<?= $changeUp ? 'success' : 'danger' ?> small">
            <i class="bi bi-arrow-<?= $changeUp ? 'up' : 'down' ?>"></i> <?= abs($monthChange) ?>%
          </span>
        </div>
        <div class="text-secondary small">vs <?= $lastMonthCount ?> last month</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm rounded-4 h-100 kpi-card animate-in" style="animation-delay:0.1s;">
      <div class="card-body">
        <div class="text-secondary small">Clients</div>
        <div class="fs-3 fw-bold"><a href="<?= url('clients') ?>" class="text-decoration-none"><?= $clientCount ?></a></div>
        <div class="text-secondary small">Track contacts and document history</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm rounded-4 h-100 kpi-card animate-in" style="animation-delay:0.15s;">
      <div class="card-body">
        <div class="text-secondary small">Plan</div>
        <?php if ($user['plan'] === 'free'): ?>
          <div class="fs-5 fw-bold"><a href="<?= url('pricing') ?>" class="text-decoration-none">Upgrade Plan</a></div>
          <div class="text-secondary small">Unlock premium templates &amp; more</div>
        <?php else: ?>
          <div class="fs-5 fw-bold"><a href="<?= url('billing/portal') ?>" class="text-decoration-none">Manage Billing</a></div>
          <div class="text-secondary small"><?= $user['plan_expires_at'] ? 'Renews ' . e(date('M j, Y', strtotime($user['plan_expires_at']))) : 'Active subscription' ?></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  <div class="col-md-8">
    <div class="card border-0 shadow-sm rounded-4 h-100 animate-in" style="animation-delay:0.2s;">
      <div class="card-body">
        <h5 class="fw-bold mb-3">Documents &amp; Value Over Time</h5>
        <canvas id="trendChart" height="110"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm rounded-4 h-100 animate-in" style="animation-delay:0.25s;">
      <div class="card-body">
        <h5 class="fw-bold mb-3">Draft vs Final</h5>
        <canvas id="statusChart" height="160"></canvas>
      </div>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm rounded-4 animate-in" style="animation-delay:0.3s;">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="fw-bold mb-0">Recent Documents</h5>
      <a href="<?= url('documents') ?>" class="small">View all</a>
    </div>
    <?php if (empty($documents)): ?>
      <p class="text-secondary mb-0">You haven't created any documents yet. <a href="<?= url('generators') ?>">Browse generators</a> to get started.</p>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead><tr><th>Title</th><th>Type</th><th>Status</th><th>Updated</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($documents as $doc): ?>
          <tr>
            <td><?= e($doc['title']) ?></td>
            <td><?= e($doc['template_name']) ?></td>
            <td><span class="badge bg-<?= $doc['status'] === 'final' ? 'success' : 'secondary' ?>"><?= e($doc['status']) ?></span></td>
            <td class="text-secondary small"><?= e(date('M j, Y', strtotime($doc['updated_at']))) ?></td>
            <td class="text-end"><a href="<?= url('documents/' . $doc['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Open</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
const monthLabels = <?= json_encode(array_column($monthlySeries, 'label')) ?>;
const monthCounts = <?= json_encode(array_column($monthlySeries, 'count')) ?>;
const monthValues = <?= json_encode(array_column($monthlySeries, 'value')) ?>;

new Chart(document.getElementById('trendChart'), {
  type: 'bar',
  data: {
    labels: monthLabels,
    datasets: [
      {
        type: 'line',
        label: 'Value (<?= e(currency_symbol($currency)) ?>)',
        data: monthValues,
        borderColor: '#16a34a',
        backgroundColor: 'rgba(22,163,74,0.1)',
        yAxisID: 'y1',
        tension: 0.35,
        fill: true,
      },
      {
        type: 'bar',
        label: 'Documents',
        data: monthCounts,
        backgroundColor: 'rgba(67,56,202,0.6)',
        yAxisID: 'y',
        borderRadius: 4,
      },
    ],
  },
  options: {
    responsive: true,
    interaction: { mode: 'index', intersect: false },
    scales: {
      y: { beginAtZero: true, position: 'left', ticks: { precision: 0 } },
      y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } },
    },
  },
});

new Chart(document.getElementById('statusChart'), {
  type: 'doughnut',
  data: {
    labels: ['Draft', 'Final'],
    datasets: [{
      data: [<?= (int) $statusBreakdown['draft'] ?>, <?= (int) $statusBreakdown['final'] ?>],
      backgroundColor: ['#94a3b8', '#4338ca'],
    }],
  },
  options: { responsive: true, plugins: { legend: { position: 'bottom' } } },
});
</script>
