<div class="container py-4" style="max-width:900px;">
  <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
      <h3 class="fw-bold mb-1"><?= e($client['name']) ?></h3>
      <p class="text-secondary mb-0"><?= e($client['company'] ?? '') ?></p>
    </div>
    <a href="<?= url('clients/' . $client['id'] . '/edit') ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil"></i> Edit</a>
  </div>

  <div class="row g-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
          <h6 class="fw-bold mb-3">Contact Details</h6>
          <p class="small mb-1"><i class="bi bi-envelope me-2 text-secondary"></i><?= e($client['email'] ?? '—') ?></p>
          <p class="small mb-1"><i class="bi bi-telephone me-2 text-secondary"></i><?= e($client['phone'] ?? '—') ?></p>
          <p class="small mb-1"><i class="bi bi-geo-alt me-2 text-secondary"></i><?= e($client['address'] ?? '—') ?></p>
          <?php if (!empty($client['notes'])): ?>
            <hr>
            <p class="small text-secondary mb-0"><?= e($client['notes']) ?></p>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
          <h6 class="fw-bold mb-3">Documents</h6>
          <?php if (empty($documents)): ?>
            <p class="text-secondary small mb-0">No documents created for this client yet.</p>
          <?php else: ?>
          <div class="table-responsive">
            <table class="table align-middle">
              <thead><tr><th>Title</th><th>Type</th><th>Status</th><th></th></tr></thead>
              <tbody>
              <?php foreach ($documents as $doc): ?>
                <tr>
                  <td><?= e($doc['title']) ?></td>
                  <td><?= e($doc['template_name']) ?></td>
                  <td><span class="badge bg-<?= $doc['status'] === 'final' ? 'success' : 'secondary' ?>"><?= e($doc['status']) ?></span></td>
                  <td class="text-end"><a href="<?= url('documents/' . $doc['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Open</a></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
