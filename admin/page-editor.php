<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';
sendSecurityHeaders();
requireLogin();
$pageTitle = 'Page Editor';
$navData = jsonRead(DATA_DIR . 'nav.json');
$sel = null; $selName = ''; $data = [];

if (isset($_GET['p'])) {
    $slug = preg_replace('/[^a-z0-9_-]/', '', strtolower($_GET['p']));
    $path = DATA_DIR . 'pages/' . $slug . '.json';
    if (is_file($path)) { $sel = $path; $selName = $slug; $data = jsonRead($sel); }
}

// ── Rendering helpers ─────────────────────────────────────────────

function pe_label(string $key): string {
    return ucwords(str_replace(['_', '-'], ' ', $key));
}

function pe_is_image(string $key, $value): bool {
    $words = ['photo', 'image', 'img', 'picture', 'thumbnail', 'banner', 'cover', 'avatar', 'logo'];
    $k = strtolower($key);
    if (in_array($k, $words)) return true;
    foreach ($words as $w) {
        if (strlen($k) >= strlen($w) && substr($k, -strlen($w)) === $w) return true;
    }
    return is_string($value) && pe_is_image_file((string)$value);
}

function pe_is_image_file(string $value): bool {
    return (bool)preg_match('/^[\w\-. ]+\.(png|jpe?g|webp|gif)$/i', trim($value));
}

function pe_is_icon(string $key): bool {
    $k = strtolower($key);
    return $k === 'icon' || substr($k, -5) === '_icon';
}

function pe_is_list(array $arr): bool {
    if (empty($arr)) return true;
    return array_keys($arr) === range(0, count($arr) - 1);
}

function pe_drag(): string {
    return '<span class="drag-handle pe-drag" title="Drag to reorder">'
         . '<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">'
         . '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>'
         . '</svg></span>';
}

function pe_scalar(string $key, string $value): string {
    $k   = htmlspecialchars($key, ENT_QUOTES);
    $v   = htmlspecialchars($value, ENT_QUOTES);
    $lbl = htmlspecialchars(pe_label($key));
    $long = strlen($value) > 80 || strpos($value, "\n") !== false || strpos($value, '<br>') !== false;
    if ($long) {
        $rows = min(8, max(2, substr_count($value, "\n") + substr_count($value, '<br>') + 2));
        $inp = '<textarea class="form-control form-control-sm" data-key="' . $k . '" rows="' . $rows . '">' . $v . '</textarea>';
    } else {
        $inp = '<input type="text" class="form-control form-control-sm" data-key="' . $k . '" value="' . $v . '">';
    }
    return '<div class="pe-field mb-3" data-key="' . $k . '"><label class="form-label-xs">' . $lbl . '</label>' . $inp . '</div>';
}

function pe_image(string $key, string $value, string $dataKey = ''): string {
    $dk   = $dataKey ?: $key;
    $k    = htmlspecialchars($dk, ENT_QUOTES);
    $v    = htmlspecialchars($value, ENT_QUOTES);
    $lbl  = $dataKey ? '' : '<label class="form-label-xs">' . htmlspecialchars(pe_label($key)) . '</label>';
    $wrap = $dataKey ? 'pe-field pe-field-image mb-0' : 'pe-field pe-field-image mb-3';
    $dkAttr = $dataKey ? '' : ' data-key="' . htmlspecialchars($key, ENT_QUOTES) . '"';
    $src  = $value ? htmlspecialchars('../uploads/pages/' . $value, ENT_QUOTES) : '';
    $hide = $value ? '' : ' style="display:none"';
    $name = $value ? htmlspecialchars($value) : 'No image';
    return '<div class="' . $wrap . '"' . $dkAttr . '>'
         . $lbl
         . '<img src="' . $src . '" class="pe-img-preview mb-1"' . $hide
         . ' onerror="this.style.display=\'none\'" style="max-width:100%;max-height:120px;border-radius:6px;border:1px solid var(--border);display:block">'
         . '<input type="hidden" data-key="' . $k . '" value="' . $v . '">'
         . '<div style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;margin-top:.4rem">'
         . '<button type="button" class="btn btn-outline btn-sm pe-img-change-btn">Change Image</button>'
         . '<span class="pe-img-name text-muted small">' . $name . '</span>'
         . '</div>'
         . '<input type="file" accept="image/*" class="pe-img-file" style="display:none">'
         . '</div>';
}

