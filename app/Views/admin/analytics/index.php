<h3 class="fw-bold mb-4">Analytics</h3>

<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body">
        <p class="text-secondary small mb-1">Total Users</p>
        <h3 class="fw-bold mb-0"><?= number_format($totalUsers) ?></h3>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body">
        <p class="text-secondary small mb-1">Total Documents</p>
        <h3 class="fw-bold mb-0"><?= number_format($totalDocuments) ?></h3>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body">
        <p class="text-secondary small mb-1">Total Generators</p>
        <h3 class="fw-bold mb-0"><?= number_format($totalTemplates) ?></h3>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-md-4">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body">
        <h5 class="fw-bold mb-3">Users by Plan</h5>
        <?php if (empty($usersByPlan)): ?>
          <p class="text-secondary mb-0">No data yet.</p>
        <?php else: ?>
        <ul class="list-unstyled mb-0">
          <?php foreach ($usersByPlan as $row): ?>
            <li class="d-flex justify-content-between border-bottom py-2 text-capitalize"><span><?= e($row['plan']) ?></span><span class="fw-bold"><?= (int) $row['c'] ?></span></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body">
        <h5 class="fw-bold mb-3">Documents per Month</h5>
        <?php if (empty($documentsByMonth)): ?>
          <p class="text-secondary mb-0">No data yet.</p>
        <?php else: ?>
        <ul class="list-unstyled mb-0">
          <?php foreach ($documentsByMonth as $row): ?>
            <li class="d-flex justify-content-between border-bottom py-2"><span><?= e($row['month']) ?></span><span class="fw-bold"><?= (int) $row['c'] ?></span></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body">
        <h5 class="fw-bold mb-3">Top Generators</h5>
        <?php if (empty($topTemplates)): ?>
          <p class="text-secondary mb-0">No data yet.</p>
        <?php else: ?>
        <ul class="list-unstyled mb-0">
          <?php foreach ($topTemplates as $row): ?>
            <li class="d-flex justify-content-between border-bottom py-2"><span><?= e($row['name']) ?></span><span class="fw-bold"><?= (int) $row['uses'] ?></span></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mt-4">
  <div class="card-body">
    <h6 class="fw-bold">Tracking Scripts</h6>
    <p class="text-secondary small mb-0">Configure your Google Analytics and Facebook Pixel IDs under <a href="<?= url('admin/settings') ?>">Website Settings</a> to enable tracking on public pages.</p>
  </div>
</div>
