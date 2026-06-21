<?php
/** @var array $template */
/** @var array $fields */
/** @var array|null $document */
/** @var array $documentData */
/** @var array $clients */
/** @var string $formAction */
$documentData = $documentData ?? [];
$isEdit = $document !== null;

$termsFieldNames = [
    'notes', 'condition_notes', 'discussion_notes', 'additional_terms', 'key_terms',
    'payment_terms', 'termination_terms', 'vesting_terms', 'terms', 'policies', 'policy_details',
];

$itemsFields = [];
$termsFields = [];
$basicFields = [];

foreach ($fields as $field) {
    if ($field['type'] === 'line_items') {
        $itemsFields[] = $field;
    } elseif (in_array($field['name'], $termsFieldNames, true)) {
        $termsFields[] = $field;
    } else {
        $basicFields[] = $field;
    }
}

$hasItemsTab = !empty($itemsFields);
$hasTermsTab = !empty($termsFields);

$accentColor = $document['accent_color'] ?? '#2563eb';
$templateStyle = $document['template_style'] ?? 'modern';
$showLogo = $document === null || (bool) $document['show_logo'];

if (!function_exists('render_field')) {
function render_field(array $field, array $documentData): void {
    $name = $field['name'];
    $value = array_key_exists($name, $documentData) ? $documentData[$name] : ($field['default'] ?? '');
    ?>
    <div class="mb-3">
      <?php if ($field['type'] === 'line_items'): ?>
        <label class="form-label fw-bold"><?= e($field['label']) ?></label>
        <div class="repeater-rows" data-repeater="<?= e($name) ?>">
          <?php $items = is_array($value) && !empty($value) ? $value : [['description' => '', 'qty' => 1, 'price' => 0]]; ?>
          <?php foreach ($items as $item): ?>
          <div class="row g-2 mb-2 repeater-row">
            <div class="col-5"><input type="text" name="<?= e($name) ?>_description[]" class="form-control" placeholder="Description" value="<?= e($item['description'] ?? '') ?>"></div>
            <div class="col-2"><input type="number" step="0.01" name="<?= e($name) ?>_qty[]" class="form-control" placeholder="Qty" value="<?= e((string) ($item['qty'] ?? 1)) ?>"></div>
            <div class="col-3"><input type="number" step="0.01" name="<?= e($name) ?>_price[]" class="form-control" placeholder="Price" value="<?= e((string) ($item['price'] ?? 0)) ?>"></div>
            <div class="col-2"><button type="button" class="btn btn-outline-danger w-100 remove-row"><i class="bi bi-x"></i></button></div>
          </div>
          <?php endforeach; ?>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary mt-1 add-row" data-repeater-target="<?= e($name) ?>"><i class="bi bi-plus"></i> Add Line</button>
      <?php elseif ($field['type'] === 'group_list'): ?>
        <?php $columns = $field['columns'] ?? []; ?>
        <label class="form-label fw-bold"><?= e($field['label']) ?></label>
        <?php if (!empty($field['help'])): ?><div class="form-text mb-2"><?= e($field['help']) ?></div><?php endif; ?>
        <div class="repeater-rows" data-repeater="<?= e($name) ?>">
          <?php $rows = is_array($value) && !empty($value) ? $value : [[]]; ?>
          <?php foreach ($rows as $row): ?>
          <div class="row g-2 mb-2 repeater-row border rounded-3 p-2">
            <?php foreach ($columns as $col): ?>
              <div class="col-md<?= ($col['type'] ?? 'text') === 'textarea' ? '-12' : '' ?>">
                <?php if (($col['type'] ?? 'text') === 'textarea'): ?>
                  <textarea name="<?= e($name) ?>_<?= e($col['name']) ?>[]" class="form-control mb-1" rows="2" placeholder="<?= e($col['label']) ?>"><?= e($row[$col['name']] ?? '') ?></textarea>
                <?php else: ?>
                  <input type="text" name="<?= e($name) ?>_<?= e($col['name']) ?>[]" class="form-control mb-1" placeholder="<?= e($col['label']) ?>" value="<?= e($row[$col['name']] ?? '') ?>">
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
            <div class="col-12 text-end"><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-x"></i> Remove</button></div>
          </div>
          <?php endforeach; ?>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary mt-1 add-row" data-repeater-target="<?= e($name) ?>"><i class="bi bi-plus"></i> Add Entry</button>
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
    <?php
}
}
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
            <input type="text" name="title" class="form-control" value="<?= e($document['title'] ?? doc_title($template['name'])) ?>">
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
      <div class="card-header bg-white border-0 pt-3 pb-0">
        <ul class="nav nav-tabs card-header-tabs" id="generatorTabs">
          <li class="nav-item"><button type="button" class="nav-link active" data-tab="basic">Basic</button></li>
          <?php if ($hasItemsTab): ?>
          <li class="nav-item"><button type="button" class="nav-link" data-tab="items">Items</button></li>
          <?php endif; ?>
          <li class="nav-item"><button type="button" class="nav-link" data-tab="design">Design</button></li>
          <?php if ($hasTermsTab): ?>
          <li class="nav-item"><button type="button" class="nav-link" data-tab="terms">Terms</button></li>
          <?php endif; ?>
        </ul>
      </div>
      <div class="card-body">
        <div class="tab-pane" data-pane="basic">
          <?php foreach ($basicFields as $field): render_field($field, $documentData); endforeach; ?>
        </div>

        <?php if ($hasItemsTab): ?>
        <div class="tab-pane d-none" data-pane="items">
          <?php foreach ($itemsFields as $field): render_field($field, $documentData); endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="tab-pane d-none" data-pane="design">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Accent Color</label>
              <input type="color" name="accent_color" id="accentColorInput" class="form-control form-control-color w-100" value="<?= e($accentColor) ?>">
              <div class="d-flex flex-wrap gap-2 mt-2" id="accentSwatches">
                <?php
                $presetColors = ['#2563eb', '#4338ca', '#7c3aed', '#db2777', '#dc2626', '#ea580c', '#d97706', '#16a34a', '#0d9488', '#0891b2', '#475569', '#111827'];
                foreach ($presetColors as $pc): ?>
                <button type="button" class="accent-swatch" data-color="<?= e($pc) ?>" style="width:24px;height:24px;border-radius:50%;border:2px solid #fff;outline:1px solid #e5e7eb;background-color:<?= e($pc) ?>;cursor:pointer;"></button>
                <?php endforeach; ?>
              </div>
              <div class="form-text">Used for headings and totals in the PDF.</div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Template Style</label>
              <select name="template_style" class="form-select">
                <option value="modern" <?= $templateStyle === 'modern' ? 'selected' : '' ?>>Modern</option>
                <option value="classic" <?= $templateStyle === 'classic' ? 'selected' : '' ?>>Classic</option>
                <option value="minimal" <?= $templateStyle === 'minimal' ? 'selected' : '' ?>>Minimal</option>
                <option value="bold" <?= $templateStyle === 'bold' ? 'selected' : '' ?>>Bold</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label d-block">Logo</label>
              <div class="form-check form-switch mt-2">
                <input class="form-check-input" type="checkbox" name="show_logo" id="showLogoSwitch" <?= $showLogo ? 'checked' : '' ?>>
                <label class="form-check-label" for="showLogoSwitch">Show company logo on this document</label>
              </div>
              <div class="form-text">Manage your logo in <a href="<?= url('settings') ?>">Company Settings</a>.</div>
            </div>
          </div>
        </div>

        <?php if ($hasTermsTab): ?>
        <div class="tab-pane d-none" data-pane="terms">
          <?php foreach ($termsFields as $field): render_field($field, $documentData); endforeach; ?>
        </div>
        <?php endif; ?>
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
document.querySelectorAll('#generatorTabs .nav-link').forEach(function (btn) {
  btn.addEventListener('click', function () {
    document.querySelectorAll('#generatorTabs .nav-link').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.add('d-none'));
    btn.classList.add('active');
    document.querySelector('.tab-pane[data-pane="' + btn.dataset.tab + '"]').classList.remove('d-none');
  });
});

document.addEventListener('click', function (e) {
  const swatch = e.target.closest('.accent-swatch');
  if (swatch) {
    document.getElementById('accentColorInput').value = swatch.dataset.color;
    document.getElementById('accentColorInput').dispatchEvent(new Event('input', { bubbles: true }));
    return;
  }

  const addBtn = e.target.closest('.add-row');
  if (addBtn) {
    const target = addBtn.dataset.repeaterTarget;
    const container = document.querySelector('.repeater-rows[data-repeater="' + target + '"]');
    if (!container) return;
    const rows = container.querySelectorAll('.repeater-row');
    const row = rows[rows.length - 1].cloneNode(true);
    row.querySelectorAll('input, textarea').forEach(function (field) {
      if (field.type === 'number') {
        field.value = /_qty\[\]$/.test(field.name) ? 1 : '';
      } else {
        field.value = '';
      }
    });
    container.appendChild(row);
    return;
  }

  const removeBtn = e.target.closest('.remove-row');
  if (removeBtn) {
    const container = removeBtn.closest('.repeater-rows');
    if (container && container.querySelectorAll('.repeater-row').length > 1) {
      removeBtn.closest('.repeater-row').remove();
    }
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
