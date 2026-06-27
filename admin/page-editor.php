<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';
sendSecurityHeaders();
requireLogin();
$pageTitle = 'Page Editor';
$pages = glob(DATA_DIR . 'pages/*.json') ?: [];
$sel = null; $selName = ''; $data = []; $success = false; $error = '';

if (isset($_GET['p'])) {
    $slug = preg_replace('/[^a-z0-9_-]/', '', strtolower($_GET['p']));
    $path = DATA_DIR . 'pages/' . $slug . '.json';
    if (is_file($path)) { $sel = $path; $selName = $slug; $data = jsonRead($sel); }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $sel) {
    requireCsrf();
    $raw = json_decode($_POST['json_data'] ?? '{}', true);
    if (json_last_error() !== JSON_ERROR_NONE) { $error = 'Invalid JSON. Check syntax and try again.'; }
    else {
        array_walk_recursive($raw, function (&$v) { if (is_string($v)) $v = strip_tags($v); });
        jsonWrite($sel, $raw);
        $data = $raw; $success = true;
    }
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
  <?php if ($success): ?><div class="alert alert-success">Page data saved successfully.</div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><?php echo e($error); ?></div><?php endif; ?>
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card mb-3">
        <div class="card-header">Pages</div>
        <ul class="list-group list-group-flush">
          <?php foreach ($pages as $p):
            $s = basename($p, '.json'); ?>
          <li class="list-group-item <?php echo $s === $selName ? 'active' : ''; ?>">
            <a href="page-editor.php?p=<?php echo e(urlencode($s)); ?>"><?php echo e($s); ?></a>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="card">
        <div class="card-header">Upload Image</div>
        <div class="card-body">
          <p class="small text-muted mb-2">Upload an image, then copy its path into the JSON editor.</p>
          <input type="file" id="peImgFile" class="form-control mb-2" accept="image/*">
          <button type="button" class="btn btn-primary btn-sm w-100" id="peImgBtn">Upload</button>
          <div id="peImgList" class="mt-2"></div>
        </div>
      </div>
    </div>

    <div class="col-md-9">
      <?php if ($sel): ?>
      <div class="card">
        <div class="card-header">Editing: <strong><?php echo e($selName); ?>.json</strong></div>
        <div class="card-body">
          <p class="small text-muted mb-2">Edit the text values. For images, upload above and paste the path shown. Do not change key names.</p>
          <form method="post">
            <?php echo csrfField(); ?>
            <textarea name="json_data" id="jsonEditor" class="form-control json-editor" rows="32" spellcheck="false"><?php echo e(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?></textarea>
            <div class="mt-3" style="display:flex;gap:.5rem">
              <button type="submit" class="btn btn-primary">Save Page Data</button>
              <button type="button" class="btn btn-outline" id="fmtBtn">Format JSON</button>
            </div>
          </form>
        </div>
      </div>
      <?php else: ?>
      <div class="card"><div class="card-body text-muted">Select a page from the list to edit its content.</div></div>
      <?php endif; ?>
    </div>
  </div>
</div>
</div>
<div id="toast-container"></div>
<script src="assets/admin.js"></script>
<script>
(function () {
  var ed = document.getElementById('jsonEditor');
  var fb = document.getElementById('fmtBtn');
  if (fb && ed) {
    fb.addEventListener('click', function () {
      try {
        ed.value = JSON.stringify(JSON.parse(ed.value), null, 2);
        showToast('Formatted.', 'success');
      } catch (e) {
        showToast('Invalid JSON: ' + e.message, 'error');
      }
    });
  }

  var peBtn = document.getElementById('peImgBtn');
  if (peBtn) {
    peBtn.addEventListener('click', async function () {
      var file = document.getElementById('peImgFile').files[0];
      if (!file) { showToast('Select an image file first.', 'warning'); return; }
      var fd = new FormData();
      fd.append('file', file);
      fd.append('dest', 'pages');
      peBtn.disabled = true;
      peBtn.textContent = 'Uploading...';
      var res = await palPost('api/upload-image.php', fd);
      peBtn.disabled = false;
      peBtn.textContent = 'Upload';
      if (!res.ok) { showToast(res.message || 'Upload failed.', 'error'); return; }
      var list = document.getElementById('peImgList');
      var div  = document.createElement('div');
      div.style.cssText = 'border-top:1px solid var(--border);padding-top:.5rem;margin-top:.5rem';
      div.innerHTML = '<img src="' + res.url + '" style="max-width:100%;max-height:80px;border-radius:4px;display:block;margin-bottom:.3rem">'
        + '<code style="font-size:.75rem;background:#f0f2f5;padding:.15rem .35rem;border-radius:3px;cursor:pointer;display:block;word-break:break-all" '
        + 'onclick="navigator.clipboard.writeText(\'' + res.path.replace(/'/g, "\\'") + '\').then(function(){showToast(\'Path copied!\',\'success\')})" '
        + 'title="Click to copy path">' + res.path + '</code>';
      list.prepend(div);
      showToast('Uploaded. Click path below to copy.', 'success');
    });
  }
})();
</script>
</body>
</html>
