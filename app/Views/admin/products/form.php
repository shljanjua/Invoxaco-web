<?php
/** @var array|null $product */
/** @var array $categories */
/** @var array $types */
$isEdit = $product !== null;
$action = $isEdit ? url('admin/products/' . $product['id']) : url('admin/products');
if (!function_exists('pv')) { function pv(?array $p, string $k, $default = '') { return e((string) ($p[$k] ?? $default)); } }
$coverUrl = ($isEdit && !empty($product['cover_image'])) ? url('uploads/products/' . $product['cover_image']) : '';
$curPrice = $isEdit ? (float) $product['price'] : 0.0;
$curCurrency = $isEdit ? ($product['currency'] ?: 'USD') : 'USD';
?>
<style>
.upload-zone{border:2px dashed #cbd5e1;border-radius:14px;padding:26px;text-align:center;cursor:pointer;transition:.15s;background:#f8fafc;}
.upload-zone:hover,.upload-zone.dragover{border-color:#2563eb;background:#eff6ff;}
.upload-zone .uz-icon{font-size:2rem;color:#94a3b8;}
.cover-preview{width:100%;border-radius:12px;object-fit:cover;max-height:240px;display:none;}
.file-chip{display:none;align-items:center;gap:10px;background:#eef2ff;border:1px solid #c7d2fe;border-radius:10px;padding:10px 14px;margin-top:10px;}
.preview-card{border:0;box-shadow:0 6px 24px rgba(2,6,23,.08);border-radius:16px;overflow:hidden;}
.preview-cover{height:150px;background:#f1f5f9 center/cover no-repeat;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:2rem;}
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h3 class="fw-bold mb-0"><?= $isEdit ? 'Edit Product' : 'Add New Product' ?></h3>
    <p class="text-secondary mb-0 small">Sell e-books, templates, documents and more — buyers download instantly after checkout.</p>
  </div>
  <a href="<?= url('admin/products') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<form method="POST" action="<?= $action ?>" enctype="multipart/form-data" id="productForm">
  <?= csrf_field() ?>
  <div class="row g-4">
    <!-- ===================== LEFT: editor ===================== -->
    <div class="col-lg-8">

      <!-- Basics -->
      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
          <h6 class="fw-bold mb-3"><i class="bi bi-pencil-square me-2"></i>Product details</h6>
          <div class="mb-3">
            <label class="form-label">Product name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="f_name" class="form-control form-control-lg" value="<?= pv($product, 'name') ?>" placeholder="e.g. The Freelancer Finance Handbook" required>
          </div>
          <div class="mb-3">
            <label class="form-label">URL slug <span class="text-secondary small">(leave blank to auto-generate)</span></label>
            <div class="input-group">
              <span class="input-group-text text-secondary">/store/product/</span>
              <input type="text" name="slug" class="form-control" value="<?= pv($product, 'slug') ?>" placeholder="auto from name">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Short description</label>
            <input type="text" name="short_description" id="f_short" class="form-control" maxlength="255" value="<?= pv($product, 'short_description') ?>" placeholder="One punchy line shown on store cards">
          </div>
          <div class="mb-0">
            <label class="form-label">Full description</label>
            <textarea name="description" class="form-control" rows="7" placeholder="What's inside, who it's for, what they'll get, the format and page count..."><?= pv($product, 'description') ?></textarea>
            <div class="form-text">Tip: use short paragraphs and bullet-style lines. This appears on the product page.</div>
          </div>
        </div>
      </div>

      <!-- Cover image -->
      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
          <h6 class="fw-bold mb-1"><i class="bi bi-image me-2"></i>Cover image</h6>
          <p class="text-secondary small mb-3">Shown on storefront cards and the product page. PNG, JPG or WEBP, up to 2MB. Recommended 1280&times;720 (16:9).</p>
          <div class="upload-zone" id="coverZone">
            <img class="cover-preview" id="coverPreview" src="<?= e($coverUrl) ?>" style="<?= $coverUrl ? 'display:block;' : '' ?>">
            <div id="coverPrompt" style="<?= $coverUrl ? 'display:none;' : '' ?>">
              <div class="uz-icon"><i class="bi bi-cloud-arrow-up"></i></div>
              <div class="fw-semibold mt-1">Click or drag an image here</div>
              <div class="text-secondary small">PNG, JPG, WEBP &middot; max 2MB</div>
            </div>
          </div>
          <input type="file" name="cover_image" id="coverInput" accept="image/png,image/jpeg,image/webp" class="d-none">
          <div class="mt-2" id="coverActions" style="<?= $coverUrl ? '' : 'display:none;' ?>">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="coverChange"><i class="bi bi-arrow-repeat me-1"></i>Change image</button>
          </div>
        </div>
      </div>

      <!-- Product file -->
      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
          <h6 class="fw-bold mb-1"><i class="bi bi-file-earmark-arrow-up me-2"></i>Product file <?= $isEdit ? '' : '<span class="text-danger">*</span>' ?></h6>
          <p class="text-secondary small mb-3">The file buyers download. Stored privately outside the web root — only paid customers can access it. PDF, EPUB, MOBI, ZIP, Word, Excel, PowerPoint, TXT, CSV, PNG, JPG &middot; up to 100MB.</p>
          <div class="upload-zone" id="fileZone">
            <div class="uz-icon"><i class="bi bi-file-earmark-arrow-up"></i></div>
            <div class="fw-semibold mt-1">Click or drag your file here</div>
            <div class="text-secondary small">Max 100MB</div>
          </div>
          <input type="file" name="product_file" id="fileInput" class="d-none">
          <div class="file-chip" id="fileChip">
            <i class="bi bi-file-earmark-check-fill text-primary fs-4"></i>
            <div class="flex-grow-1">
              <div class="fw-semibold" id="fileName">file.pdf</div>
              <div class="text-secondary small" id="fileSize"></div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="fileChange">Change</button>
          </div>
          <?php if ($isEdit && !empty($product['file_name'])): ?>
            <div class="mt-2 small text-secondary"><i class="bi bi-check-circle text-success me-1"></i>Current file: <strong><?= e($product['file_name']) ?></strong><?= $product['file_size'] ? ' (' . round($product['file_size']/1048576, 2) . ' MB)' : '' ?>. Upload a new file only if you want to replace it.</div>
          <?php endif; ?>
        </div>
      </div>

      <!-- SEO -->
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
          <h6 class="fw-bold mb-3"><i class="bi bi-search me-2"></i>Search engine listing (optional)</h6>
          <div class="mb-3">
            <label class="form-label">Meta title</label>
            <input type="text" name="meta_title" class="form-control" maxlength="190" value="<?= pv($product, 'meta_title') ?>" placeholder="Defaults to the product name">
          </div>
          <div class="mb-0">
            <label class="form-label">Meta description</label>
            <textarea name="meta_description" class="form-control" rows="2" maxlength="255" placeholder="Defaults to the short description"><?= pv($product, 'meta_description') ?></textarea>
          </div>
        </div>
      </div>
    </div>

    <!-- ===================== RIGHT: preview + settings ===================== -->
    <div class="col-lg-4">
      <!-- Live preview -->
      <div class="card preview-card mb-4">
        <div class="preview-cover" id="previewCover" style="<?= $coverUrl ? "background-image:url('".e($coverUrl)."');" : '' ?>">
          <span id="previewCoverIcon" style="<?= $coverUrl ? 'display:none;' : '' ?>"><i class="bi bi-image"></i></span>
        </div>
        <div class="card-body">
          <span class="badge bg-light text-secondary text-uppercase mb-2" style="font-size:.6rem;" id="previewType"><?= e($product['type'] ?? 'ebook') ?></span>
          <h6 class="fw-bold mb-1" id="previewName"><?= pv($product, 'name') ?: 'Product name' ?></h6>
          <p class="text-secondary small mb-2" id="previewShort"><?= pv($product, 'short_description') ?: 'Your short description appears here.' ?></p>
          <div class="fw-bold fs-5" id="previewPrice"><?= $curPrice <= 0 ? 'Free' : money($curPrice, $curCurrency) ?></div>
        </div>
      </div>

      <!-- Pricing -->
      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
          <h6 class="fw-bold mb-3"><i class="bi bi-tag me-2"></i>Pricing</h6>
          <?php $pm = $product['pricing_model'] ?? 'fixed'; ?>
          <div class="mb-3">
            <label class="form-label">Pricing model</label>
            <select name="pricing_model" id="f_pmodel" class="form-select">
              <option value="fixed" <?= $pm === 'fixed' ? 'selected' : '' ?>>Fixed price</option>
              <option value="pwyw" <?= $pm === 'pwyw' ? 'selected' : '' ?>>Pay what you want</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label"><span id="priceLabel"><?= $pm === 'pwyw' ? 'Minimum price' : 'Price' ?></span></label>
            <div class="input-group">
              <span class="input-group-text" id="curSymbol"><?= e(currency_symbol($curCurrency)) ?></span>
              <input type="number" step="0.01" min="0" name="price" id="f_price" class="form-control" value="<?= pv($product, 'price', '0.00') ?>">
            </div>
            <div class="form-text" id="priceHelp"><?= $pm === 'pwyw' ? 'The least a buyer may pay. Set 0 to allow free (“name your price”).' : 'Enter 0 for a free lead magnet.' ?></div>
          </div>
          <div class="mb-3 pm-fixed" style="<?= $pm === 'pwyw' ? 'display:none;' : '' ?>">
            <label class="form-label">Sale price <span class="text-secondary small">(optional)</span></label>
            <div class="input-group">
              <span class="input-group-text price-sym"><?= e(currency_symbol($curCurrency)) ?></span>
              <input type="number" step="0.01" min="0" name="sale_price" class="form-control" value="<?= $product && $product['sale_price'] !== null ? pv($product, 'sale_price') : '' ?>" placeholder="Discounted price">
            </div>
          </div>
          <div class="mb-3 pm-pwyw" style="<?= $pm === 'pwyw' ? '' : 'display:none;' ?>">
            <label class="form-label">Suggested price <span class="text-secondary small">(optional)</span></label>
            <div class="input-group">
              <span class="input-group-text price-sym"><?= e(currency_symbol($curCurrency)) ?></span>
              <input type="number" step="0.01" min="0" name="suggested_price" class="form-control" value="<?= $product && ($product['suggested_price'] ?? null) !== null ? pv($product, 'suggested_price') : '' ?>" placeholder="Pre-filled amount for buyers">
            </div>
            <div class="form-text">Shown as the default the buyer can adjust. Must be at least the minimum.</div>
          </div>
          <div class="mb-0">
            <label class="form-label">Currency</label>
            <input type="text" name="currency" id="f_currency" class="form-control text-uppercase" maxlength="10" value="<?= pv($product, 'currency', 'USD') ?>">
          </div>
        </div>
      </div>

      <!-- Organization -->
      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
          <h6 class="fw-bold mb-3"><i class="bi bi-collection me-2"></i>Organization</h6>
          <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select">
              <option value="">— None —</option>
              <?php foreach ($categories as $c): ?>
                <option value="<?= (int) $c['id'] ?>" <?= ($product['category_id'] ?? null) == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <div class="form-text"><a href="<?= url('admin/store-categories') ?>">Manage categories</a></div>
          </div>
          <div class="mb-3">
            <label class="form-label">Product type</label>
            <select name="type" id="f_type" class="form-select text-capitalize">
              <?php foreach ($types as $t): ?>
                <option value="<?= e($t) ?>" <?= ($product['type'] ?? 'ebook') === $t ? 'selected' : '' ?>><?= e($t) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-0">
            <label class="form-label">Sort order</label>
            <input type="number" name="sort_order" class="form-control" value="<?= pv($product, 'sort_order', '0') ?>">
            <div class="form-text">Lower numbers appear first.</div>
          </div>
        </div>
      </div>

      <!-- Visibility -->
      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
          <h6 class="fw-bold mb-3"><i class="bi bi-eye me-2"></i>Visibility</h6>
          <div class="form-check form-switch mb-2">
            <input type="checkbox" class="form-check-input" name="is_active" id="is_active" <?= (!$isEdit || (int) $product['is_active'] === 1) ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_active">Published (visible in store)</label>
          </div>
          <div class="form-check form-switch mb-0">
            <input type="checkbox" class="form-check-input" name="is_featured" id="is_featured" <?= ($isEdit && (int) $product['is_featured'] === 1) ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_featured">Feature on storefront</label>
          </div>
        </div>
      </div>

      <button class="btn btn-primary w-100 btn-lg"><i class="bi bi-save me-1"></i><?= $isEdit ? 'Save Changes' : 'Publish Product' ?></button>
      <a href="<?= url('store') ?>" target="_blank" class="btn btn-link w-100 mt-1">Preview store <i class="bi bi-box-arrow-up-right small"></i></a>
    </div>
  </div>
</form>

<script>
(function () {
  function bytes(n){ if(n<1024)return n+' B'; if(n<1048576)return (n/1024).toFixed(1)+' KB'; return (n/1048576).toFixed(2)+' MB'; }

  // ---- Cover image: click, drag-drop, live preview ----
  var coverZone=document.getElementById('coverZone'), coverInput=document.getElementById('coverInput'),
      coverPreview=document.getElementById('coverPreview'), coverPrompt=document.getElementById('coverPrompt'),
      coverActions=document.getElementById('coverActions'), coverChange=document.getElementById('coverChange'),
      previewCover=document.getElementById('previewCover'), previewCoverIcon=document.getElementById('previewCoverIcon');

  function showCover(file){
    if(!file || !file.type.startsWith('image/')) return;
    var r=new FileReader();
    r.onload=function(e){
      coverPreview.src=e.target.result; coverPreview.style.display='block';
      coverPrompt.style.display='none'; coverActions.style.display='';
      previewCover.style.backgroundImage="url('"+e.target.result+"')";
      previewCoverIcon.style.display='none';
    };
    r.readAsDataURL(file);
  }
  coverZone.addEventListener('click', function(){ coverInput.click(); });
  if(coverChange) coverChange.addEventListener('click', function(e){ e.stopPropagation(); coverInput.click(); });
  coverInput.addEventListener('change', function(){ if(this.files[0]) showCover(this.files[0]); });
  ['dragover','dragenter'].forEach(function(ev){ coverZone.addEventListener(ev,function(e){e.preventDefault();coverZone.classList.add('dragover');}); });
  ['dragleave','drop'].forEach(function(ev){ coverZone.addEventListener(ev,function(e){e.preventDefault();coverZone.classList.remove('dragover');}); });
  coverZone.addEventListener('drop', function(e){ var f=e.dataTransfer.files[0]; if(f){ coverInput.files=e.dataTransfer.files; showCover(f); } });

  // ---- Product file: click, drag-drop, name/size chip ----
  var fileZone=document.getElementById('fileZone'), fileInput=document.getElementById('fileInput'),
      fileChip=document.getElementById('fileChip'), fileName=document.getElementById('fileName'),
      fileSize=document.getElementById('fileSize'), fileChange=document.getElementById('fileChange');
  function showFile(file){ if(!file)return; fileName.textContent=file.name; fileSize.textContent=bytes(file.size); fileChip.style.display='flex'; fileZone.style.display='none'; }
  fileZone.addEventListener('click', function(){ fileInput.click(); });
  if(fileChange) fileChange.addEventListener('click', function(){ fileInput.click(); });
  fileInput.addEventListener('change', function(){ if(this.files[0]) showFile(this.files[0]); });
  ['dragover','dragenter'].forEach(function(ev){ fileZone.addEventListener(ev,function(e){e.preventDefault();fileZone.classList.add('dragover');}); });
  ['dragleave','drop'].forEach(function(ev){ fileZone.addEventListener(ev,function(e){e.preventDefault();fileZone.classList.remove('dragover');}); });
  fileZone.addEventListener('drop', function(e){ var f=e.dataTransfer.files[0]; if(f){ fileInput.files=e.dataTransfer.files; showFile(f); } });

  // ---- Live preview bindings ----
  var SYM={USD:'$',EUR:'€',GBP:'£',INR:'₹',PKR:'Rs ',AUD:'A$',CAD:'C$',JPY:'¥',AED:'AED ',SAR:'SAR '};
  function sym(c){ c=(c||'USD').toUpperCase(); return SYM[c]||(c+' '); }
  function bind(id, fn){ var el=document.getElementById(id); if(el) el.addEventListener('input', fn); }
  function isPwyw(){ var s=document.getElementById('f_pmodel'); return s && s.value==='pwyw'; }
  function upd(){
    var name=document.getElementById('f_name').value||'Product name';
    var short=document.getElementById('f_short').value||'Your short description appears here.';
    var price=parseFloat(document.getElementById('f_price').value||'0');
    var cur=document.getElementById('f_currency').value||'USD';
    document.getElementById('previewName').textContent=name;
    document.getElementById('previewShort').textContent=short;
    var pwyw=isPwyw();
    var priceTxt = price>0 ? (sym(cur)+price.toFixed(2)) : 'Free';
    if(pwyw) priceTxt = price>0 ? (sym(cur)+price.toFixed(2)+'+') : 'Name your price';
    document.getElementById('previewPrice').textContent=priceTxt;
    document.getElementById('curSymbol').textContent=sym(cur);
    document.querySelectorAll('.price-sym').forEach(function(el){ el.textContent=sym(cur); });
  }
  function togglePricing(){
    var pwyw=isPwyw();
    document.querySelectorAll('.pm-fixed').forEach(function(el){ el.style.display = pwyw?'none':''; });
    document.querySelectorAll('.pm-pwyw').forEach(function(el){ el.style.display = pwyw?'':'none'; });
    document.getElementById('priceLabel').textContent = pwyw?'Minimum price':'Price';
    document.getElementById('priceHelp').innerHTML = pwyw
      ? 'The least a buyer may pay. Set 0 to allow free (“name your price”).'
      : 'Enter 0 for a free lead magnet.';
    upd();
  }
  ['f_name','f_short','f_price','f_currency'].forEach(function(id){ bind(id, upd); });
  var pmodel=document.getElementById('f_pmodel'); if(pmodel) pmodel.addEventListener('change', togglePricing);
  var typeSel=document.getElementById('f_type');
  if(typeSel) typeSel.addEventListener('change', function(){ document.getElementById('previewType').textContent=this.value; });
})();
</script>
