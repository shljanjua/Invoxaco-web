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
    'special_clauses', 'maintenance_terms', 'house_rules', 'terms_conditions', 'validity_terms',
    'cancellation_policy', 'renewal_terms',
];

$itemsFields = [];
$partyFields = [];
$signatureFields = [];
$termsFields = [];
$mediaFields = [];
$basicFields = [];

foreach ($fields as $field) {
    if ($field['type'] === 'line_items' || $field['type'] === 'group_list') {
        $itemsFields[] = $field;
    } elseif (in_array($field['type'], ['image', 'gallery', 'table', 'chart'], true)) {
        $mediaFields[] = $field;
    } elseif ($field['type'] === 'party') {
        $partyFields[] = $field;
    } elseif ($field['type'] === 'signature') {
        $signatureFields[] = $field;
    } elseif (in_array($field['name'], $termsFieldNames, true)) {
        $termsFields[] = $field;
    } else {
        $basicFields[] = $field;
    }
}

$hasItemsTab = !empty($itemsFields);
$hasPartiesTab = !empty($partyFields) || !empty($signatureFields);
$hasTermsTab = !empty($termsFields);
$hasMediaTab = !empty($mediaFields);

$accentColor = $document['accent_color'] ?? '#2563eb';
$templateStyle = $document['template_style'] ?? 'modern';
$fontFamily = $document['font_family'] ?? 'sans';
$fontScale = $document['font_scale'] ?? 'normal';
$headingColor = $document['heading_color'] ?? '#111827';
$bodyColor = $document['body_color'] ?? '#1f2937';
$showLogo = $document === null || (bool) $document['show_logo'];
$showStamp = $document !== null && (bool) $document['show_stamp'];

