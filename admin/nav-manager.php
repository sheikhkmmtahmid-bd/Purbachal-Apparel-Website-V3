<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';
sendSecurityHeaders();
requireLogin();
$pageTitle = 'Navigation Manager';
$nav = jsonRead(DATA_DIR . 'nav.json');

// Build a flat map of file => friendly label from all known pages
$knownPages = [];
foreach ($nav['pages'] as $p) {
    $knownPages[$p['file']] = $p['label'];
    if (!empty($p['dropdown'])) {
        foreach ($p['dropdown'] as $sub) {
            $knownPages[$sub['file']] = $sub['label'];
        }
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
<style>
.nav-file-custom { margin-top: 4px; }
</style>
</head>
<body class="admin-body">
<?php require_once __DIR__ . '/partials/sidebar.php'; ?>
<div class="admin-main">
<?php require_once __DIR__ . '/partials/topbar.php'; ?>
<div class="admin-content">
  <div class="card mb-4">
    <div class="card-header">Nav Pages (drag to reorder)</div>
    <div class="card-body">
      <p class="text-muted small mb-3">Drag rows to reorder. Edit <strong>Menu Label</strong> to rename the link. Use <strong>Links To</strong> to choose which page it opens.</p>
      <ul class="sortable-list" id="navList">
        <?php foreach ($nav['pages'] as $i => $page):
          $fileVal = $page['file'];
          $isKnown = isset($knownPages[$fileVal]);
        ?>
        <li class="sortable-item" data-index="<?php echo $i; ?>">
          <span class="drag-handle"><svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg></span>
          <div class="sortable-item-body">
            <div class="row align-items-start">
              <div class="col">
                <label class="form-label-xs">Menu Label</label>
                <input type="text" class="form-control form-control-sm" placeholder="Label" value="<?php echo e($page['label']); ?>" data-field="label">
              </div>
              <div class="col">
                <label class="form-label-xs">Links To</label>
                <input type="hidden" data-field="file" value="<?php echo e($fileVal); ?>">
                <select class="form-control form-control-sm nav-file-select">
                  <?php foreach ($knownPages as $f => $lbl): ?>
                  <option value="<?php echo e($f); ?>"<?php echo $fileVal === $f ? ' selected' : ''; ?>><?php echo e($lbl); ?></option>
                  <?php endforeach; ?>
                  <option value="__custom__"<?php echo !$isKnown ? ' selected' : ''; ?>>Custom URL...</option>
                </select>
                <input type="text" class="form-control form-control-sm nav-file-custom" placeholder="Enter a URL" value="<?php echo !$isKnown ? e($fileVal) : ''; ?>" style="<?php echo $isKnown ? 'display:none' : ''; ?>">
              </div>
            </div>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
      <div class="mt-3">
        <div class="card mb-2">
          <div class="card-header">CTA Button</div>
          <div class="card-body">
            <div class="row align-items-start">
              <div class="col">
                <label class="form-label">Button Label</label>
                <input type="text" id="ctaLabel" class="form-control" value="<?php echo e($nav['cta']['label'] ?? ''); ?>">
              </div>
              <div class="col">
                <label class="form-label">Links To</label>
                <?php
                  $ctaFile = $nav['cta']['file'] ?? '';
                  $ctaKnown = isset($knownPages[$ctaFile]);
                ?>
                <input type="hidden" id="ctaUrl" value="<?php echo e($ctaFile); ?>">
                <select class="form-control" id="ctaSelect">
                  <?php foreach ($knownPages as $f => $lbl): ?>
                  <option value="<?php echo e($f); ?>"<?php echo $ctaFile === $f ? ' selected' : ''; ?>><?php echo e($lbl); ?></option>
                  <?php endforeach; ?>
                  <option value="__custom__"<?php echo !$ctaKnown ? ' selected' : ''; ?>>Custom URL...</option>
                </select>
                <input type="text" id="ctaCustom" class="form-control mt-1" placeholder="Enter a URL" value="<?php echo !$ctaKnown ? e($ctaFile) : ''; ?>" style="<?php echo $ctaKnown ? 'display:none' : ''; ?>">
              </div>
            </div>
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
  /* Drag to reorder */
  var el = document.getElementById('navList');
  if (el) Sortable.create(el, { handle: '.drag-handle', animation: 150 });

  /* Wire up each row's file select <-> hidden input <-> custom text */
  function wireFileSelect(select, hiddenInput, customInput) {
    select.addEventListener('change', function() {
      if (this.value === '__custom__') {
        customInput.style.display = '';
        customInput.focus();
        hiddenInput.value = customInput.value;
      } else {
        customInput.style.display = 'none';
        hiddenInput.value = this.value;
      }
    });
    customInput.addEventListener('input', function() {
      hiddenInput.value = this.value;
    });
  }

  document.querySelectorAll('.sortable-item').forEach(function(li) {
    var select      = li.querySelector('.nav-file-select');
    var hidden      = li.querySelector('[data-field="file"]');
    var customInput = li.querySelector('.nav-file-custom');
    if (select && hidden && customInput) wireFileSelect(select, hidden, customInput);
  });

  /* CTA button */
  wireFileSelect(
    document.getElementById('ctaSelect'),
    document.getElementById('ctaUrl'),
    document.getElementById('ctaCustom')
  );

  /* Save */
  document.getElementById('saveNav').addEventListener('click', async function(){
    var items = Array.from(el.querySelectorAll('.sortable-item')).map(function(li){
      return {
        label: li.querySelector('[data-field="label"]').value,
        file:  li.querySelector('[data-field="file"]').value
      };
    });
    var cta = {
      label: document.getElementById('ctaLabel').value,
      url:   document.getElementById('ctaUrl').value
    };
    var res = await palPost('api/save-nav.php', { pages: items, cta: cta });
    showToast(res.message || 'Saved', res.ok ? 'success' : 'error');
  });
})();
</script>
</body>
</html>
