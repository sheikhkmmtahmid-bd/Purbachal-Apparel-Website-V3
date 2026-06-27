<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';
sendSecurityHeaders();
requireLogin();
$pageTitle = 'Navigation Manager';
$nav = jsonRead(DATA_DIR . 'nav.json');
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
  <div class="card mb-4">
    <div class="card-header">Nav Pages (drag to reorder)</div>
    <div class="card-body">
      <ul class="sortable-list" id="navList" data-endpoint="api/save-nav.php">
        <?php foreach ($nav['pages'] as $i => $page): ?>
        <li class="sortable-item" data-index="<?php echo $i; ?>">
          <span class="drag-handle"><svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg></span>
          <div class="sortable-item-body">
            <div class="row">
              <div class="col"><input type="text" class="form-control form-control-sm" placeholder="Label" value="<?php echo e($page['label']); ?>" data-field="label"></div>
              <div class="col"><input type="text" class="form-control form-control-sm" placeholder="File (e.g. about.php)" value="<?php echo e($page['file']); ?>" data-field="file"></div>
            </div>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
      <div class="mt-3">
        <div class="card mb-2">
          <div class="card-header">CTA Button</div>
          <div class="card-body row">
            <div class="col"><label class="form-label">Label</label><input type="text" id="ctaLabel" class="form-control" value="<?php echo e($nav['cta']['label'] ?? ''); ?>"></div>
            <div class="col"><label class="form-label">URL</label><input type="text" id="ctaUrl" class="form-control" value="<?php echo e($nav['cta']['url'] ?? ''); ?>"></div>
          </div>
        </div>
      </div>
      <button class="btn btn-primary" id="saveNav">Save Navigation</button>
    </div>
  </div>
</div>
</div>
<div id="toast-container"></div>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="assets/admin.js"></script>
<script>
(function(){
  var el = document.getElementById('navList');
  if (el) Sortable.create(el, { handle: '.drag-handle', animation: 150 });
  document.getElementById('saveNav').addEventListener('click', async function(){
    var items = Array.from(el.querySelectorAll('.sortable-item')).map(function(li){
      return { label: li.querySelector('[data-field="label"]').value, file: li.querySelector('[data-field="file"]').value };
    });
    var cta = { label: document.getElementById('ctaLabel').value, url: document.getElementById('ctaUrl').value };
    var res = await palPost('api/save-nav.php', { pages: items, cta: cta });
    showToast(res.message || 'Saved', res.ok ? 'success' : 'error');
  });
})();
</script>
</body>
</html>
