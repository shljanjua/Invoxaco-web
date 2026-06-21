<div class="container py-4" style="max-width:800px;">
  <h3 class="fw-bold mb-1"><?= e($team['name']) ?></h3>
  <p class="text-secondary mb-4"><?= $plan['team_members'] === null ? 'Unlimited team members' : count($members) . ' / ' . $plan['team_members'] . ' team members used' ?></p>

  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
      <h6 class="fw-bold mb-3">Invite a Team Member</h6>
      <form method="POST" action="<?= url('team/invite') ?>" class="d-flex flex-wrap gap-2">
        <?= csrf_field() ?>
        <input type="email" name="email" required class="form-control" style="max-width:320px;" placeholder="teammate@company.com">
        <button class="btn btn-primary">Send Invite</button>
      </form>
    </div>
  </div>

  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">
      <h6 class="fw-bold mb-3">Members</h6>
      <?php if (empty($members)): ?>
        <p class="text-secondary small mb-0">No team members yet.</p>
      <?php else: ?>
      <div class="table-responsive">
        <table class="table align-middle">
          <thead><tr><th>Email</th><th>Role</th><th>Status</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($members as $m): ?>
            <tr>
              <td><?= e($m['email']) ?></td>
              <td class="text-capitalize"><?= e($m['role']) ?></td>
              <td><span class="badge bg-<?= $m['status'] === 'active' ? 'success' : 'secondary' ?>"><?= e($m['status']) ?></span></td>
              <td class="text-end">
                <form method="POST" action="<?= url('team/' . $m['id'] . '/remove') ?>" onsubmit="return confirm('Remove this team member?');">
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