function pe_icon(string $key, string $value, string $dataKey = ''): string {
    $dk    = $dataKey ?: $key;
    $k     = htmlspecialchars($dk, ENT_QUOTES);
    $v     = htmlspecialchars($value, ENT_QUOTES);
    $lbl   = $dataKey ? '' : '<label class="form-label-xs">' . htmlspecialchars(pe_label($key)) . '</label>';
    $wrap  = $dataKey ? 'pe-field pe-field-icon mb-0' : 'pe-field pe-field-icon mb-3';
    $dkAttr = $dataKey ? '' : ' data-key="' . htmlspecialchars($key, ENT_QUOTES) . '"';
    return '<div class="' . $wrap . '"' . $dkAttr . '>'
         . $lbl
         . '<div class="pe-icon-row">'
         . '<div class="pe-icon-svg-preview" data-icon-preview="1"></div>'
         . '<span class="pe-icon-label text-muted small">' . ($value ? htmlspecialchars($value) : 'No icon selected') . '</span>'
         . '<button type="button" class="btn btn-outline btn-sm pe-icon-pick-btn">Choose Icon</button>'
         . '</div>'
         . '<input type="hidden" data-key="' . $k . '" value="' . $v . '" class="pe-icon-hidden">'
         . '</div>';
}

function pe_item_fields(array $item): string {
    $html = '';
    foreach ($item as $k => $v) {
        if (is_array($v)) continue;
        if (pe_is_icon($k)) {
            $html .= pe_icon($k, (string)$v);
        } elseif (pe_is_image($k, (string)$v)) {
            $html .= pe_image($k, (string)$v);
        } else {
            $html .= pe_scalar($k, (string)$v);
        }
    }
    return $html;
}

function pe_render_item($item, int $index, bool $isStr, bool $imgArray = false): string {
    $n   = $index + 1;
    $hdr = '<div class="block-header">'
         . pe_drag()
         . '<span class="block-label">Item ' . $n . '</span>'
         . '<button type="button" class="btn btn-danger btn-sm pe-remove-item" style="margin-left:auto">Remove</button>'
         . '</div>';
    if ($isStr) {
        $v = (string)$item;
        if ($imgArray || pe_is_image_file($v)) {
            $body = '<div class="block-body">' . pe_image('', $v, '__str__') . '</div>';
        } else {
            $ve   = htmlspecialchars($v, ENT_QUOTES);
            $rows = min(6, max(2, substr_count($v, "\n") + 2));
            $body = '<div class="block-body"><textarea class="form-control form-control-sm" data-key="__str__" rows="' . $rows . '">' . $ve . '</textarea></div>';
        }
        return '<li class="pe-item pe-item-block sortable-item" data-type="string">' . $hdr . $body . '</li>';
    }
    $body = '<div class="block-body">' . pe_item_fields((array)$item) . '</div>';
    return '<li class="pe-item pe-item-block sortable-item" data-type="object">' . $hdr . $body . '</li>';
}