if (!function_exists('render_field')) {
function render_field(array $field, array $documentData): void {
    $name = $field['name'];
    $value = array_key_exists($name, $documentData) ? $documentData[$name] : ($field['default'] ?? '');
    ?>
    <div class="mb-3">
      <?php if ($field['type'] === 'line_items'): ?>
        <?php
        $hasUnit = !empty($field['has_unit']);
        $hasItemDiscount = !empty($field['per_item_discount']);
        $hasItemTax = !empty($field['per_item_tax']);
        ?>
        <label class="form-label fw-bold"><?= e($field['label']) ?></label>
        <div class="repeater-rows" data-repeater="<?= e($name) ?>">
          <?php $items = is_array($value) && !empty($value) ? $value : [['description' => '', 'unit' => '', 'qty' => 1, 'price' => 0, 'discount_percent' => 0, 'tax_percent' => 0]]; ?>
          <?php foreach ($items as $item): ?>
          <div class="row g-2 mb-2 repeater-row">
            <div class="col">
              <input type="text" name="<?= e($name) ?>_description[]" class="form-control" placeholder="Description" value="<?= e($item['description'] ?? '') ?>">
            </div>
            <?php if ($hasUnit): ?>
            <div class="col-2"><input type="text" name="<?= e($name) ?>_unit[]" class="form-control" placeholder="Unit" value="<?= e($item['unit'] ?? '') ?>"></div>
            <?php endif; ?>
            <div class="col-2"><input type="number" step="0.01" name="<?= e($name) ?>_qty[]" class="form-control" placeholder="Qty" value="<?= e((string) ($item['qty'] ?? 1)) ?>"></div>
            <div class="col-2"><input type="number" step="0.01" name="<?= e($name) ?>_price[]" class="form-control" placeholder="Price" value="<?= e((string) ($item['price'] ?? 0)) ?>"></div>
            <?php if ($hasItemDiscount): ?>
            <div class="col-2"><input type="number" step="0.01" name="<?= e($name) ?>_discount_percent[]" class="form-control" placeholder="Disc %" value="<?= e((string) ($item['discount_percent'] ?? 0)) ?>"></div>
            <?php endif; ?>
            <?php if ($hasItemTax): ?>
            <div class="col-2"><input type="number" step="0.01" name="<?= e($name) ?>_tax_percent[]" class="form-control" placeholder="Tax %" value="<?= e((string) ($item['tax_percent'] ?? 0)) ?>"></div>
            <?php endif; ?>
            <div class="col-1"><button type="button" class="btn btn-outline-danger w-100 remove-row"><i class="bi bi-x"></i></button></div>
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
              <?php $colType = $col['type'] ?? 'text'; ?>
              <div class="col-md<?= $colType === 'textarea' ? '-12' : '' ?>">
                <?php if ($colType === 'textarea'): ?>
                  <textarea name="<?= e($name) ?>_<?= e($col['name']) ?>[]" class="form-control mb-1" rows="2" placeholder="<?= e($col['label']) ?>"><?= e($row[$col['name']] ?? '') ?></textarea>
                <?php elseif ($colType === 'select_or_text'): ?>
                  <input type="text" name="<?= e($name) ?>_<?= e($col['name']) ?>[]" class="form-control mb-1" list="<?= e($name . '_' . $col['name']) ?>_options" placeholder="<?= e($col['label']) ?>" value="<?= e($row[$col['name']] ?? '') ?>">
                  <datalist id="<?= e($name . '_' . $col['name']) ?>_options">
                    <?php foreach (($col['options'] ?? []) as $opt): ?>
                      <option value="<?= e($opt) ?>"></option>
                    <?php endforeach; ?>
                  </datalist>
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
      <?php elseif ($field['type'] === 'party'): ?>
        <?php $party = is_array($value) ? $value : []; ?>
        <label class="form-label fw-bold"><?= e($field['label']) ?></label>
        <div class="row g-2 border rounded-3 p-2">
          <div class="col-md-6"><input type="text" name="<?= e($name) ?>_full_name" class="form-control mb-1" placeholder="Full Name" value="<?= e($party['full_name'] ?? '') ?>"></div>
          <div class="col-md-6"><input type="text" name="<?= e($name) ?>_id_no" class="form-control mb-1" placeholder="ID / CNIC / Passport No." value="<?= e($party['id_no'] ?? '') ?>"></div>
          <div class="col-md-6"><input type="text" name="<?= e($name) ?>_phone" class="form-control mb-1" placeholder="Phone" value="<?= e($party['phone'] ?? '') ?>"></div>
          <div class="col-md-6"><input type="email" name="<?= e($name) ?>_email" class="form-control mb-1" placeholder="Email" value="<?= e($party['email'] ?? '') ?>"></div>
          <div class="col-12"><textarea name="<?= e($name) ?>_address" class="form-control" rows="2" placeholder="Address"><?= e($party['address'] ?? '') ?></textarea></div>
        </div>
      <?php elseif ($field['type'] === 'select'): ?>
        <label class="form-label"><?= e($field['label']) ?></label>
        <select name="<?= e($name) ?>" class="form-select" <?= $field['required'] ? 'required' : '' ?>>
          <?php foreach (($field['options'] ?? []) as $opt): ?>
            <option value="<?= e($opt) ?>" <?= (string) $value === (string) $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
          <?php endforeach; ?>
        </select>
      <?php elseif ($field['type'] === 'signature'): ?>
        <?php
        $sig = is_array($value) ? $value : [];
        $mode = $sig['use_company_stamp'] ?? false ? 'stamp' : 'upload';
        $existingPath = $sig['path'] ?? '';
        ?>
        <label class="form-label fw-bold"><?= e($field['label']) ?></label>
        <div class="border rounded-3 p-3 signature-block" data-name="<?= e($name) ?>">
          <input type="hidden" name="<?= e($name) ?>_existing" value="<?= e((string) $existingPath) ?>">
          <input type="hidden" name="<?= e($name) ?>_data" class="signature-data-input">
          <div class="mb-2">
            <input type="text" name="<?= e($name) ?>_id_no" class="form-control" placeholder="Signer ID / CNIC / Passport No." value="<?= e($sig['id_no'] ?? '') ?>">
          </div>
          <div class="btn-group mb-2 signature-mode-group" role="group">
            <button type="button" class="btn btn-sm btn-outline-secondary sig-mode-btn <?= $mode === 'upload' ? 'active' : '' ?>" data-mode="upload">Upload Image</button>
            <button type="button" class="btn btn-sm btn-outline-secondary sig-mode-btn <?= $mode === 'draw' ? 'active' : '' ?>" data-mode="draw">Draw Signature</button>
            <button type="button" class="btn btn-sm btn-outline-secondary sig-mode-btn <?= $mode === 'stamp' ? 'active' : '' ?>" data-mode="stamp">Use Company Stamp</button>
          </div>
          <input type="hidden" name="<?= e($name) ?>_mode" class="signature-mode-input" value="<?= e($mode) ?>">

          <div class="sig-pane" data-mode-pane="upload" style="<?= $mode === 'upload' ? '' : 'display:none;' ?>">
            <input type="file" name="<?= e($name) ?>_file" class="form-control" accept="image/png,image/jpeg,image/webp">
            <?php if ($existingPath): ?><div class="form-text">Current signature on file. Upload a new image to replace it.</div><?php endif; ?>
          </div>

          <div class="sig-pane" data-mode-pane="draw" style="<?= $mode === 'draw' ? '' : 'display:none;' ?>">
            <canvas class="signature-canvas border rounded-2 bg-white" width="400" height="150" style="touch-action:none;cursor:crosshair;"></canvas>
            <div class="mt-1"><button type="button" class="btn btn-sm btn-outline-secondary sig-clear-btn">Clear</button></div>
          </div>

          <div class="sig-pane" data-mode-pane="stamp" style="<?= $mode === 'stamp' ? '' : 'display:none;' ?>">
            <div class="form-text">The company stamp from your <a href="<?= url('settings') ?>" target="_blank">Company Settings</a> will be used on this signature.</div>
          </div>
        </div>
      <?php elseif ($field['type'] === 'image'): ?>
        <label class="form-label fw-bold"><?= e($field['label']) ?></label>
        <?php if (!empty($field['help'])): ?><div class="form-text mb-1"><?= e($field['help']) ?></div><?php endif; ?>
        <input type="hidden" name="<?= e($name) ?>_existing" value="<?= e(is_array($value) ? '' : (string) $value) ?>">
        <input type="file" name="<?= e($name) ?>_file" class="form-control" accept="image/png,image/jpeg,image/webp">
        <div class="form-text">PNG, JPG or WEBP, max 2MB.</div>
        <?php if (!is_array($value) && $value !== ''): ?>
          <img src="<?= url('uploads/doc-images/' . e((string) $value)) ?>" class="mt-2 rounded border" style="max-height:90px;">
        <?php endif; ?>
      <?php elseif ($field['type'] === 'gallery'): ?>
        <?php $gal = []; if (is_string($value) && $value !== '') { $dec = json_decode($value, true); $gal = is_array($dec) ? $dec : []; } ?>
        <label class="form-label fw-bold"><?= e($field['label']) ?></label>
        <?php if (!empty($field['help'])): ?><div class="form-text mb-1"><?= e($field['help']) ?></div><?php endif; ?>
        <input type="hidden" name="<?= e($name) ?>_existing" value="<?= e(json_encode($gal)) ?>">
        <input type="file" name="<?= e($name) ?>_file[]" class="form-control" accept="image/png,image/jpeg,image/webp" multiple>
        <div class="form-text">Select one or more images (PNG, JPG, WEBP). New uploads are added to any existing images (up to 12).</div>
        <?php if (!empty($gal)): ?>
          <div class="d-flex flex-wrap gap-2 mt-2">
            <?php foreach ($gal as $g): ?><img src="<?= url('uploads/doc-images/' . e((string) $g)) ?>" class="rounded border" style="height:64px;"><?php endforeach; ?>
          </div>
        <?php endif; ?>
      <?php elseif ($field['type'] === 'table'): ?>
        <?php $columns = $field['columns'] ?? []; ?>
        <label class="form-label fw-bold"><?= e($field['label']) ?></label>
        <?php if (!empty($field['help'])): ?><div class="form-text mb-2"><?= e($field['help']) ?></div><?php endif; ?>
        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead><tr><?php foreach ($columns as $col): ?><th class="small text-secondary"><?= e($col['label']) ?></th><?php endforeach; ?><th></th></tr></thead>
            <tbody class="repeater-rows" data-repeater="<?= e($name) ?>">
              <?php $rows = is_array($value) && !empty($value) ? $value : [[]]; ?>
              <?php foreach ($rows as $row): ?>
              <tr class="repeater-row">
                <?php foreach ($columns as $col): ?>
                  <td><input type="text" name="<?= e($name) ?>_<?= e($col['name']) ?>[]" class="form-control form-control-sm" placeholder="<?= e($col['label']) ?>" value="<?= e($row[$col['name']] ?? '') ?>"></td>
                <?php endforeach; ?>
                <td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-x"></i></button></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary add-row" data-repeater-target="<?= e($name) ?>"><i class="bi bi-plus"></i> Add Row</button>
      <?php elseif ($field['type'] === 'chart'): ?>
        <?php
        $chart = is_array($value) ? $value : [];
        $ctype = $chart['type'] ?? ($field['default_type'] ?? 'bar');
        $ctitle = $chart['title'] ?? '';
        $crows = !empty($chart['rows']) ? $chart['rows'] : [['label' => '', 'value' => '']];
        ?>
        <label class="form-label fw-bold"><?= e($field['label']) ?></label>
        <?php if (!empty($field['help'])): ?><div class="form-text mb-2"><?= e($field['help']) ?></div><?php endif; ?>
        <div class="border rounded-3 p-2">
          <div class="row g-2 mb-2">
            <div class="col-md-7"><input type="text" name="<?= e($name) ?>_title" class="form-control form-control-sm" placeholder="Chart title (optional)" value="<?= e($ctitle) ?>"></div>
            <div class="col-md-5">
              <select name="<?= e($name) ?>_type" class="form-select form-select-sm">
                <?php foreach (['bar' => 'Bar chart', 'line' => 'Line chart', 'pie' => 'Pie chart', 'donut' => 'Donut chart'] as $ck => $cl): ?>
                  <option value="<?= $ck ?>" <?= $ctype === $ck ? 'selected' : '' ?>><?= $cl ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="repeater-rows" data-repeater="<?= e($name) ?>">
            <?php foreach ($crows as $row): ?>
            <div class="row g-2 mb-1 repeater-row">
              <div class="col"><input type="text" name="<?= e($name) ?>_label[]" class="form-control form-control-sm" placeholder="Label (e.g. 2024)" value="<?= e($row['label'] ?? '') ?>"></div>
              <div class="col-4"><input type="number" step="any" name="<?= e($name) ?>_value[]" class="form-control form-control-sm" placeholder="Value" value="<?= e((string) ($row['value'] ?? '')) ?>"></div>
              <div class="col-1"><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-x"></i></button></div>
            </div>
            <?php endforeach; ?>
          </div>
          <button type="button" class="btn btn-sm btn-outline-primary mt-1 add-row" data-repeater-target="<?= e($name) ?>"><i class="bi bi-plus"></i> Add Data Point</button>
        </div>
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

  <form method="POST" action="<?= e($formAction) ?>" id="documentForm" enctype="multipart/form-data">
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
          <?php if ($hasMediaTab): ?>
          <li class="nav-item"><button type="button" class="nav-link" data-tab="media">Media &amp; Charts</button></li>
          <?php endif; ?>
          <?php if ($hasPartiesTab): ?>
          <li class="nav-item"><button type="button" class="nav-link" data-tab="parties">Parties &amp; Signatures</button></li>
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

        <?php if ($hasMediaTab): ?>
        <div class="tab-pane d-none" data-pane="media">
          <p class="text-secondary small">Add images, photos, data charts and tables. Charts are drawn from the values you enter and render in both the preview and the downloaded PDF.</p>
          <?php foreach ($mediaFields as $field): render_field($field, $documentData); endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ($hasPartiesTab): ?>
        <div class="tab-pane d-none" data-pane="parties">
          <?php foreach ($partyFields as $field): render_field($field, $documentData); endforeach; ?>
          <?php foreach ($signatureFields as $field): render_field($field, $documentData); endforeach; ?>
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
                <option value="corporate" <?= $templateStyle === 'corporate' ? 'selected' : '' ?>>Corporate</option>
                <option value="construction" <?= $templateStyle === 'construction' ? 'selected' : '' ?>>Construction</option>
                <option value="freelancer" <?= $templateStyle === 'freelancer' ? 'selected' : '' ?>>Freelancer</option>
                <option value="consulting" <?= $templateStyle === 'consulting' ? 'selected' : '' ?>>Consulting</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Font Family</label>
              <select name="font_family" class="form-select">
                <option value="sans" <?= $fontFamily === 'sans' ? 'selected' : '' ?>>Sans-serif (Helvetica / Arial)</option>
                <option value="serif" <?= $fontFamily === 'serif' ? 'selected' : '' ?>>Serif (Times)</option>
                <option value="modern" <?= $fontFamily === 'modern' ? 'selected' : '' ?>>Modern (DejaVu Sans)</option>
                <option value="classic" <?= $fontFamily === 'classic' ? 'selected' : '' ?>>Classic (Georgia / Serif)</option>
                <option value="mono" <?= $fontFamily === 'mono' ? 'selected' : '' ?>>Monospace (Courier)</option>
              </select>
              <div class="form-text">Applied to the whole document.</div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Text Size</label>
              <select name="font_scale" class="form-select">
                <option value="compact" <?= $fontScale === 'compact' ? 'selected' : '' ?>>Compact</option>
                <option value="normal" <?= $fontScale === 'normal' ? 'selected' : '' ?>>Normal</option>
                <option value="large" <?= $fontScale === 'large' ? 'selected' : '' ?>>Large</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Heading Colour</label>
              <input type="color" name="heading_color" class="form-control form-control-color w-100" value="<?= e($headingColor) ?>">
              <div class="form-text">Section titles and headings.</div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Body Text Colour</label>
              <input type="color" name="body_color" class="form-control form-control-color w-100" value="<?= e($bodyColor) ?>">
              <div class="form-text">Main paragraph text.</div>
            </div>
            <div class="col-md-4">
              <label class="form-label d-block">Logo &amp; Stamp</label>
              <div class="form-check form-switch mt-2">
                <input class="form-check-input" type="checkbox" name="show_logo" id="showLogoSwitch" <?= $showLogo ? 'checked' : '' ?>>
                <label class="form-check-label" for="showLogoSwitch">Show company logo on this document</label>
              </div>
              <div class="form-check form-switch mt-2">
                <input class="form-check-input" type="checkbox" name="show_stamp" id="showStampSwitch" <?= $showStamp ? 'checked' : '' ?>>
                <label class="form-check-label" for="showStampSwitch">Show company stamp on this document</label>
              </div>
              <div class="form-text">Manage your logo and stamp in <a href="<?= url('settings') ?>">Company Settings</a>.</div>
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

  const modeBtn = e.target.closest('.sig-mode-btn');
  if (modeBtn) {
    const block = modeBtn.closest('.signature-block');
    const mode = modeBtn.dataset.mode;
    block.querySelectorAll('.sig-mode-btn').forEach(b => b.classList.remove('active'));
    modeBtn.classList.add('active');
    block.querySelectorAll('.sig-pane').forEach(p => {
      p.style.display = p.dataset.modePane === mode ? '' : 'none';
    });
    block.querySelector('.signature-mode-input').value = mode;
    return;
  }

  const clearBtn = e.target.closest('.sig-clear-btn');
  if (clearBtn) {
    const block = clearBtn.closest('.signature-block');
    const canvas = block.querySelector('.signature-canvas');
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    block.querySelector('.signature-data-input').value = '';
  }
});

