<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';
sendSecurityHeaders();
requireLogin();
$pageTitle = 'Footer Editor';
$footer = jsonRead(DATA_DIR . 'footer.json');
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $footer['brand_desc'] = sanitize($_POST['brand_desc'] ?? '', 500);
    $footer['pdf'] = [
        'label' => sanitize($_POST['pdf_label'] ?? ''),
        'title' => sanitize($_POST['pdf_title'] ?? ''),
        'url'   => sanitize($_POST['pdf_url'] ?? ''),
    ];
    jsonWrite(DATA_DIR . 'footer.json', $footer);
    $success = true;
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
  <?php if ($success): ?><div class="alert alert-success">Footer saved.</div><?php endif; ?>
  <form method="post">
    <?php echo csrfField(); ?>
    <div class="card mb-4">
      <div class="card-header">Brand Description</div>
      <div class="card-body">
        <textarea name="brand_desc" class="form-control" rows="4"><?php echo e($footer['brand_desc'] ?? ''); ?></textarea>
      </div>
    </div>
    <div class="card mb-4">
      <div class="card-header">PDF / Download Card</div>
      <div class="card-body">
        <div class="form-group mb-3"><label>Label (e.g. "Company Profile")</label><input type="text" name="pdf_label" class="form-control" value="<?php echo e($footer['pdf']['label'] ?? ''); ?>"></div>
        <div class="form-group mb-3"><label>Title</label><input type="text" name="pdf_title" class="form-control" value="<?php echo e($footer['pdf']['title'] ?? ''); ?>"></div>
        <div class="form-group"><label>PDF URL</label><input type="url" name="pdf_url" class="form-control" value="<?php echo e($footer['pdf']['url'] ?? ''); ?>"></div>
      </div>
    </div>
    <button type="submit" class="btn btn-primary">Save Footer</button>
  </form>
</div>
</div>
<div id="toast-container"></div>
<script src="assets/admin.js"></script>
</body>
</html>
