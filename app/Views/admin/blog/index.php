<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <h3 class="fw-bold mb-0">Blog Posts</h3>
  <a href="<?= url('admin/blog/create') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Post</a>
</div>

<div class="card border-0 shadow-sm rounded-4">
  <div class="card-body">
    <?php if (empty($posts)): ?>
      <p class="text-secondary mb-0">No posts yet.</p>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead><tr><th>Title</th><th>Category</th><th>Author</th><th>Status</th><th>Updated</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($posts as $p): ?>
          <tr>
            <td><?= e($p['title']) ?></td>
            <td><?= e($p['category_name'] ?? '—') ?></td>
            <td><?= e($p['author_name'] ?? '—') ?></td>
            <td><span class="badge bg-<?= $p['status'] === 'published' ? 'success' : 'secondary' ?> text-capitalize"><?= e($p['status']) ?></span></td>
            <td class="text-secondary small"><?= e(date('M j, Y', strtotime($p['updated_at']))) ?></td>
            <td class="text-end">
              <a href="<?= url('admin/blog/' . $p['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Edit</a>
              <form method="POST" action="<?= url('admin/blog/' . $p['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this post?');">
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