document.querySelectorAll('.signature-canvas').forEach(function (canvas) {
  const ctx = canvas.getContext('2d');
  ctx.lineWidth = 2;
  ctx.lineCap = 'round';
  ctx.strokeStyle = '#111827';
  let drawing = false;

  function pos(evt) {
    const rect = canvas.getBoundingClientRect();
    const point = evt.touches ? evt.touches[0] : evt;
    return {
      x: (point.clientX - rect.left) * (canvas.width / rect.width),
      y: (point.clientY - rect.top) * (canvas.height / rect.height),
    };
  }

  function start(evt) {
    drawing = true;
    const p = pos(evt);
    ctx.beginPath();
    ctx.moveTo(p.x, p.y);
    evt.preventDefault();
  }

  function move(evt) {
    if (!drawing) return;
    const p = pos(evt);
    ctx.lineTo(p.x, p.y);
    ctx.stroke();
    evt.preventDefault();
  }

  function end() {
    if (!drawing) return;
    drawing = false;
    const block = canvas.closest('.signature-block');
    block.querySelector('.signature-data-input').value = canvas.toDataURL('image/png');
  }

  canvas.addEventListener('mousedown', start);
  canvas.addEventListener('mousemove', move);
  canvas.addEventListener('mouseup', end);
  canvas.addEventListener('mouseleave', end);
  canvas.addEventListener('touchstart', start, { passive: false });
  canvas.addEventListener('touchmove', move, { passive: false });
  canvas.addEventListener('touchend', end);
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
  // Build payload excluding file inputs so images aren't re-uploaded on every
  // keystroke (existing image paths are preserved via the hidden _existing fields).
  const data = new FormData();
  form.querySelectorAll('input, select, textarea').forEach(function (el) {
    if (!el.name || el.type === 'file') return;
    if ((el.type === 'checkbox' || el.type === 'radio') && !el.checked) return;
    data.append(el.name, el.value);
  });
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
