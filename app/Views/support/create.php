<div class="container py-4" style="max-width:700px;">
  <h3 class="fw-bold mb-4">New Support Ticket</h3>
  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
      <form method="POST" action="<?= url('support') ?>">
        <?= csrf_field() ?>
        <div class="mb-3">
          <label class="form-label">Subject</label>
          <input type="text" name="subject" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Message</label>
          <textarea name="message" rows="6" class="form-control" required></textarea>
        </div>
        <button class="btn btn-primary">Submit Ticket</button>
      </form>
    </div>
  </div>
</div>
