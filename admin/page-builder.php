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

if (isset($_GET['p'])) {
    $slug = preg_replace('/[^a-z0-9_-]/', '', strtolower($_GET['p']));
    $path = $customDir . $slug . '.json';
    if (is_file($path)) { $sel = $path; $selName = $slug; $data = jsonRead($sel); }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $slug = preg_replace('/[^a-z0-9_-]/', '', strtolower($_POST['slug'] ?? ''));
        $title = sanitize($_POST['title'] ?? '');
        if (!$slug) { $error = 'Invalid slug.'; }
        else {
            $newPath = $customDir . $slug . '.json';
            if (is_file($newPath)) { $error = 'Page already exists.'; }
            else {
                jsonWrite($newPath, ['title' => $title, 'sections' => []]);
                header('Location: page-builder.php?p=' . urlencode($slug)); exit;
            }
        }
    } elseif ($action === 'delete' && $sel) {
        @unlink($sel); header('Location: page-builder.php'); exit;
    } elseif ($action === 'save' && $sel) {
        $raw = json_decode($_POST['json_data'] ?? '{}', true);
        if (json_last_error() !== JSON_ERROR_NONE) { $error = 'Invalid JSON.'; }
        else { array_walk_recursive($raw, function(&$v){ if (is_string($v)) $v = strip_tags($v); }); jsonWrite($sel, $raw); $data = $raw; $success = true; }
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
  <div class="row">
    <div class="col-md-3">
      <div class="card mb-3">
        <div class="card-header">Custom Pages</div>
        <ul class="list-group list-group-flush">
          <?php if (empty($pages)): ?><li class="list-group-item text-muted small">None yet</li><?php endif; ?>
          <?php foreach ($pages as $p): $slug2 = basename($p, '.json'); ?>
          <li class="list-group-item <?php echo $slug2 === $selName ? 'active' : ''; ?>">
            <a href="page-builder.php?p=<?php echo e(urlencode($slug2)); ?>"><?php echo e($slug2); ?></a>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="card">
        <div class="card-header">Create Page</div>
        <div class="card-body">
          <form method="post">
            <?php echo csrfField(); ?>
            <input type="hidden" name="action" value="create">
            <div class="form-group mb-2"><label>Slug</label><input type="text" name="slug" class="form-control form-control-sm" placeholder="e.g. csr-report" pattern="[a-z0-9_-]+"></div>
            <div class="form-group mb-2"><label>Title</label><input type="text" name="title" class="form-control form-control-sm"></div>
            <button type="submit" class="btn btn-primary btn-sm w-100">Create</button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-9">
      <?php if ($sel): ?>
      <div class="card">
        <div class="card-header d-flex-between">
          <span>Editing: <strong><?php echo e($selName); ?>.json</strong></span>
          <form method="post" style="display:inline" onsubmit="return confirm('Delete this page?')">
            <?php echo csrfField(); ?>
            <input type="hidden" name="action" value="delete">
            <button type="submit" class="btn btn-danger btn-sm">Delete Page</button>
          </form>
        </div>
        <div class="card-body">
          <form method="post">
            <?php echo csrfField(); ?>
            <input type="hidden" name="action" value="save">
            <textarea name="json_data" class="form-control json-editor" rows="30" spellcheck="false"><?php echo e(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?></textarea>
            <div class="mt-3">
              <button type="submit" class="btn btn-primary">Save</button>
              <button type="button" class="btn btn-outline ml-2" id="fmtBtn">Format JSON</button>
            </div>
          </form>
        </div>
      </div>
      <?php else: ?><div class="card"><div class="card-body text-muted">Select or create a page.</div></div><?php endif; ?>
    </div>
  </div>
</div>
</div>
<div id="toast-container"></div>
<script src="assets/admin.js"></script>
<script>
var ed = document.querySelector('.json-editor');
var fb = document.getElementById('fmtBtn');
if (fb && ed) fb.addEventListener('click', function(){
  try { ed.value = JSON.stringify(JSON.parse(ed.value), null, 2); showToast('Formatted.', 'success'); }
  catch(e) { showToast('Invalid JSON.', 'error'); }
});
</script>
</body>
</html>
