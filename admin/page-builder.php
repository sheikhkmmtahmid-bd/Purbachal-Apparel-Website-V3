<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';
sendSecurityHeaders();
requireLogin();
$pageTitle = 'Page Builder';
$customDir = DATA_DIR . 'custom/';
if (!is_dir($customDir)) mkdir($customDir, 0755, true);
$pages = glob($customDir . '*.json') ?: [];
$sel = null; $selName = ''; $data = []; $success = false; $error = '';

/* ── Handle create / delete form POSTs ────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $newSlug  = preg_replace('/[^a-z0-9_-]/', '', strtolower($_POST['slug'] ?? ''));
        $newTitle = sanitize($_POST['title'] ?? '', 200);
        if (!$newSlug) { $error = 'Slug is required and must use only a-z, 0-9, hyphens.'; }
        elseif (is_file($customDir . $newSlug . '.json')) { $error = 'A page with that slug already exists.'; }
        else {
            jsonWrite($customDir . $newSlug . '.json', ['title' => $newTitle ?: $newSlug, 'sections' => []]);
            $nav = jsonRead(DATA_DIR . 'nav.json');
            $nav['pages'][] = [
                'slug'     => $newSlug,
                'label'    => $newTitle ?: $newSlug,
                'file'     => 'page.php?p=' . $newSlug,
                'order'    => 99,
                'builtin'  => false,
                'dropdown' => [],
            ];
            jsonWrite(DATA_DIR . 'nav.json', $nav);
            header('Location: page-builder.php?p=' . urlencode($newSlug)); exit;
        }

    } elseif ($action === 'delete') {
        $delSlug = preg_replace('/[^a-z0-9_-]/', '', strtolower($_POST['slug'] ?? ''));
        $delPath = $customDir . $delSlug . '.json';
        if ($delSlug && is_file($delPath)) {
            @unlink($delPath);
            $nav = jsonRead(DATA_DIR . 'nav.json');
            $nav['pages'] = array_values(array_filter($nav['pages'], function ($p) use ($delSlug) {
                return ($p['file'] ?? '') !== 'page.php?p=' . $delSlug;
            }));
            jsonWrite(DATA_DIR . 'nav.json', $nav);
        }
        header('Location: page-builder.php'); exit;
    }
}

if (isset($_GET['p'])) {
    $selName = preg_replace('/[^a-z0-9_-]/', '', strtolower($_GET['p']));
    $selPath = $customDir . $selName . '.json';
    if (is_file($selPath)) { $sel = $selPath; $data = jsonRead($selPath); }
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
</head>
<body class="admin-body">
<?php require_once __DIR__ . '/partials/sidebar.php'; ?>
<div class="admin-main">
<?php require_once __DIR__ . '/partials/topbar.php'; ?>
<div class="admin-content">
  <?php if ($error): ?><div class="alert alert-danger"><?php echo e($error); ?></div><?php endif; ?>

  <div class="row">
    <!-- Left: page list + create form -->
    <div class="col-md-3">
      <div class="card mb-3">
        <div class="card-header">Custom Pages</div>
        <ul class="list-group list-group-flush">
          <?php if (empty($pages)): ?>
          <li class="list-group-item text-muted small">No custom pages yet.</li>
          <?php endif; ?>
          <?php foreach ($pages as $p): $s2 = basename($p, '.json'); ?>
          <li class="list-group-item <?php echo $s2 === $selName ? 'active' : ''; ?>" style="display:flex;align-items:center;justify-content:space-between">
            <a href="page-builder.php?p=<?php echo e(urlencode($s2)); ?>"><?php echo e($s2); ?></a>
            <?php if ($s2 !== $selName): ?>
            <form method="post" style="margin:0" onsubmit="return confirm('Delete page <?php echo e($s2); ?> and remove from navbar?')">
              <?php echo csrfField(); ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="slug" value="<?php echo e($s2); ?>">
              <button type="submit" class="btn btn-danger btn-sm" style="padding:.15rem .4rem;font-size:.7rem">Del</button>
            </form>
            <?php endif; ?>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="card">
        <div class="card-header">Create New Page</div>
        <div class="card-body">
          <form method="post">
            <?php echo csrfField(); ?>
            <input type="hidden" name="action" value="create">
            <div class="form-group mb-2">
              <label class="form-label">Slug</label>
              <input type="text" name="slug" class="form-control form-control-sm" placeholder="e.g. csr-report" pattern="[a-z0-9_-]+" required>
              <small class="text-muted">Used in URL: /page.php?p=slug</small>
            </div>
            <div class="form-group mb-3">
              <label class="form-label">Page Title</label>
              <input type="text" name="title" class="form-control form-control-sm" placeholder="e.g. CSR Report">
            </div>
            <button type="submit" class="btn btn-primary btn-sm w-100">Create + Add to Navbar</button>
          </form>
        </div>
      </div>

      <?php if ($sel): ?>
      <div class="card mt-3">
        <div class="card-header" style="color:var(--danger)">Delete This Page</div>
        <div class="card-body">
          <form method="post" onsubmit="return confirm('Permanently delete page &quot;<?php echo e($selName); ?>&quot; and remove it from the navbar?')">
            <?php echo csrfField(); ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="slug" value="<?php echo e($selName); ?>">
            <button type="submit" class="btn btn-danger btn-sm w-100">Delete &amp; Remove from Navbar</button>
          </form>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Right: block editor -->
    <div class="col-md-9">
      <?php if (!$sel): ?>
      <div class="card"><div class="card-body text-muted">Select a page from the list or create a new one.</div></div>
      <?php else: ?>
      <div class="card mb-3">
        <div class="card-header">
          Editing: <strong><?php echo e($selName); ?></strong>
          &nbsp;<a href="../page.php?p=<?php echo e(urlencode($selName)); ?>" target="_blank" class="btn btn-outline btn-sm">Preview</a>
        </div>
        <div class="card-body">
          <div class="form-group mb-0">
            <label class="form-label">Page Title</label>
            <input type="text" id="pbTitle" class="form-control" value="<?php echo e($data['title'] ?? $selName); ?>">
          </div>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header">Add Block</div>
        <div class="card-body block-add-bar">
          <button type="button" class="btn-block-add" onclick="addBlock('hero')">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="vertical-align:middle;margin-right:4px"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>Hero Banner
          </button>
          <button type="button" class="btn-block-add" onclick="addBlock('text_block')">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="vertical-align:middle;margin-right:4px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h12"/></svg>Text Block
          </button>
          <button type="button" class="btn-block-add" onclick="addBlock('cta_banner')">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="vertical-align:middle;margin-right:4px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>CTA Banner
          </button>
          <button type="button" class="btn-block-add" onclick="addBlock('image_grid')">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="vertical-align:middle;margin-right:4px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>Image Grid
          </button>
        </div>
      </div>

      <div id="blockList" class="mb-3"></div>

      <div style="display:flex;gap:.75rem">
        <button type="button" class="btn btn-primary" id="pbSaveBtn" onclick="savePage()">Save Page</button>
        <span id="pbStatus" class="text-muted small" style="align-self:center"></span>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
</div>
<div id="toast-container"></div>

<script id="pbInitData" type="application/json"><?php echo json_encode($data['sections'] ?? [], JSON_HEX_TAG | JSON_UNESCAPED_UNICODE); ?></script>
<input type="hidden" id="pbSlug" value="<?php echo e($selName); ?>">

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="assets/admin.js"></script>
<script>
'use strict';

/* ── Block type labels ─────────────────────────────────────── */
var BT_LABELS = { hero: 'Hero Banner', text_block: 'Text Block', cta_banner: 'CTA Banner', image_grid: 'Image Grid' };

