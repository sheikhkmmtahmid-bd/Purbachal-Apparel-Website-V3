<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';
sendSecurityHeaders();
requireLogin();
$pageTitle = 'Gallery Manager';
$teamData  = jsonRead(DATA_DIR . 'pages/team.json');
$gallery   = $teamData['gallery_items'] ?? [];
$error     = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    if (!empty($_FILES['images']['name'][0])) {
        $count = count($_FILES['images']['name']);
        for ($i = 0; $i < $count; $i++) {
            $file = [
                'name'     => $_FILES['images']['name'][$i],
                'type'     => $_FILES['images']['type'][$i],
                'tmp_name' => $_FILES['images']['tmp_name'][$i],
                'error'    => $_FILES['images']['error'][$i],
                'size'     => $_FILES['images']['size'][$i],
            ];
            $res = validateAndProcessUpload($file, UPLOAD_DIR . 'gallery/');
            if ($res['ok']) {
                $gallery[] = ['filename' => $res['filename'], 'alt' => ''];
            } else {
                $error .= $res['error'] . ' ';
            }
        }
        $teamData['gallery_items'] = $gallery;
        jsonWrite(DATA_DIR . 'pages/team.json', $teamData);
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
.gallery-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:1rem;margin-bottom:1rem}
.gallery-item{position:relative;border:1px solid var(--border);border-radius:8px;overflow:hidden;background:#f8f9fa}
.gallery-item img{width:100%;height:140px;object-fit:cover;display:block}
.gallery-item-meta{padding:.5rem}
.gallery-item-meta input{width:100%;font-size:.8rem;padding:.25rem .4rem;border:1px solid var(--border);border-radius:4px;background:var(--bg)}
.gallery-drag-handle{position:absolute;top:.4rem;left:.4rem;cursor:grab;background:rgba(0,0,0,.45);color:#fff;border-radius:4px;padding:.15rem .3rem;font-size:.75rem;line-height:1}
.gallery-delete{position:absolute;top:.4rem;right:.4rem;background:rgba(220,38,38,.85);color:#fff;border:none;border-radius:4px;width:26px;height:26px;cursor:pointer;display:flex;align-items:center;justify-content:center}
.gallery-delete:hover{background:rgb(185,28,28)}
</style>
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
      <div class="gallery-grid" id="galleryGrid">
        <?php foreach ($gallery as $item):
            $fn  = is_array($item) ? ($item['filename'] ?? '') : $item;
            $alt = is_array($item) ? ($item['alt'] ?? '') : '';
        ?>
        <div class="gallery-item" data-file="<?php echo e($fn); ?>">
          <span class="gallery-drag-handle" title="Drag to reorder">&#9776;</span>
          <img src="../uploads/gallery/<?php echo e($fn); ?>" alt="<?php echo e($alt); ?>" loading="lazy"
               onerror="this.style.opacity='.3'">
          <div class="gallery-item-meta">
            <input type="text" class="gallery-alt" placeholder="Alt text / caption" value="<?php echo e($alt); ?>">
          </div>
          <button class="gallery-delete" data-file="<?php echo e($fn); ?>" title="Delete image">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <?php endforeach; ?>
      </div>
      <?php if (empty($gallery)): ?>
      <p class="text-muted">No images yet. Upload images above to populate the gallery.</p>
      <?php endif; ?>
      <button class="btn btn-primary" id="saveGalleryOrder">Save Order &amp; Alt Text</button>
    </div>
  </div>
</div>
</div>
<div id="toast-container"></div>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="assets/admin.js"></script>
<script>
(function () {
  var grid = document.getElementById('galleryGrid');
  if (grid) Sortable.create(grid, { animation: 150, handle: '.gallery-drag-handle' });

  document.getElementById('saveGalleryOrder').addEventListener('click', async function () {
    var items = Array.from(grid.querySelectorAll('.gallery-item')).map(function (el) {
      return {
        filename: el.dataset.file,
        alt: (el.querySelector('.gallery-alt') || {}).value || ''
      };
    });
    var res = await palPost('api/save-gallery.php', { items: items });
    showToast(res.message || (res.ok ? 'Saved.' : 'Error saving.'), res.ok ? 'success' : 'error');
  });

  grid.addEventListener('click', async function (e) {
    var btn = e.target.closest('.gallery-delete');
    if (!btn) return;
    if (!confirm('Delete this image permanently?')) return;
    var res = await palPost('api/save-gallery.php', { delete: btn.dataset.file });
    if (res.ok) {
      btn.closest('.gallery-item').remove();
      showToast('Image deleted.', 'success');
    } else {
      showToast(res.message || 'Delete failed.', 'error');
    }
  });
})();
</script>
</body>
</html>
