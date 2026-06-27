<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';
sendSecurityHeaders();
requireLogin();
$pageTitle = 'Page Editor';
$pages = glob(DATA_DIR . 'pages/*.json');
$sel = null; $selName = ''; $data = []; $success = false; $error = '';

if (isset($_GET['p'])) {
    $slug = preg_replace('/[^a-z0-9_-]/', '', strtolower($_GET['p']));
    $path = DATA_DIR . 'pages/' . $slug . '.json';
    if (is_file($path)) { $sel = $path; $selName = $slug; $data = jsonRead($sel); }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $sel) {
    requireCsrf();
    // Deep sanitise all string values in decoded JSON
    $raw = json_decode($_POST['json_data'] ?? '{}', true);
    if (json_last_error() !== JSON_ERROR_NONE) { $error = 'Invalid JSON.'; }
    else {
        array_walk_recursive($raw, function(&$v){ if (is_string($v)) $v = strip_tags($v); });
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
<title><?php echo e($pageTitle); ?> &mdash; PAL CMS</title>
<link rel="stylesheet" href="assets/admin.css">
<?php echo csrfMeta(); ?>
</head>
<body class="admin-body">
<?php require_once __DIR__ . '/partials/sidebar.php'; ?>
<div class="admin-main">
<?php require_once __DIR__ . '/partials/topbar.php'; ?>
<div class="admin-content">
  <?php if ($success): ?><div class="alert alert-success">Page saved.</div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><?php echo e($error); ?></div><?php endif; ?>
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card">
        <div class="card-header">Pages</div>
        <ul class="list-group list-group-flush">
          <?php foreach ($pages as $p):
            $slug = basename($p, '.json'); ?>
          <li class="list-group-item <?php echo $slug === $selName ? 'active' : ''; ?>">
            <a href="page-editor.php?p=<?php echo e(urlencode($slug)); ?>"><?php echo e($slug); ?></a>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
    <div class="col-md-9">
      <?php if ($sel): ?>
      <div class="card">
        <div class="card-header">Editing: <strong><?php echo e($selName); ?>.json</strong></div>
        <div class="card-body">
          <form method="post">
            <?php echo csrfField(); ?>
            <textarea name="json_data" id="jsonEditor" class="form-control json-editor" rows="30" spellcheck="false"><?php echo e(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?></textarea>
            <div class="mt-3">
              <button type="submit" class="btn btn-primary">Save Page Data</button>
              <button type="button" class="btn btn-outline ml-2" id="formatJson">Format JSON</button>
            </div>
          </form>
        </div>
      </div>
      <?php else: ?>
      <div class="card"><div class="card-body text-muted">Select a page from the list to edit its data.</div></div>
      <?php endif; ?>
    </div>
  </div>
</div>
</div>
<div id="toast-container"></div>
<script src="assets/admin.js"></script>
<script>
var ed = document.getElementById('jsonEditor');
if (ed) {
  document.getElementById('formatJson').addEventListener('click', function(){
    try { ed.value = JSON.stringify(JSON.parse(ed.value), null, 2); showToast('Formatted.', 'success'); }
    catch(e) { showToast('Invalid JSON: ' + e.message, 'error'); }
  });
}
</script>
</body>
</html>
