<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h3 class="fw-bold mb-0">My Documents</h3>
    <a href="<?= url('generators') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Document</a>
  </div>

  <form method="GET" action="<?= url('documents') ?>" class="mb-4">
    <div class="input-group" style="max-width:360px;">
      <input type="text" name="search" value="<?= e($search) ?>" class="form-control" placeholder="Search documents...">
      <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
    </div>
  </form>

  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">
      <?php if (empty($documents)): ?>
        <p class="text-secondary mb-0">
          <?php if ($search !== ''): ?>
            No documents match "<?= e($search) ?>".
          <?php else: ?>
            You haven't created any documents yet. <a href="<?= url('generators') ?>">Browse generators</a> to get started.
          <?php endif; ?>
        </p>
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
              <td class="text-end">
                <div class="btn-group">
                  <a href="<?= url('documents/' . $doc['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Open</a>
                  <a href="<?= url('documents/' . $doc['id'] . '/pdf') ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-pdf"></i></a>
                  <form method="POST" action="<?= url('documents/' . $doc['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this document?');">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                  </form>
                </div>
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
