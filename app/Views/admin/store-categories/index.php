<?php
/** @var array $categories */
?>
<h3 class="fw-bold mb-4">Store Categories</h3>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body">
        <?php if (empty($categories)): ?>
          <p class="text-secondary mb-0">No categories yet. Add one on the right.</p>
        <?php else: ?>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead><tr><th>Name</th><th>Slug</th><th>Order</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($categories as $c): ?>
              <tr>
                <td>
                  <form method="POST" action="<?= url('admin/store-categories/' . $c['id']) ?>" class="row g-1 align-items-center">
                    <?= csrf_field() ?>
                    <div class="col"><input type="text" name="name" class="form-control form-control-sm" value="<?= e($c['name']) ?>"></div>
                </td>
                <td><input type="text" name="slug" class="form-control form-control-sm" value="<?= e($c['slug']) ?>" style="min-width:140px;"></td>
                <td><input type="number" name="sort_order" class="form-control form-control-sm" value="<?= (int) $c['sort_order'] ?>" style="width:74px;"></td>
                <td class="text-end text-nowrap">
                    <button class="btn btn-sm btn-outline-primary">Save</button>
                  </form>
                  <form method="POST" action="<?= url('admin/store-categories/' . $c['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this category? Products keep their files but lose this category.');">
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

  <div class="col-lg-5">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body">
        <h6 class="fw-bold mb-3">Add Category</h6>
        <form method="POST" action="<?= url('admin/store-categories') ?>">
          <?= csrf_field() ?>
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Slug <span class="text-secondary small">(optional)</span></label>
            <input type="text" name="slug" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <input type="text" name="description" class="form-control" maxlength="255">
          </div>
          <div class="mb-3">
            <label class="form-label">Sort order</label>
            <input type="number" name="sort_order" class="form-control" value="0">
          </div>
          <button class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i>Add Category</button>
        </form>
      </div>
    </div>
  </div>
</div>
