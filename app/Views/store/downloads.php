<?php
/** @var array $grants */
?>
<div class="container py-5" style="max-width:900px;">
  <h1 class="fw-bold mb-1">My Downloads</h1>
  <p class="text-secondary mb-4">Every digital product you've purchased, ready to download anytime.</p>

  <?php if (empty($grants)): ?>
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body text-center py-5">
        <i class="bi bi-download display-4 text-secondary"></i>
        <p class="text-secondary mt-3 mb-3">You haven't purchased any products yet.</p>
        <a href="<?= url('store') ?>" class="btn btn-primary">Browse the Store</a>
      </div>
    </div>
  <?php else: ?>
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body p-0">
        <table class="table align-middle mb-0">
          <tbody>
          <?php foreach ($grants as $g): ?>
            <tr>
              <td style="width:64px;">
                <?php if (!empty($g['cover_image'])): ?>
                  <img src="<?= url('uploads/products/' . $g['cover_image']) ?>" style="width:48px;height:48px;object-fit:cover;border-radius:8px;">
                <?php else: ?>
                  <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:48px;height:48px;"><i class="bi bi-file-earmark-richtext text-secondary"></i></div>
                <?php endif; ?>
              </td>
              <td>
                <div class="fw-semibold"><?= e($g['product_name'] ?? 'Download') ?></div>
                <div class="text-secondary small">Purchased <?= e(date('M j, Y', strtotime($g['purchased_at']))) ?></div>
              </td>
              <td class="text-end">
                <?php if (!empty($g['product_id'])): ?>
                  <a href="<?= url('store/download/' . $g['token']) ?>" class="btn btn-sm btn-primary"><i class="bi bi-download me-1"></i>Download</a>
                <?php else: ?>
                  <span class="text-secondary small">No longer available</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>
</div>
