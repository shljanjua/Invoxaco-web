<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
  <h3 class="fw-bold mb-0">Edit User</h3>
  <a href="<?= url('admin/users') ?>" class="btn btn-outline-secondary btn-sm">Back to Users</a>
</div>

<div class="card border-0 shadow-sm rounded-4" style="max-width:600px;">
  <div class="card-body p-4">
    <form method="POST" action="<?= url('admin/users/' . $user['id']) ?>">
      <?= csrf_field() ?>
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" class="form-control" value="<?= e($user['name']) ?>" disabled>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="text" class="form-control" value="<?= e($user['email']) ?>" disabled>
      </div>
      <div class="mb-3">
        <label class="form-label">Role</label>
        <select name="role" class="form-select">
          <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
          <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Plan</label>
        <select name="plan" class="form-select">
          <option value="free" <?= $user['plan'] === 'free' ? 'selected' : '' ?>>Free</option>
          <option value="pro" <?= $user['plan'] === 'pro' ? 'selected' : '' ?>>Pro</option>
          <option value="premium" <?= $user['plan'] === 'premium' ? 'selected' : '' ?>>Premium</option>
        </select>
      </div>
      <div class="form-check mb-3">
        <input type="checkbox" name="is_banned" class="form-check-input" id="is_banned" value="1" <?= (int) $user['is_banned'] === 1 ? 'checked' : '' ?>>
        <label class="form-check-label" for="is_banned">Banned (cannot log in)</label>
      </div>
      <button class="btn btn-primary">Save Changes</button>
    </form>
  </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mt-3" style="max-width:600px;">
  <div class="card-body p-4">
    <h6 class="fw-bold text-danger">Danger Zone</h6>
    <p class="text-secondary small">Deleting a user permanently removes their account and all associated data.</p>
    <form method="POST" action="<?= url('admin/users/' . $user['id'] . '/delete') ?>" onsubmit="return confirm('Permanently delete this user?');">
      <?= csrf_field() ?>
      <button class="btn btn-outline-danger btn-sm">Delete User</button>
    </form>
  </div>
</div>
