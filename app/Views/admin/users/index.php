<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <h3 class="fw-bold mb-0">Users</h3>
</div>

<form method="GET" action="<?= url('admin/users') ?>" class="mb-4">
  <div class="input-group" style="max-width:360px;">
    <input type="text" name="search" value="<?= e($search) ?>" class="form-control" placeholder="Search by name or email...">
    <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
  </div>
</form>

<div class="card border-0 shadow-sm rounded-4">
  <div class="card-body">
    <?php if (empty($users)): ?>
      <p class="text-secondary mb-0">No users found.</p>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Plan</th><th>Status</th><th>Joined</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><?= e($u['name']) ?></td>
            <td><?= e($u['email']) ?></td>
            <td class="text-capitalize"><?= e($u['role']) ?></td>
            <td class="text-capitalize"><?= e($u['plan']) ?></td>
            <td><?php if ((int) $u['is_banned'] === 1): ?><span class="badge bg-danger">Banned</span><?php else: ?><span class="badge bg-success">Active</span><?php endif; ?></td>
            <td class="text-secondary small"><?= e(date('M j, Y', strtotime($u['created_at']))) ?></td>
            <td class="text-end"><a href="<?= url('admin/users/' . $u['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Edit</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <nav class="mt-3">
      <ul class="pagination mb-0">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="<?= url('admin/users') ?>?page=<?= max(1, $page - 1) ?>&search=<?= urlencode($search) ?>">Previous</a></li>
        <li class="page-item"><span class="page-link"><?= $page ?></span></li>
        <li class="page-item <?= count($users) < 20 ? 'disabled' : '' ?>"><a class="page-link" href="<?= url('admin/users') ?>?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">Next</a></li>
      </ul>
    </nav>
    <?php endif; ?>
  </div>
</div>
