<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <h3 class="fw-bold mb-0">Generators</h3>
  <a href="<?= url('admin/generators/create') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Generator</a>
</div>

<div class="card border-0 shadow-sm rounded-4">
  <div class="card-body">
    <?php if (empty($templates)): ?>
      <p class="text-secondary mb-0">No generators yet.</p>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead><tr><th>Name</th><th>Category</th><th>Plan</th><th>Built</th><th>Active</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($templates as $t): ?>
          <tr>
            <td><?= e($t['name']) ?><br><span class="text-secondary small"><?= e($t['slug']) ?></span></td>
            <td><?= e($t['category_name']) ?></td>
            <td class="text-capitalize"><?= e($t['plan_required']) ?></td>
            <td><?php if ((int) $t['is_built'] === 1): ?><span class="badge bg-success">Built</span><?php else: ?><span class="badge bg-secondary">Coming Soon</span><?php endif; ?></td>
            <td><?php if ((int) $t['is_active'] === 1): ?><span class="badge bg-success">Yes</span><?php else: ?><span class="badge bg-secondary">No</span><?php endif; ?></td>
            <td class="text-end">
              <a href="<?= url('admin/generators/' . $t['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Edit</a>
              <form method="POST" action="<?= url('admin/generators/' . $t['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this generator?');">
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
