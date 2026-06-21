<div class="container py-4" style="max-width:760px;">
  <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
      <h3 class="fw-bold mb-1"><?= e($ticket['subject']) ?></h3>
      <span class="badge bg-<?= $ticket['status'] === 'closed' ? 'secondary' : ($ticket['status'] === 'pending' ? 'warning' : 'success') ?>"><?= e($ticket['status']) ?></span>
    </div>
    <a href="<?= url('support') ?>" class="btn btn-outline-secondary btn-sm">Back to Tickets</a>
  </div>

  <div class="card border-0 shadow-sm rounded-4 mb-3">
    <div class="card-body">
      <p class="text-secondary small mb-1">You &middot; <?= e(date('M j, Y g:i A', strtotime($ticket['created_at']))) ?></p>
      <p class="mb-0"><?= nl2br(e($ticket['message'])) ?></p>
    </div>
  </div>

  <?php foreach ($replies as $reply): ?>
  <div class="card border-0 shadow-sm rounded-4 mb-3 <?= $reply['is_admin'] ? 'bg-primary-subtle' : '' ?>">
    <div class="card-body">
      <p class="text-secondary small mb-1"><?= $reply['is_admin'] ? 'Invoxaco Support' : 'You' ?> &middot; <?= e(date('M j, Y g:i A', strtotime($reply['created_at']))) ?></p>
      <p class="mb-0"><?= nl2br(e($reply['message'])) ?></p>
    </div>
  </div>
  <?php endforeach; ?>

  <?php if ($ticket['status'] !== 'closed'): ?>
  <div class="card border-0 shadow-sm rounded-4 mt-4">
    <div class="card-body p-4">
      <form method="POST" action="<?= url('support/' . $ticket['id'] . '/reply') ?>">
        <?= csrf_field() ?>
        <textarea name="message" rows="3" class="form-control mb-2" placeholder="Write a reply..." required></textarea>
        <button class="btn btn-primary">Send Reply</button>
      </form>
    </div>
  </div>
  <?php endif; ?>
</div>
