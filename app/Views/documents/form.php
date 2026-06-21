<?php
/** @var array $template */
/** @var array $fields */
/** @var array|null $document */
/** @var array $documentData */
/** @var array $clients */
/** @var string $formAction */
$documentData = $documentData ?? [];
$isEdit = $document !== null;
?>
<div class="container py-4" style="max-width:900px;">
  <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
    <div>
      <h3 class="fw-bold mb-1"><?= $isEdit ? 'Edit' : 'Create' ?> <?= e($template['name']) ?></h3>
      <span id="autosave-status" class="text-secondary small"></span>
    </div>
    <?php if ($isEdit): ?>
    <div class="d-flex gap-2">
      <a href="<?= url('documents/' . $document['id'] . '/print') ?>" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer"></i> Print</a>
      <a href="<?= url('documents/' . $document['id'] . '/pdf') ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-file-pdf"></i> PDF</a>
      <a href="<?= url('documents/' . $document['id'] . '/docx') ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-file-word"></i> DOCX</a>
      <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#emailModal"><i class="bi bi-envelope"></i> Email</button>
      <button type="button" class="btn btn-outline-info btn-sm" id="shareBtn" data-id="<?= $document['id'] ?>"><i class="bi bi-link-45deg"></i> Share</button>
      <form method="POST" action="<?= url('documents/' . $document['id'] . '/duplicate') ?>" class="d-inline"><?= csrf_field() ?><button class="btn btn-outline-secondary btn-sm"><i class="bi bi-copy"></i> Duplicate</button></form>
      <form method="POST" action="<?= url('documents/' . $document['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this document?');"><?= csrf_field() ?><button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button></form>
    </div>
    <?php endif; ?>
  </div>
  <div id="shareResult" class="alert alert-info d-none"></div>

  <form method="POST" action="<?= e($formAction) ?>" id="documentForm">
    <?= csrf_field() ?>
    <input type="hidden" name="template_slug" value="<?= e($template['slug']) ?>">

    <div class="card border-0 shadow-sm rounded-4 mb-3">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Document Title</label>
            <input type="text" name="title" class="form-control" value="<?= e($document['title'] ?? $template['name']) ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Client (optional)</label>
            <select name="client_id" class="form-select">
              <option value="">— No client —</option>
              <?php foreach ($clients as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($document['client_id'] ?? null) == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-3">
      <div class="card-body">
        <?php foreach ($fields as $field): ?>
          <?php $name = $field['name']; $value = $documentData[$name] ?? ''; ?>
          <div class="mb-3">
            <?php if ($field['type'] === 'line_items'): ?>
              <label class="form-label fw-bold"><?= e($field['label']) ?></label>
              <div id="lineItems">
                <?php $items = is_array($value) && !empty($value) ? $value : [['description' => '', 'qty' => 1, 'price' => 0]]; ?>
                <?php foreach ($items as $item): ?>
                <div class="row g-2 mb-2 line-item">
                  <div class="col-5"><input type="text" name="items_description[]" class="form-control" placeholder="Description" value="<?= e($item['description'] ?? '') ?>"></div>
                  <div class="col-2"><input type="number" step="0.01" name="items_qty[]" class="form-control" placeholder="Qty" value="<?= e((string) ($item['qty'] ?? 1)) ?>"></div>
                  <div class="col-3"><input type="number" step="0.01" name="items_price[]" class="form-control" placeholder="Price" value="<?= e((string) ($item['price'] ?? 0)) ?>"></div>
                  <div class="col-2"><button type="button" class="btn btn-outline-danger w-100 remove-item"><i class="bi bi-x"></i></button></div>
                </div>
                <?php endforeach; ?>
              </div>
              <button type="button" id="addItem" class="btn btn-sm btn-outline-primary mt-1"><i class="bi bi-plus"></i> Add Line</button>
            <?php elseif ($field['type'] === 'textarea'): ?>
              <label class="form-label"><?= e($field['label']) ?></label>
              <textarea name="<?= e($name) ?>" class="form-control" rows="3" <?= $field['required'] ? 'required' : '' ?>><?= e(is_array($value) ? '' : (string) $value) ?></textarea>
            <?php elseif ($field['type'] === 'date'): ?>
              <label class="form-label"><?= e($field['label']) ?></label>
              <input type="date" name="<?= e($name) ?>" class="form-control" value="<?= e((string) $value) ?>" <?= $field['required'] ? 'required' : '' ?>>
            <?php elseif ($field['type'] === 'number'): ?>
              <label class="form-label"><?= e($field['label']) ?></label>
              <input type="number" step="0.01" name="<?= e($name) ?>" class="form-control" value="<?= e((string) $value) ?>" <?= $field['required'] ? 'required' : '' ?>>
            <?php else: ?>
              <label class="form-label"><?= e($field['label']) ?></label>
              <input type="text" name="<?= e($name) ?>" class="form-control" value="<?= e((string) $value) ?>" <?= $field['required'] ? 'required' : '' ?>>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="d-flex gap-2">
      <button type="submit" name="status" value="draft" class="btn btn-outline-primary">Save Draft</button>
      <button type="submit" name="status" value="final" class="btn btn-primary">Save &amp; Finalize</button>
    </div>
  </form>
</div>

<?php if ($isEdit): ?>
<div class="modal fade" id="emailModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="<?= url('documents/' . $document['id'] . '/email') ?>" class="modal-content">
      <?= csrf_field() ?>
      <div class="modal-header"><h5 class="modal-title">Email this document</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-3"><label class="form-label">Recipient Email</label><input type="email" name="to_email" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Recipient Name</label><input type="text" name="to_name" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Format</label>
          <select name="format" class="form-select"><option value="pdf">PDF</option><option value="docx">DOCX</option></select>
        </div>
        <div class="mb-3"><label class="form-label">Message (optional)</label><textarea name="message" class="form-control" rows="3"></textarea></div>
      </div>
      <div class="modal-footer"><button class="btn btn-primary">Send</button></div>
    </form>
  </div>
</div>
<?php endif; ?>

<script>
document.getElementById('addItem')?.addEventListener('click', function () {
  const container = document.getElementById('lineItems');
  const row = container.querySelector('.line-item').cloneNode(true);
  row.querySelectorAll('input').forEach(i => i.value = i.type === 'number' ? '' : '');
  container.appendChild(row);
});
document.addEventListener('click', function (e) {
  if (e.target.closest('.remove-item')) {
    const items = document.querySelectorAll('.line-item');
    if (items.length > 1) e.target.closest('.line-item').remove();
  }
});

<?php if ($isEdit): ?>
const csrfToken = document.querySelector('input[name="_csrf"]').value;
let autosaveTimer;
document.getElementById('documentForm').addEventListener('input', function () {
  clearTimeout(autosaveTimer);
  autosaveTimer = setTimeout(autosave, 1500);
});
function autosave() {
  const form = document.getElementById('documentForm');
  const data = new FormData(form);
  fetch('<?= url('documents/' . $document['id'] . '/autosave') ?>', {
    method: 'POST',
    body: data,
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  }).then(r => r.json()).then(res => {
    document.getElementById('autosave-status').textContent = res.ok ? ('Autosaved at ' + res.saved_at) : '';
  }).catch(() => {});
}

document.getElementById('shareBtn')?.addEventListener('click', function () {
  fetch('<?= url('documents/' . $document['id'] . '/share') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: '_csrf=' + encodeURIComponent(csrfToken)
  }).then(r => r.json()).then(res => {
    if (res.ok) {
      const box = document.getElementById('shareResult');
      box.classList.remove('d-none');
      box.innerHTML = 'Shareable link: <a href="' + res.url + '" target="_blank">' + res.url + '</a>';
    }
  });
});
<?php endif; ?>
</script>