function pe_field(string $key, $value): string {
    if (!is_array($value)) {
        if (pe_is_icon($key)) return pe_icon($key, (string)$value);
        return pe_is_image($key, (string)$value) ? pe_image($key, (string)$value) : pe_scalar($key, (string)$value);
    }

    // Non-list (associative object): preserve as-is via hidden input, not editable here
    if (!pe_is_list($value)) {
        $k   = htmlspecialchars($key, ENT_QUOTES);
        $lbl = htmlspecialchars(pe_label($key));
        $jv  = htmlspecialchars(json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES);
        return '<div class="pe-field mb-3" data-key="' . $k . '">'
             . '<div class="alert alert-warning" style="font-size:.82rem;margin-bottom:.4rem">'
             . '<strong>' . $lbl . '</strong> contains a complex structure managed in a dedicated manager (e.g. Products Manager). '
             . 'It is preserved automatically when you save this page.</div>'
             . '<input type="hidden" data-key="' . $k . '" data-complex="true" value="' . $jv . '">'
             . '</div>';
    }

    $k     = htmlspecialchars($key, ENT_QUOTES);
    $lbl   = htmlspecialchars(pe_label($key));
    $first = array_values($value)[0] ?? null;
    $isStr = !is_array($first);
    $imgArr = $isStr && is_string($first) && pe_is_image_file((string)$first);
    $cnt   = count($value);

    $itemsHtml = '';
    foreach ($value as $i => $item) {
        $itemsHtml .= pe_render_item($isStr ? (string)$item : (array)$item, $i, $isStr, $imgArr);
    }

    // Blank template for + Add Item
    if ($isStr) {
        $tpl = pe_render_item('', 0, true, $imgArr);
    } else {
        $blank = [];
        if (is_array($first)) {
            foreach ($first as $kk => $vv) { if (!is_array($vv)) $blank[$kk] = ''; }
        }
        $tpl = pe_render_item($blank, 0, false, false);
    }

    return '<div class="pe-array-section card mb-4" data-key="' . $k . '" data-array-type="' . ($isStr ? 'string' : 'object') . '">'
         . '<div class="card-header">' . $lbl
         . ' <span class="text-muted small pe-count" style="font-weight:400">(' . $cnt . ' items)</span>'
         . '</div>'
         . '<div class="card-body">'
         . '<ul class="sortable-list pe-items">' . $itemsHtml . '</ul>'
         . '<template class="pe-item-tpl">' . $tpl . '</template>'
         . '<button type="button" class="btn btn-outline btn-sm pe-add-item mt-3">+ Add Item</button>'
         . '</div></div>';
}

