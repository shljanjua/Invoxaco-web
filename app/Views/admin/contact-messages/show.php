<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
  <h3 class="fw-bold mb-0">Contact Message</h3>
  <a href="<?= url('admin/contact-messages') ?>" class="btn btn-outline-secondary btn-sm">Back to Messages</a>
</div>

<div class="card border-0 shadow-sm rounded-4" style="max-width:760px;">
  <div class="card-body p-4">
    <dl class="row mb-3">
      <dt class="col-sm-3">From</dt>
      <dd class="col-sm-9"><?= e($message['name']) ?> &lt;<?= e($message['email']) ?>&gt;</dd>
      <dt class="col-sm-3">Subject</dt>
      <dd class="col-sm-9"><?= e($message['subject'] ?? '—') ?></dd>
      <dt class="col-sm-3">Received</dt>
      <dd class="col-sm-9"><?= e(date('M j, Y g:i A', strtotime($message['created_at']))) ?></dd>
    </dl>
    <hr>
    <p class="mb-0"><?= nl2br(e($message['message'])) ?></p>
  </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mt-3" style="max-width:760px;">
  <div class="card-body p-4 d-flex align-items-center gap-3">
    <span class="fw-bold">Status:</span>
    <form method="POST" action="<?= url('admin/contact-messages/' . $message['id'] . '/status') ?>" class="d-flex gap-2">
      <?= csrf_field() ?>
      <select name="status" class="form-select form-select-sm" style="width:auto;">
        <?php foreach (['new', 'read', 'replied'] as $s): ?>
          <option value="<?= $s ?>" <?= $message['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn-sm btn-primary">Update</button>
    </form>
    <a href="mailto:<?= e($message['email']) ?>" class="btn btn-sm btn-outline-secondary ms-auto"><i class="bi bi-reply me-1"></i>Reply via Email</a>
  </div>
</div>
