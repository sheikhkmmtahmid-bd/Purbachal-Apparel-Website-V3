<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';
sendSecurityHeaders();
requireLogin();
$pageTitle = 'Dashboard';
$site = jsonRead(DATA_DIR . 'site.json');

function countFiles(string $dir): int {
    if (!is_dir($dir)) return 0;
    return count(array_filter(scandir($dir), fn($f) => !in_array($f, ['.', '..']) && is_file($dir . DIRECTORY_SEPARATOR . $f)));
}
$stats = [
  'pagessq_ => count(glob(DATA_DIR . 'pages/*.json')),
  'galleryimg'  => countFiles(UPLOAD_DIR . 'gallery/'),
  'productsimg' => countFiles(UPLOAD_DIR . 'products/kids/') + countFiles(UPLOAD_DIR . 'products/mens/') + countFiles(UPLOAD_DIR . 'products/womens/'),
  'customdata'  => count(glob(DATA_DIR . 'custom/*.json')),
];
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
  <div class="dash-cards">
    <a class="dash-card" href="site-settings.php">
      <div class="dash-card-icon dash-icon-teal"><svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
      <div class="dash-card-body"><h3>Site Settings</h3><p>Company info, logo, favicon, social links</p></div>
      <div class="dash-card-arrow"><svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></div>
    </a>
    <a class="dash-card" href="nav-manager.php">
      <div class="dash-card-icon dash-icon-navy"><svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg></div>
      <div class="dash-card-body"><h3>Navigation</h3><p>Reorder pages, edit labels &amp; CTAs</p></div>
      <div class="dash-card-arrow"><svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></div>
    </a>
    <a class="dash-card" href="page-editor.php">
      <div class="dash-card-icon dash-icon-teal"><svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></div>
      <div class="dash-card-body"><h3>Page Editor</h3><p><?php echo $stats['pages']]; ?> pages available</p></div>
      <div class="dash-card-arrow"><svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></div>
    </a>
    <a class="dash-card" href="gallery-manager.php">
      <div class="dash-card-icon dash-icon-navy"><svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
      <div class="dash-card-body"><h3>Gallery</h3><p><?php echo $stats['galleryimg']; ?> images uploaded</p></div>
      <div class="dash-card-arrow"><svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></div>
    </a>
    <a class="dash-card" href="products-manager.php">
      <div class="dash-card-icon dash-icon-teal"><svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg></div>
      <div class="dash-card-body"><h3>Products</h3><p><?php echo $stats['productsimg']; ?> product images</p></div>
      <div class="dash-card-arrow"><svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></div>
    </a>
    <a class="dash-card" href="footer-editor.php">
      <div class="dash-card-icon dash-icon-navy"><svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg></div>
      <div class="dash-card-body"><h3>Footer Editor</h3><p>Brand text, PDF card link</p></div>
      <div class="dash-card-arrow"><svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></div>
    </a>
    <a class="dash-card" href="page-builder.php">
      <div class="dash-card-icon dash-icon-teal"><svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg></div>
      <div class="dash-card-body"><h3>Page Builder</h3><p>Create and manage custom pages</p></div>
      <div class="dash-card-arrow"><svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></div>
    </a>
    <a class="dash-card" href="../index.php" target="_blank">
      <div class="dash-card-icon dash-icon-navy"><svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg></div>
      <div class="dash-card-body"><h3>View Website</h3><p>Open the live site in a new tab</p></div>
      <div class="dash-card-arrow"><svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg></div>
    </a>
  </div>
  <div class="dash-info mt-4">
    <div class="card"><div class="card-body">
      <h5 class="text-muted mb-1">Logged in as</h5>
      <strong><?php echo e($_SESSION['pal_admin_user']); ?></strong>
    </div></div>
    <div class="card"><div class="card-body">
      <h5 class="text-muted mb-1">Company</h5>
      <strong><?php echo e($site['company_name']); ?></strong>
    </div></div>
  </div>
</div>
</div>
<div id="toast-container"></div>
<script src="assets/admin.js"></script>
</body>
</html>