function pe_render(array $data): string {
    $html = '';
    foreach ($data as $key => $value) { $html .= pe_field($key, $value); }
    return $html;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?php echo e($pageTitle); ?> | PAL CMS</title>
<link rel="stylesheet" href="assets/admin.css">
<?php echo csrfMeta(); ?>
<style>
.pe-item-block .block-body { padding: .75rem 1rem; }
.pe-item-block .block-header { cursor: default; }
.pe-items { margin-bottom: 0; }
.pe-field > label { display: block; }
.pe-nav-link { display: block; padding: .5rem .9rem; color: var(--text); text-decoration: none; font-size: .875rem; }
.pe-nav-link:hover { background: var(--gray-50); color: var(--teal); }
.pe-nav-active { color: var(--teal); font-weight: 600; }
.pe-nav-sub { padding-left: 1.75rem; font-size: .84rem; color: var(--text-muted); border-top: 1px solid var(--border); }
.pe-nav-sub:hover { color: var(--teal); }
.pe-sub-list { border-top: 1px solid var(--border); }
.pe-sub-list .list-group-item { border: none; border-radius: 0; }
/* Icon picker */
.pe-icon-row { display:flex; align-items:center; gap:.6rem; flex-wrap:wrap; margin-top:.35rem; }
.pe-icon-svg-preview { width:36px; height:36px; display:flex; align-items:center; justify-content:center; border:1px solid var(--border); border-radius:6px; background:#f8fafc; flex-shrink:0; }
.pe-icon-svg-preview svg { display:block; }
.pe-icon-label { flex:1; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.pe-icon-modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9000; align-items:center; justify-content:center; }
.pe-icon-modal-overlay.active { display:flex; }
.pe-icon-modal { background:#fff; border-radius:10px; width:min(92vw,560px); max-height:80vh; display:flex; flex-direction:column; box-shadow:0 20px 60px rgba(0,0,0,.25); }
.pe-icon-modal-head { display:flex; align-items:center; justify-content:space-between; padding:.85rem 1.1rem; border-bottom:1px solid var(--border); flex-shrink:0; }
.pe-icon-modal-head strong { font-size:.95rem; }
.pe-icon-modal-close { background:none; border:none; cursor:pointer; color:var(--text-muted); padding:.2rem; line-height:1; }
.pe-icon-modal-close:hover { color:var(--text); }
.pe-icon-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(72px,1fr)); gap:.5rem; padding:1rem; overflow-y:auto; flex:1; }
.pe-icon-opt { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.35rem; border:2px solid var(--border); border-radius:8px; padding:.55rem .3rem; cursor:pointer; background:#fff; transition:border-color .15s,background .15s; font-size:.68rem; color:var(--text-muted); text-align:center; line-height:1.2; word-break:break-all; }
.pe-icon-opt:hover { border-color:var(--teal); background:var(--teal-50,#f0fdfa); color:var(--teal); }
.pe-icon-opt.selected { border-color:var(--teal); background:var(--teal-50,#f0fdfa); color:var(--teal); }
.pe-icon-modal-foot { padding:.7rem 1rem; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:.5rem; flex-shrink:0; }
</style>
</head>
<body class="admin-body">
<?php require_once __DIR__ . '/partials/sidebar.php'; ?>
<div class="admin-main">
<?php require_once __DIR__ . '/partials/topbar.php'; ?>
<div class="admin-content">

  <div class="row mb-4">
    <!-- Left: page list -->
    <div class="col-md-3">
      <div class="card mb-3">
        <div class="card-header">Pages</div>
        <ul class="list-group list-group-flush">
          <?php foreach ($navData['pages'] as $navPg):
            $pgSlug  = $navPg['slug'];
            $pgLabel = $navPg['label'];
            $pgActive = ($pgSlug === $selName);
            $pgHasSub = !empty($navPg['dropdown']);
            $pgExists = is_file(DATA_DIR . 'pages/' . $pgSlug . '.json');
            // Check if a child is currently selected (to highlight parent too)
            $pgChildActive = false;
            if ($pgHasSub) {
                foreach ($navPg['dropdown'] as $ch) {
                    if ($ch['slug'] === $selName) { $pgChildActive = true; break; }
                }
            }
          ?>
          <li class="list-group-item" style="padding:0">
            <?php if ($pgExists): ?>
            <a href="page-editor.php?p=<?php echo e(urlencode($pgSlug)); ?>" class="pe-nav-link<?php echo $pgActive ? ' pe-nav-active' : ''; ?>"><?php echo e($pgLabel); ?></a>
            <?php else: ?>
            <span class="pe-nav-link text-muted"><?php echo e($pgLabel); ?></span>
            <?php endif; ?>
            <?php if ($pgHasSub): ?>
            <ul class="list-group list-group-flush pe-sub-list">
              <?php foreach ($navPg['dropdown'] as $ch):
                $chSlug   = $ch['slug'];
                $chLabel  = $ch['label'];
                $chActive = ($chSlug === $selName);
                $chExists = is_file(DATA_DIR . 'pages/' . $chSlug . '.json');
              ?>
              <li class="list-group-item" style="padding:0">
                <?php if ($chExists): ?>
                <a href="page-editor.php?p=<?php echo e(urlencode($chSlug)); ?>" class="pe-nav-link pe-nav-sub<?php echo $chActive ? ' pe-nav-active' : ''; ?>"><?php echo e($chLabel); ?></a>
                <?php else: ?>
                <span class="pe-nav-link pe-nav-sub text-muted"><?php echo e($chLabel); ?></span>
                <?php endif; ?>
              </li>
              <?php endforeach; ?>
            </ul>
            <?php endif; ?>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php if ($sel): ?>
      <div class="card">
        <div class="card-header">Info</div>
        <div class="card-body">
          <p class="small text-muted">File: <strong><?php echo e($selName); ?>.json</strong></p>
          <p class="small text-muted mt-2">Images upload to <code>uploads/pages/</code>.</p>
          <p class="small text-muted mt-2">Click <strong>Save Page</strong> to apply all changes to the live site.</p>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Right: editor -->
    <div class="col-md-9">
      <?php if ($sel): ?>

      <div class="card mb-3">
        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;gap:.5rem">
          <span style="font-size:.95rem">Editing: <strong><?php echo e($selName); ?></strong></span>
          <div style="display:flex;gap:.5rem">
            <button type="button" class="btn btn-outline btn-sm" id="pe-raw-toggle">Raw JSON</button>
            <button type="button" class="btn btn-primary btn-sm" id="pe-save-btn">Save Page</button>
          </div>
        </div>
        <div class="card-body">
          <div id="pe-editor">
            <?php echo pe_render($data); ?>
          </div>
        </div>
      </div>

      <div id="pe-raw-panel" class="card mb-3" style="display:none">
        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;gap:.5rem">
          <span>Raw JSON</span>
          <div style="display:flex;gap:.5rem">
            <button type="button" class="btn btn-outline btn-sm" id="pe-raw-copy">Copy</button>
            <button type="button" class="btn btn-primary btn-sm" id="pe-raw-apply">Apply JSON</button>
          </div>
        </div>
        <div class="card-body">
          <p class="small text-muted mb-2">For advanced edits. Click "Apply JSON" to save directly from this editor.</p>
          <textarea id="pe-raw-json" class="form-control json-editor" rows="20" spellcheck="false"></textarea>
        </div>
      </div>

      <?php else: ?>
      <div class="card">
        <div class="card-body text-muted">Select a page from the list to edit its content.</div>
      </div>
      <?php endif; ?>
    </div>
  </div>

</div>
</div>
<div id="toast-container"></div>
<!-- Icon picker modal -->
<div class="pe-icon-modal-overlay" id="peIconOverlay">
  <div class="pe-icon-modal">
    <div class="pe-icon-modal-head">
      <strong>Choose Icon</strong>
      <button type="button" class="pe-icon-modal-close" id="peIconClose" title="Close">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="pe-icon-grid" id="peIconGrid"></div>
    <div class="pe-icon-modal-foot">
      <button type="button" class="btn btn-outline btn-sm" id="peIconClearBtn">Clear Icon</button>
      <button type="button" class="btn btn-outline btn-sm" id="peIconCancelBtn">Cancel</button>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="assets/admin.js"></script>
<?php if ($sel): ?>
<script>
(function () {
  var SLUG   = '<?php echo e($selName); ?>';
  var editor = document.getElementById('pe-editor');
  if (!editor) return;

  /* ── SortableJS ── */
  function initSortable(list) {
    if (list._sortable) return;
    list._sortable = Sortable.create(list, {
      handle: '.pe-drag',
      animation: 150,
      onEnd: function () { refreshNumbers(list); }
    });
  }
  editor.querySelectorAll('.pe-items').forEach(initSortable);

  /* ── Item numbering and count badge ── */
  function refreshNumbers(list) {
    var items = list.querySelectorAll(':scope > .pe-item');
    items.forEach(function (li, i) {
      var lbl = li.querySelector('.block-label');
      if (lbl) lbl.textContent = 'Item ' + (i + 1);
    });
    var section = list.closest('.pe-array-section');
    if (section) {
      var cnt = section.querySelector('.pe-count');
      if (cnt) cnt.textContent = '(' + items.length + ' items)';
    }
  }

  /* ── Add item ── */
  editor.addEventListener('click', function (e) {
    var btn = e.target.closest('.pe-add-item');
    if (!btn) return;
    var section = btn.closest('.pe-array-section');
    var list    = section.querySelector('.pe-items');
    var tpl     = section.querySelector('.pe-item-tpl');
    if (!tpl) return;
    var clone = tpl.content.cloneNode(true);
    clone.querySelectorAll('input:not([type=hidden]), textarea').forEach(function (inp) { inp.value = ''; });
    clone.querySelectorAll('input[type=hidden][data-key]').forEach(function (inp) { inp.value = ''; });
    clone.querySelectorAll('.pe-img-preview').forEach(function (img) { img.src = ''; img.style.display = 'none'; });
    clone.querySelectorAll('.pe-img-name').forEach(function (s) { s.textContent = 'No image'; });
    clone.querySelectorAll('.pe-icon-svg-preview').forEach(function (el) { el.innerHTML = ''; });
    clone.querySelectorAll('.pe-icon-label').forEach(function (s) { s.textContent = 'No icon selected'; });
    list.appendChild(clone);
    refreshNumbers(list);
    initSortable(list);
  });

  /* ── Remove item ── */
  editor.addEventListener('click', function (e) {
    var btn = e.target.closest('.pe-remove-item');
    if (!btn) return;
    var item = btn.closest('.pe-item');
    var list = item.closest('.pe-items');
    item.remove();
    refreshNumbers(list);
  });

  /* ── Image upload (event-delegated) ── */
  editor.addEventListener('click', function (e) {
    var btn = e.target.closest('.pe-img-change-btn');
    if (!btn) return;
    var field = btn.closest('.pe-field-image');
    if (field) field.querySelector('.pe-img-file').click();
  });

  editor.addEventListener('change', function (e) {
    var fileInput = e.target.closest('.pe-img-file');
    if (!fileInput) return;
    var file = fileInput.files[0];
    if (!file) return;
    var field    = fileInput.closest('.pe-field-image');
    var btn      = field.querySelector('.pe-img-change-btn');
    var hidden   = field.querySelector('input[type=hidden][data-key]');
    var nameSpan = field.querySelector('.pe-img-name');
    var img      = field.querySelector('.pe-img-preview');

    btn.textContent = 'Uploading...';
    btn.disabled = true;

    var fd = new FormData();
    fd.append('file', file);
    fd.append('dest', 'pages');
    palPost('api/upload-image.php', fd).then(function (res) {
      btn.textContent = 'Change Image';
      btn.disabled = false;
      fileInput.value = '';
      if (!res.ok) { showToast(res.message || 'Upload failed.', 'error'); return; }
      if (hidden)   hidden.value = res.filename;
      if (nameSpan) nameSpan.textContent = res.filename;
      if (img) { img.src = res.url; img.style.display = 'block'; }
      showToast('Image uploaded.', 'success');
    });
  });

  /* ── Build JSON from form ── */
  function buildJSON() {
    var result = {};

    editor.querySelectorAll(':scope > .pe-field[data-key]').forEach(function (field) {
      var key = field.dataset.key;
      var inp = field.querySelector('input[data-key], textarea[data-key]');
      if (!inp) return;
      if (inp.dataset.complex === 'true') {
        try { result[key] = JSON.parse(inp.value); } catch (e) { /* skip malformed */ }
      } else {
        result[key] = inp.value;
      }
    });

    editor.querySelectorAll(':scope > .pe-array-section[data-key]').forEach(function (section) {
      var key   = section.dataset.key;
      var isStr = section.dataset.arrayType === 'string';
      result[key] = Array.from(section.querySelectorAll('.pe-items > .pe-item')).map(function (item) {
        if (isStr) {
          var inp = item.querySelector('textarea[data-key="__str__"], input[data-key="__str__"]');
          return inp ? inp.value : '';
        }
        var obj = {};
        item.querySelectorAll('.block-body input[data-key], .block-body textarea[data-key]').forEach(function (inp) {
          obj[inp.dataset.key] = inp.value;
        });
        return obj;
      });
    });

    return result;
  }

  /* ── Save ── */
  async function savePage(data) {
    var saveBtn = document.getElementById('pe-save-btn');
    if (saveBtn) { saveBtn.disabled = true; saveBtn.textContent = 'Saving...'; }
    try {
      var res = await palPost('api/save-page.php', { slug: SLUG, data: data });
      showToast(res.message || (res.ok ? 'Page saved.' : 'Save failed.'), res.ok ? 'success' : 'error');
    } catch (ex) {
      showToast('Network error.', 'error');
    }
    if (saveBtn) { saveBtn.disabled = false; saveBtn.textContent = 'Save Page'; }
  }

  document.getElementById('pe-save-btn').addEventListener('click', function () {
    savePage(buildJSON());
  });

  /* ── Raw JSON panel ── */
  var rawPanel  = document.getElementById('pe-raw-panel');
  var rawTa     = document.getElementById('pe-raw-json');
  var rawToggle = document.getElementById('pe-raw-toggle');

  rawToggle.addEventListener('click', function () {
    var open = rawPanel.style.display !== 'none';
    if (!open) {
      rawTa.value = JSON.stringify(buildJSON(), null, 2);
      rawPanel.style.display = '';
      rawToggle.textContent = 'Hide JSON';
    } else {
      rawPanel.style.display = 'none';
      rawToggle.textContent = 'Raw JSON';
    }
  });

  document.getElementById('pe-raw-copy').addEventListener('click', function () {
    rawTa.value = JSON.stringify(buildJSON(), null, 2);
    navigator.clipboard.writeText(rawTa.value).then(function () {
      showToast('JSON copied to clipboard.', 'success');
    });
  });

  document.getElementById('pe-raw-apply').addEventListener('click', function () {
    try {
      var parsed = JSON.parse(rawTa.value);
      savePage(parsed);
    } catch (ex) {
      showToast('Invalid JSON: ' + ex.message, 'error');
    }
  });

})();
</script>
<script>
/* ── Icon picker for Page Editor ── */
(function () {
  var ICON_NAMES = ['award','shield','shield-check','user-check','leaf','tree','sun','droplet','plant','refresh',
    'users','heart','trending','star','briefcase','check-circle','target','globe','package','truck','mail','phone',
    'settings','clock','lock','key','zap','layers','dollar','recycle','bar-chart','map-pin','user','flag',
    'clipboard-check','database','wind','cpu','anchor','user-plus','search','calendar'];

  var ICON_PATHS = {
    'award':'<circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/>',
    'shield':'<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
    'shield-check':'<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/>',
    'user-check':'<path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/>',
    'leaf':'<path d="M11 20A7 7 0 019.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>',
    'tree':'<path d="M12 3L4 16h16L12 3z"/><line x1="12" y1="16" x2="12" y2="22"/><line x1="9" y1="22" x2="15" y2="22"/>',
    'sun':'<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>',
    'droplet':'<path d="M12 22a7 7 0 007-7c0-2-1-3.9-3-5.5S12.5 5 12 2.5C11.5 5 10 7.4 8 9c-2 1.6-3 3.5-3 5a7 7 0 007 7z"/>',
    'plant':'<path d="M12 22V12"/><path d="M12 12C12 7 18 3 22 3C22 8 17 12 12 12"/><path d="M12 12C12 8 6 4 2 4C2 9 7 12 12 12"/>',
    'refresh':'<polyline points="1 4 1 10 7 10"/><polyline points="23 20 23 14 17 14"/><path d="M20.49 9A9 9 0 005.64 5.64L1 10"/><path d="M3.51 15a9 9 0 0014.85 3.36L23 14"/>',
    'users':'<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>',
    'heart':'<path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>',
    'trending':'<polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>',
    'star':'<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>',
    'briefcase':'<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>',
    'check-circle':'<path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
    'target':'<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>',
    'globe':'<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/>',
    'package':'<path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>',
    'truck':'<rect x="1" y="3" width="15" height="13" rx="1"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
    'mail':'<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22 6 12 13 2 6"/>',
    'phone':'<path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 8.81a19.79 19.79 0 01-3.07-8.63A2 2 0 012 0h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 14.92v2z"/>',
    'settings':'<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>',
    'clock':'<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
    'lock':'<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>',
    'key':'<path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 11-7.778 7.778 5.5 5.5 0 017.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>',
    'zap':'<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>',
    'layers':'<polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>',
    'dollar':'<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>',
    'recycle':'<polyline points="7.6 14.6 2 12 7.6 9.4"/><path d="M22 12A10 10 0 007.6 2.6L2 5"/><polyline points="16.4 9.4 22 12 16.4 14.6"/><path d="M2 12a10 10 0 0014.4 9.4l5.6-2.4"/>',
    'bar-chart':'<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>',
    'map-pin':'<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>',
    'user':'<path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>',
    'flag':'<path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/>',
    'clipboard-check':'<path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/><polyline points="9 12 11 14 15 10"/>',
    'database':'<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>',
    'wind':'<path d="M9.59 4.59A2 2 0 1111 8H2m10.59 11.41A2 2 0 1114 16H2m15.73-8.27A2.5 2.5 0 1119.5 12H2"/>',
    'cpu':'<rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><line x1="9" y1="1" x2="9" y2="4"/><line x1="15" y1="1" x2="15" y2="4"/><line x1="9" y1="20" x2="9" y2="23"/><line x1="15" y1="20" x2="15" y2="23"/><line x1="20" y1="9" x2="23" y2="9"/><line x1="20" y1="14" x2="23" y2="14"/><line x1="1" y1="9" x2="4" y2="9"/><line x1="1" y1="14" x2="4" y2="14"/>',
    'anchor':'<circle cx="12" cy="5" r="3"/><line x1="12" y1="22" x2="12" y2="8"/><path d="M5 12H2a10 10 0 0020 0h-3"/>',
    'user-plus':'<path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>',
    'search':'<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
    'calendar':'<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>'
  };

  function iconSvg(name, size, stroke) {
    size = size || 22; stroke = stroke || 'currentColor';
    var p = ICON_PATHS[name] || ICON_PATHS['star'];
    return '<svg width="' + size + '" height="' + size + '" viewBox="0 0 24 24" fill="none" stroke="' + stroke + '" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">' + p + '</svg>';
  }

  /* Initialize SVG previews for icon fields that already have a value */
  function initIconPreviews(root) {
    (root || document).querySelectorAll('.pe-field-icon').forEach(function (field) {
      var hidden = field.querySelector('.pe-icon-hidden');
      var preview = field.querySelector('.pe-icon-svg-preview');
      var label = field.querySelector('.pe-icon-label');
      if (!hidden || !preview) return;
      var val = hidden.value.trim();
      if (val && ICON_PATHS[val]) {
        preview.innerHTML = iconSvg(val);
        if (label) { label.textContent = val; label.classList.remove('text-muted'); }
      }
    });
  }

  /* ── Modal wiring ── */
  var overlay   = document.getElementById('peIconOverlay');
  var grid      = document.getElementById('peIconGrid');
  var closeBtn  = document.getElementById('peIconClose');
  var cancelBtn = document.getElementById('peIconCancelBtn');
  var clearBtn  = document.getElementById('peIconClearBtn');

  if (!overlay || !grid) return;

  /* Populate grid once */
  ICON_NAMES.forEach(function (name) {
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'pe-icon-opt';
    btn.dataset.icon = name;
    btn.innerHTML = iconSvg(name, 24) + '<span>' + name + '</span>';
    grid.appendChild(btn);
  });

  var activeField = null;

  function openModal(field) {
    activeField = field;
    var current = field.querySelector('.pe-icon-hidden').value.trim();
    grid.querySelectorAll('.pe-icon-opt').forEach(function (b) {
      b.classList.toggle('selected', b.dataset.icon === current);
    });
    overlay.classList.add('active');
  }

  function closeModal() {
    overlay.classList.remove('active');
    activeField = null;
  }

  function applyIcon(name) {
    if (!activeField) return;
    var hidden  = activeField.querySelector('.pe-icon-hidden');
    var preview = activeField.querySelector('.pe-icon-svg-preview');
    var label   = activeField.querySelector('.pe-icon-label');
    if (hidden)  hidden.value = name;
    if (preview) preview.innerHTML = name ? iconSvg(name) : '';
    if (label)  { label.textContent = name || 'No icon selected'; label.classList.toggle('text-muted', !name); }
    closeModal();
  }

  closeBtn.addEventListener('click', closeModal);
  cancelBtn.addEventListener('click', closeModal);
  overlay.addEventListener('click', function (e) { if (e.target === overlay) closeModal(); });

  clearBtn.addEventListener('click', function () { applyIcon(''); });

  grid.addEventListener('click', function (e) {
    var btn = e.target.closest('.pe-icon-opt');
    if (btn) applyIcon(btn.dataset.icon);
  });

  /* Event delegation: open modal when any "Choose Icon" button is clicked */
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.pe-icon-pick-btn');
    if (!btn) return;
    var field = btn.closest('.pe-field-icon');
    if (field) openModal(field);
  });

  /* Run on load to show SVGs for pre-filled values */
  document.addEventListener('DOMContentLoaded', function () { initIconPreviews(); });
  /* Also run now in case DOMContentLoaded already fired */
  if (document.readyState !== 'loading') initIconPreviews();

})();
</script>
<?php endif; ?>
</body>
</html>
