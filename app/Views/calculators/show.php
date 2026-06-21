<?php
/** @var string $slug */
/** @var array $def */
/** @var string $currency */
$currencies = ['USD', 'EUR', 'GBP', 'INR', 'PKR', 'AUD', 'CAD', 'AED', 'SAR', 'JPY', 'CNY', 'ZAR', 'NGN', 'BRL', 'SGD'];
?>
<div class="container py-4">
  <nav class="small mb-3"><a href="<?= url('calculators') ?>" class="text-decoration-none"><i class="bi bi-arrow-left me-1"></i>All Calculators</a></nav>

  <div class="row g-4">
    <div class="col-lg-5">
      <div class="card border-0 shadow-sm rounded-4 animate-in">
        <div class="card-body">
          <h4 class="fw-bold mb-1"><i class="bi <?= e($def['icon']) ?> text-primary me-2"></i><?= e($def['name']) ?></h4>
          <p class="text-secondary small mb-4"><?= e($def['description']) ?></p>

          <form id="calcForm">
            <div class="mb-3">
              <label class="form-label small">Currency</label>
              <select name="currency" id="calcCurrency" class="form-select">
                <?php foreach ($currencies as $c): ?>
                  <option value="<?= e($c) ?>" <?= $c === $currency ? 'selected' : '' ?>><?= e($c) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <?php foreach ($def['fields'] as $field): ?>
              <div class="mb-3">
                <label class="form-label small"><?= e($field['label']) ?></label>
                <?php if (($field['type'] ?? 'number') === 'select'): ?>
                  <select name="<?= e($field['name']) ?>" class="form-select calc-input">
                    <?php foreach ($field['options'] as $value => $label): ?>
                      <option value="<?= e($value) ?>" <?= $value === $field['default'] ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                  </select>
                <?php else: ?>
                  <input type="number" step="any" name="<?= e($field['name']) ?>" class="form-control calc-input"
                         value="<?= e((string) $field['default']) ?>"
                         <?= isset($field['min']) ? 'min="' . e((string) $field['min']) . '"' : '' ?>
                         <?= isset($field['max']) ? 'max="' . e((string) $field['max']) . '"' : '' ?>>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="card border-0 shadow-sm rounded-4 animate-in" style="animation-delay:0.1s;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Results</h5>
            <a href="#" id="downloadPdfBtn" class="btn btn-primary btn-sm"><i class="bi bi-file-earmark-pdf me-1"></i>Download PDF</a>
          </div>
          <div id="calcResults" class="row g-3"></div>

          <?php if (!empty($def['hasAmortization'])): ?>
          <h6 class="fw-bold mt-4 mb-2">Amortization Schedule</h6>
          <div class="table-responsive" style="max-height:420px; overflow-y:auto;">
            <table class="table table-sm">
              <thead class="sticky-top bg-white"><tr><th>#</th><th class="text-end">Payment</th><th class="text-end">Principal</th><th class="text-end">Interest</th><th class="text-end">Balance</th></tr></thead>
              <tbody id="amortBody"></tbody>
            </table>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  const slug = <?= json_encode($slug) ?>;
  const resultDefs = <?= json_encode($def['results']) ?>;
  const hasAmortization = <?= !empty($def['hasAmortization']) ? 'true' : 'false' ?>;
  const form = document.getElementById('calcForm');
  const resultsEl = document.getElementById('calcResults');
  const amortBody = document.getElementById('amortBody');
  const downloadBtn = document.getElementById('downloadPdfBtn');
  let timer = null;

  const symbols = {USD:'$',EUR:'€',GBP:'£',INR:'₹',PKR:'Rs ',AUD:'A$',CAD:'C$',AED:'AED ',SAR:'SAR ',JPY:'¥',CNY:'¥',ZAR:'R',NGN:'₦',BRL:'R$',SGD:'S$'};

  function money(value, currency) {
    if (value === null || value === undefined) return 'N/A';
    const symbol = symbols[currency] || (currency + ' ');
    return symbol + Number(value).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
  }

  function formatValue(value, format, currency) {
    if (value === null || value === undefined) return 'N/A';
    switch (format) {
      case 'currency': return money(value, currency);
      case 'percent': return Number(value).toFixed(2) + '%';
      case 'ratio': return Number(value).toFixed(2) + 'x';
      case 'months': return Number(value).toFixed(1) + ' months';
      default: return Number(value).toLocaleString(undefined, {maximumFractionDigits: 2});
    }
  }

  function buildQuery() {
    const data = new FormData(form);
    return new URLSearchParams(data).toString();
  }

  function renderResults(values, currency) {
    resultsEl.innerHTML = '';
    resultDefs.forEach(function (rd) {
      const value = values[rd.key];
      const col = document.createElement('div');
      col.className = 'col-md-6';
      col.innerHTML = '<div class="p-3 bg-primary-subtle rounded-3"><div class="small text-secondary">' + rd.label + '</div>' +
        '<div class="fs-5 fw-bold">' + formatValue(value, rd.format, currency) + '</div></div>';
      resultsEl.appendChild(col);
    });

    if (hasAmortization && amortBody) {
      amortBody.innerHTML = '';
      (values.schedule || []).forEach(function (row) {
        const tr = document.createElement('tr');
        tr.innerHTML = '<td>' + row.month + '</td><td class="text-end">' + money(row.payment, currency) + '</td>' +
          '<td class="text-end">' + money(row.principal, currency) + '</td><td class="text-end">' + money(row.interest, currency) + '</td>' +
          '<td class="text-end">' + money(row.balance, currency) + '</td>';
        amortBody.appendChild(tr);
      });
    }
  }

  function calculate() {
    const currency = document.getElementById('calcCurrency').value;
    fetch('<?= url('calculators/' . $slug . '/calculate') ?>', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
      body: buildQuery(),
    })
      .then(function (res) { return res.json(); })
      .then(function (json) {
        if (json.ok) renderResults(json.values, currency);
      });
    downloadBtn.href = '<?= url('calculators/' . $slug . '/pdf') ?>?' + buildQuery();
  }

  form.addEventListener('input', function () {
    clearTimeout(timer);
    timer = setTimeout(calculate, 200);
  });
  form.addEventListener('change', calculate);

  calculate();
})();
</script>
