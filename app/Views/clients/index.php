<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h3 class="fw-bold mb-0">My Clients</h3>
    <a href="<?= url('clients/create') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Client</a>
  </div>

  <form method="GET" action="<?= url('clients') ?>" class="mb-4">
    <div class="input-group" style="max-width:360px;">
      <input type="text" name="search" value="<?= e($search) ?>" class="form-control" placeholder="Search clients...">
      <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
    </div>
  </form>

  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">
      <?php if (empty($clients)): ?>
        <p class="text-secondary mb-0">No clients yet. <a href="<?= url('clients/create') ?>">Add your first client</a> to speed up document creation.</p>
      <?php else: ?>
      <div class="table-responsive">
        <table class="table align-middle">
          <thead><tr><th>Name</th><th>Email</th><th>Company</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($clients as $c): ?>
            <tr>
              <td><a href="<?= url('clients/' . $c['id']) ?>" class="text-decoration-none fw-bold"><?= e($c['name']) ?></a></td>
              <td><?= e($c['email'] ?? '—') ?></td>
              <td><?= e($c['company'] ?? '—') ?></td>
              <td class="text-end">
                <a href="<?= url('clients/' . $c['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                <form method="POST" action="<?= url('clients/' . $c['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this client?');">
                  <?= csrf_field() ?>
                  <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
