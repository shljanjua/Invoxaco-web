<div class="container py-4" style="max-width:900px;">
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><?= e($document['title']) ?></h5>
    <a href="<?= url('share/' . $token . '/pdf') ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-file-pdf me-1"></i>Download PDF</a>
  </div>
  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
      <?= $html ?>
    </div>
  </div>
  <p class="text-secondary small text-center mt-4">This document was shared with you via <a href="<?= url() ?>">Invoxaco</a>.</p>
</div>
