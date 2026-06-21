<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
  <div>
    <h3 class="fw-bold mb-1"><?= e($ticket['subject']) ?></h3>
    <span class="text-secondary small">From <?= e($ticket['user_name'] ?? '') ?></span>
  </div>
  <a href="<?= url('admin/support-tickets') ?>" class="btn btn-outline-secondary btn-sm">Back to Tickets</a>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-3" style="max-width:760px;">
  <div class="card-body">
    <p class="text-secondary small mb-1">User &middot; <?= e(date('M j, Y g:i A', strtotime($ticket['created_at']))) ?></p>
    <p class="mb-0"><?= nl2br(e($ticket['message'])) ?></p>
  </div>
</div>

<?php foreach ($replies as $reply): ?>
<div class="card border-0 shadow-sm rounded-4 mb-3 <?= $reply['is_admin'] ? 'bg-primary-subtle' : '' ?>" style="max-width:760px;">
  <div class="card-body">
    <p class="text-secondary small mb-1"><?= $reply['is_admin'] ? 'Invoxaco Support (you)' : 'User' ?> &middot; <?= e(date('M j, Y g:i A', strtotime($reply['created_at']))) ?></p>
    <p class="mb-0"><?= nl2br(e($reply['message'])) ?></p>
  </div>
</div>
<?php endforeach; ?>

<div class="card border-0 shadow-sm rounded-4 mt-4" style="max-width:760px;">
  <div class="card-body p-4">
    <div class="d-flex align-items-center gap-2 mb-3">
      <span class="fw-bold">Status:</span>
      <form method="POST" action="<?= url('admin/support-tickets/' . $ticket['id'] . '/status') ?>" class="d-flex gap-2">
        <?= csrf_field() ?>
        <select name="status" class="form-select form-select-sm" style="width:auto;">
          <?php foreach (['open', 'pending', 'closed'] as $s): ?>
            <option value="<?= $s ?>" <?= $ticket['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
          <?php endforeach; ?>
        </select>
        <button class="btn btn-sm btn-primary">Update</button>
      </form>
    </div>
    <?php if ($ticket['status'] !== 'closed'): ?>
    <form method="POST" action="<?= url('admin/support-tickets/' . $ticket['id'] . '/reply') ?>">
      <?= csrf_field() ?>
      <textarea name="message" rows="3" class="form-control mb-2" placeholder="Write a reply..." required></textarea>
      <button class="btn btn-primary">Send Reply</button>
    </form>
    <?php endif; ?>
  </div>
</div>
