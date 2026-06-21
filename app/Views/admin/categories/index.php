<h3 class="fw-bold mb-4">Categories</h3>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body">
        <?php if (empty($categories)): ?>
          <p class="text-secondary mb-0">No categories yet.</p>
        <?php else: ?>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead><tr><th>Name</th><th>Slug</th><th>Templates</th><th>Sort</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($categories as $c): ?>
              <tr>
                <td><?= e($c['name']) ?></td>
                <td class="text-secondary small"><?= e($c['slug']) ?></td>
                <td><?= (int) $c['template_count'] ?></td>
                <td><?= (int) $c['sort_order'] ?></td>
                <td class="text-end">
                  <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCategory<?= $c['id'] ?>">Edit</button>
                  <form method="POST" action="<?= url('admin/categories/' . $c['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this category?');">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                  </form>
                </td>
              </tr>

              <div class="modal fade" id="editCategory<?= $c['id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                  <form method="POST" action="<?= url('admin/categories/' . $c['id']) ?>" class="modal-content">
                    <?= csrf_field() ?>
                    <div class="modal-header"><h5 class="modal-title">Edit Category</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                      <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="<?= e($c['name']) ?>" required></div>
                      <div class="mb-3"><label class="form-label">Description</label><input type="text" name="description" class="form-control" value="<?= e($c['description'] ?? '') ?>"></div>
                      <div class="mb-3"><label class="form-label">Icon (Bootstrap Icon class)</label><input type="text" name="icon" class="form-control" value="<?= e($c['icon'] ?? '') ?>"></div>
                      <div class="mb-3"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" value="<?= (int) $c['sort_order'] ?>"></div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-primary">Save</button></div>
                  </form>
                </div>
              </div>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body p-4">
        <h5 class="fw-bold mb-3">Add Category</h5>
        <form method="POST" action="<?= url('admin/categories') ?>">
          <?= csrf_field() ?>
          <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Description</label><input type="text" name="description" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Icon (Bootstrap Icon class)</label><input type="text" name="icon" class="form-control" placeholder="bi-file-earmark-text"></div>
          <div class="mb-3"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" value="0"></div>
          <button class="btn btn-primary">Create Category</button>
        </form>
      </div>
    </div>
  </div>
</div>