/* ── State ─────────────────────────────────────────────────── */
var blocks = [];
var pbSlug = document.getElementById('pbSlug') ? document.getElementById('pbSlug').value : '';
var pbSortable = null;

/* ── Initialise from server data ───────────────────────────── */
(function () {
  var el = document.getElementById('pbInitData');
  if (!el) return;
  try { blocks = JSON.parse(el.textContent) || []; } catch (e) { blocks = []; }
  renderBlocks();
})();

/* ── Unique ID generator ────────────────────────────────────── */
function uid() {
  return 'b' + Date.now().toString(36) + Math.random().toString(36).slice(2, 7);
}

/* ── HTML escape helpers ────────────────────────────────────── */
function esc(s) {
  return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}
function escAttr(s) {
  return String(s || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

/* ── Collect all block data from DOM (current sort order) ───── */
function collectBlocks() {
  var items = document.querySelectorAll('#blockList .block-item');
  var result = [];
  items.forEach(function (el) {
    var id   = el.dataset.id;
    var type = el.dataset.type;
    var data = {};
    el.querySelectorAll('.blk-text').forEach(function (f) { data[f.dataset.key] = f.value; });
    el.querySelectorAll('.blk-img-val').forEach(function (f) { data[f.dataset.key] = f.value; });
    el.querySelectorAll('.blk-grid-val').forEach(function (f) {
      try { data[f.dataset.key] = JSON.parse(f.value); } catch (e) { data[f.dataset.key] = []; }
    });
    result.push({ id: id, type: type, data: data });
  });
  return result;
}

/* ── Add new block ──────────────────────────────────────────── */
function addBlock(type) {
  blocks = collectBlocks();
  var b = { id: uid(), type: type, data: {} };
  blocks.push(b);
  renderBlocks();
  var list = document.getElementById('blockList');
  if (list) {
    var last = list.querySelector('.block-item:last-child');
    if (last) { var body = last.querySelector('.block-body'); if (body) body.style.display = ''; }
  }
}

/* ── Delete block ───────────────────────────────────────────── */
function delBlock(id) {
  if (!confirm('Remove this block?')) return;
  blocks = collectBlocks().filter(function (b) { return b.id !== id; });
  renderBlocks();
}

/* ── Toggle block form open/close ───────────────────────────── */
function toggleBlock(id) {
  var body = document.getElementById('bb_' + id);
  if (body) body.style.display = body.style.display === 'none' ? '' : 'none';
}

/* ── Build form HTML for each block type ───────────────────── */
function blockFormHTML(b) {
  var id = b.id, d = b.data || {}, h = '';

  function fText(key, label, val, placeholder) {
    return '<div class="form-group mb-2"><label class="form-label">' + esc(label) + '</label>'
      + '<input type="text" class="form-control blk-text" data-key="' + key + '" value="' + escAttr(val) + '" placeholder="' + escAttr(placeholder || '') + '"></div>';
  }
  function fTextarea(key, label, val) {
    return '<div class="form-group mb-2"><label class="form-label">' + esc(label) + '</label>'
      + '<textarea class="form-control blk-text" data-key="' + key + '" rows="3">' + esc(val) + '</textarea></div>';
  }
  function fSelect(key, label, val, opts) {
    var opts_html = opts.map(function (o) { return '<option value="' + escAttr(o) + '"' + (o === val ? ' selected' : '') + '>' + esc(o) + '</option>'; }).join('');
    return '<div class="form-group mb-2"><label class="form-label">' + esc(label) + '</label>'
      + '<select class="form-control blk-text" data-key="' + key + '">' + opts_html + '</select></div>';
  }
  function fImage(key, label, val) {
    var preview = val ? '<img src="../' + escAttr(val) + '" class="img-preview" style="display:block;max-width:180px;max-height:110px;border-radius:6px;margin:.4rem 0">' : '<img src="" class="img-preview" style="display:none;max-width:180px;max-height:110px;border-radius:6px;margin:.4rem 0">';
    return '<div class="form-group mb-2"><label class="form-label">' + esc(label) + '</label>'
      + '<input type="hidden" class="blk-img-val" data-key="' + key + '" value="' + escAttr(val) + '">'
      + preview
      + '<input type="file" class="form-control blk-img-upload" data-key="' + key + '" accept="image/*">'
      + (val ? '<small class="text-muted">' + esc(val.split('/').pop()) + '</small>' : '') + '</div>';
  }
  function fGrid(val) {
    var images = Array.isArray(val) ? val : [];
    var thumbs = images.map(function (img, i) {
      return '<div class="gallery-item" data-file="' + escAttr(img) + '">'
        + '<img src="../' + escAttr(img) + '" alt="">'
        + '<button class="gallery-delete" type="button" onclick="removeGridImg(this)">&times;</button></div>';
    }).join('');
    return '<div class="form-group mb-2"><label class="form-label">Images</label>'
      + '<input type="hidden" class="blk-grid-val" data-key="images" value="' + escAttr(JSON.stringify(images)) + '">'
      + '<div class="gallery-grid blk-grid" id="grid_' + id + '">' + thumbs + '</div>'
      + '<input type="file" class="form-control mt-2 blk-grid-upload" accept="image/*" multiple><small class="text-muted">Select one or more images to upload.</small></div>';
  }

  if (b.type === 'hero') {
    h += fText('headline', 'Headline', d.headline || '', 'Main headline text');
    h += fText('subheadline', 'Subheadline', d.subheadline || '', 'Supporting text below headline');
    h += fText('cta_text', 'Button Text', d.cta_text || '', 'e.g. Contact Us');
    h += fText('cta_url', 'Button URL', d.cta_url || '', 'e.g. contact.php');
    h += fImage('bg_image', 'Background Image (optional)', d.bg_image || '');

  } else if (b.type === 'text_block') {
    h += fText('heading', 'Heading', d.heading || '', 'Section heading');
    h += fTextarea('body', 'Body Text', d.body || '');
    h += fImage('image', 'Side Image (optional)', d.image || '');
    h += fSelect('image_side', 'Image Side', d.image_side || 'right', ['right', 'left']);

  } else if (b.type === 'cta_banner') {
    h += fText('heading', 'Heading', d.heading || '', 'e.g. Ready to work with us?');
    h += fTextarea('text', 'Body Text', d.text || '');
    h += fText('btn_text', 'Button Text', d.btn_text || '', 'e.g. Contact Us');
    h += fText('btn_url', 'Button URL', d.btn_url || '', 'e.g. contact.php');

  } else if (b.type === 'image_grid') {
    h += fText('heading', 'Section Heading (optional)', d.heading || '', 'e.g. Our Gallery');
    h += fGrid(d.images);
  }
  return h;
}

/* ── Render all blocks to DOM ───────────────────────────────── */
function renderBlocks() {
  var list = document.getElementById('blockList');
  if (!list) return;
  list.innerHTML = '';

  blocks.forEach(function (b) {
    var el = document.createElement('div');
    el.className = 'block-item';
    el.dataset.id   = b.id;
    el.dataset.type = b.type;
    el.innerHTML = '<div class="block-header">'
      + '<span class="drag-handle"><svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg></span>'
      + '<span class="block-label">' + esc(BT_LABELS[b.type] || b.type) + '</span>'
      + '<div style="margin-left:auto;display:flex;gap:.4rem">'
      + '<button type="button" class="btn btn-outline btn-sm" onclick="toggleBlock(\'' + b.id + '\')">Edit</button>'
      + '<button type="button" class="btn btn-danger btn-sm" onclick="delBlock(\'' + b.id + '\')">Remove</button>'
      + '</div></div>'
      + '<div class="block-body" id="bb_' + b.id + '" style="display:none">' + blockFormHTML(b) + '</div>';
    list.appendChild(el);
  });

  if (pbSortable) pbSortable.destroy();
  pbSortable = Sortable.create(list, { handle: '.drag-handle', animation: 150 });

  list.querySelectorAll('.blk-img-upload').forEach(function (inp) {
    inp.addEventListener('change', handleImgUpload);
  });
  list.querySelectorAll('.blk-grid-upload').forEach(function (inp) {
    inp.addEventListener('change', handleGridUpload);
  });
}

/* ── Upload single image for a field ───────────────────────── */
async function handleImgUpload(e) {
  var inp  = e.target;
  var file = inp.files[0];
  if (!file) return;
  var fd = new FormData();
  fd.append('file', file);
  fd.append('dest', 'pages');
  inp.disabled = true;
  var res = await palPost('api/upload-image.php', fd);
  inp.disabled = false;
  inp.value = '';
  if (!res.ok) { showToast(res.message || 'Upload failed.', 'error'); return; }
  var group = inp.closest('.form-group');
  var hidden  = group.querySelector('.blk-img-val');
  var preview = group.querySelector('.img-preview');
  if (hidden)  hidden.value = res.path;
  if (preview) { preview.src = res.url; preview.style.display = 'block'; }
  showToast('Image uploaded.', 'success');
}

/* ── Upload images for a grid block ────────────────────────── */
async function handleGridUpload(e) {
  var inp   = e.target;
  var files = Array.from(inp.files);
  var blkEl = inp.closest('.block-item');
  if (!blkEl) return;
  var grid  = blkEl.querySelector('.blk-grid');
  inp.disabled = true;
  for (var i = 0; i < files.length; i++) {
    var fd = new FormData();
    fd.append('file', files[i]);
    fd.append('dest', 'pages');
    var res = await palPost('api/upload-image.php', fd);
    if (!res.ok) { showToast(res.message || 'One upload failed.', 'error'); continue; }
    if (grid) {
      var div = document.createElement('div');
      div.className = 'gallery-item';
      div.dataset.file = res.path;
      div.innerHTML = '<img src="' + res.url + '" alt=""><button class="gallery-delete" type="button" onclick="removeGridImg(this)">&times;</button>';
      grid.appendChild(div);
      syncGridHidden(grid);
    }
  }
  inp.disabled = false;
  inp.value = '';
  showToast('Images uploaded.', 'success');
}

/* ── Remove an image from a grid ────────────────────────────── */
function removeGridImg(btn) {
  var item = btn.closest('.gallery-item');
  var grid = btn.closest('.blk-grid');
  if (item) item.remove();
  if (grid) syncGridHidden(grid);
}

/* ── Sync hidden JSON input with grid thumbnails ─────────────── */
function syncGridHidden(grid) {
  var images = Array.from(grid.querySelectorAll('.gallery-item')).map(function (el) { return el.dataset.file; });
  var hidden = grid.parentElement.querySelector('.blk-grid-val');
  if (hidden) hidden.value = JSON.stringify(images);
}

/* ── Save page via AJAX ─────────────────────────────────────── */
async function savePage() {
  if (!pbSlug) return;
  var title    = document.getElementById('pbTitle').value;
  var sections = collectBlocks();
  var btn      = document.getElementById('pbSaveBtn');
  var status   = document.getElementById('pbStatus');
  btn.disabled = true;
  if (status) status.textContent = 'Saving...';
  var res = await palPost('api/save-custom-page.php', { slug: pbSlug, title: title, sections: sections });
  btn.disabled = false;
  if (status) status.textContent = '';
  showToast(res.message || (res.ok ? 'Saved' : 'Error'), res.ok ? 'success' : 'error');
}
</script>
</body>
</html>
