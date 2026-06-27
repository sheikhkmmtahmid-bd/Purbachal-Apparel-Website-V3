<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';
sendSecurityHeaders();
requireLogin();
$pageTitle = 'Gallery Manager';
$teamData = jsonRead(DATA_DIR . 'pages/team.json');
$gallery = $teamData['gallery'] ?? [];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    if (!empty($_FILES['images']['name'][0])) {
        $uploaded = [];
        $count = count($_FILES['images']['name']);
        for ($i = 0; $i < $count; $i++) {
            $file = [
                'name' => $_FILES['images']['name'][$i],
                'type' =>=> $_FILES['images']['type'][$i],
                'tmp_name' => $_FILES['images']['tmp_name'][$i],
                'error' => $_FILES['images']['error'][$i],
                'size' => $_FILES['images']['size'][$i],
            ];
            $res = validateAndProcessUpload($file, UPLOAD_DIR . 'gallery/');
            if ($res['ok']) { $gallery[] = $res['filename']; }
            else { $error .= $res['error'] . ' '; }
        }
        $teamData['gallery'] = $gallery;
        jsonWrite(DATA_DIR . 'pages/team.json', $teamData);
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
  <?php if ($error): ?><div class="alert alert-warning"><?php echo e($error); ?></div><?php endif; ?>
  <div class="card mb-4">
    <div class="card-header">Upload Images</div>
    <div class="card-body">
      <form method="post" enctype="multipart/form-data">
        <?php echo csrfField(); ?>
        <input type="file" name="images[]" class="form-control" multiple accept="image/*">
        <button type="submit" class="btn btn-primary mt-3">Upload</button>
      </form>
    </div>
  </div>
  <div class="card">
    <div class="card-header">Gallery Images (drag to reorder)</div>
    <div class="card-body">
      <div class="gallery-grid sortable-gallery" id="galleryGrid">
        <?php foreach ($gallery as $img): ?>
        <div class="gallery-item" data-file="<?php echo e($img); ?>">
          <img src="../uploads/gallery/<?php echo e($img); ?>" alt="">
          <button class="gallery-delete" data-file="<?php echo e($img); ?>" title="Delete">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <?php endforeach; ?>
      </div>
      <button class="btn btn-primary mt-3" id="saveGalleryOrder">Save Order</button>
    </div>
  </div>
</div>
</div>
<div id="toast-container"></div>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="assets/admin.js"></script>
<script>
(function(){
  var grid = document.getElementById('galleryGrid');
  if (grid) Sortable.create(grid, { animation: 150 });
  document.getElementById('saveGalleryOrder').addEventListener('click', async function(){
    var imgs = Array.from(grid.querySelectorAll('.gallery-item')).map(function(el){ return el.dataset.file; });
    var res = await palPost('api/save-gallery.php', { images: imgs });
    showToast(res.message || 'Saved', res.ok ? 'success' : 'error');
  });
  document.querySelectorAll('.gallery-delete').forEach(function(btn){
    btn.addEventListener('click', async function(){
      if (!confirm('Delete this image?')) return;
      var res = await palPost('api/save-gallery.php', { delete: this.dataset.file });
      if (res.ok) { this.closest('.gallery-item').remove(); showToast('Deleted', 'success'); }
      else showToast(res.message, 'error');
    });
  });
})();
</script>
</body>
</html>
