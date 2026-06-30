<?php
/** @var array $products */
?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <h3 class="fw-bold mb-0">Digital Store Products</h3>
  <a href="<?= url('admin/products/create') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Product</a>
</div>

<div class="card border-0 shadow-sm rounded-4">
  <div class="card-body">
    <?php if (empty($products)): ?>
      <p class="text-secondary mb-0">No products yet. Click “New Product” to upload your first e-book or template.</p>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead><tr><th>Product</th><th>Category</th><th>Type</th><th>Price</th><th>File</th><th>Active</th><th>Sales</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($products as $p): ?>
          <tr>
            <td>
              <div class="d-flex align-items-center gap-2">
                <?php if (!empty($p['cover_image'])): ?>
                  <img src="<?= url('uploads/products/' . $p['cover_image']) ?>" style="width:40px;height:40px;object-fit:cover;border-radius:6px;">
                <?php else: ?>
                  <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="bi bi-file-earmark text-secondary"></i></div>
                <?php endif; ?>
                <div><?= e($p['name']) ?><br><span class="text-secondary small"><?= e($p['slug']) ?></span></div>
              </div>
            </td>
            <td><?= e($p['category_name'] ?? '—') ?></td>
            <td class="text-capitalize"><?= e($p['type']) ?></td>
            <td>
              <?php if (($p['pricing_model'] ?? 'fixed') === 'pwyw'): ?>
                <?= (float) $p['price'] > 0 ? money((float) $p['price'], $p['currency']) . '+' : 'Name your price' ?>
                <br><span class="badge bg-info text-dark">Pay what you want</span>
              <?php else: ?>
                <?php if ((float) $p['price'] <= 0): ?><span class="text-success">Free</span><?php else: ?><?= money((float) $p['price'], $p['currency']) ?><?php endif; ?>
                <?php if ($p['sale_price'] !== null): ?><br><span class="badge bg-warning text-dark">Sale <?= money((float) $p['sale_price'], $p['currency']) ?></span><?php endif; ?>
              <?php endif; ?>
            </td>
            <td><?php if (!empty($p['file_path'])): ?><i class="bi bi-check-circle text-success"></i> <?= e($p['file_name']) ?><?php else: ?><span class="badge bg-danger">No file</span><?php endif; ?></td>
            <td><?php if ((int) $p['is_active'] === 1): ?><span class="badge bg-success">Yes</span><?php else: ?><span class="badge bg-secondary">No</span><?php endif; ?></td>
            <td><?= (int) $p['downloads_count'] ?></td>
            <td class="text-end text-nowrap">
              <a href="<?= url('admin/products/' . $p['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Edit</a>
              <form method="POST" action="<?= url('admin/products/' . $p['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this product and its file?');">
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
