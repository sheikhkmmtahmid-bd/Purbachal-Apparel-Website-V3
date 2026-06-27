<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';
sendSecurityHeaders();
requireLogin();
$pageTitle = 'Products Manager';
$prods = jsonRead(DATA_DIR . 'pages/products.json');
$cats = ['kids' => 'Kid\'s Wear', 'mens' => 'Men\'s Wear', 'womens' => 'Women\'s Wear'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $cat = preg_replace('/[^a-z]/', '', strtolower($_POST['category'] ?? ''));
    if (!isset($cats[$cat])) { $error = 'Invalid category.'; }
    elseif (!empty($_FILES['images']['name'][0])) {
        $count = count($_FILES['images']['name']);
        for ($i = 0; $i < $count; $i++) {
            $file = [
                'name' => $_FILES['images']['name'][$i],
                'type' => $_FILES['images']['type'][$i],
                'tmp_name' => $_FILES['images']['tmp_name'][$i],
                'error' => $_FILES['images']['error'][$i],
                'size' => $_FILES['images']['size'][$i],
            ];
            $res = validateAndProcessUpload($file, UPLOAD_DIR . 'products/' . $cat . '/');
            if ($res['ok']) { $prods[$cat][] = $res['filename']; }
            else { $error .= $res['error'] . ' '; }
        }
        jsonWrite(DATA_DIR . 'pages/products.json', $prods);
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
  <?php if ($error): ?><div class="alert alert-warning"><?php echo e($error); ?></div><?php endif; ?>
  <div class="card mb-4">
    <div class="card-header">Upload Product Images</div>
    <div class="card-body">
      <form method="post" enctype="multipart/form-data">
        <?php echo csrfField(); ?>
        <div class="row mb-3">
          <div class="col-md-4">
            <label>Category</label>
            <select name="category" class="form-control">
              <?php foreach ($cats as $k => $l): ?>
              <option value="<?php echo e($k); ?>"><?php echo e($l); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-8">
            <label>Images</label>
            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
          </div>
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
      </form>
    </div>
  </div>
  <?php foreach ($cats as $cat => $catLabel): $imgs = $prods[$cat] ?? []; ?>
  <div class="card mb-4">
    <div class="card-header"><?php echo e($catLabel); ?> (<?php echo count($imgs); ?> images) <span class="text-muted small">drag to reorder</span></div>
    <div class="card-body">
      <div class="gallery-grid sortable-gallery" id="grid-<?php echo e($cat); ?>" data-cat="<?php echo e($cat); ?>">
        <?php foreach ($imgs as $img): ?>
        <div class="gallery-item" data-file="<?php echo e($img); ?>">
          <img src="../uploads/products/<?php echo e($cat); ?>/<?php echo e($img); ?>" alt="" loading="lazy">
          <button class="gallery-delete" data-cat="<?php echo e($cat); ?>" data-file="<?php echo e($img); ?>" title="Delete">&times;</button>
        </div>
        <?php endforeach; ?>
      </div>
      <button class="btn btn-sm btn-primary mt-2 save-prod-order" data-cat="<?php echo e($cat); ?>">Save Order</button>
    </div>
  </div>
  <?php endforeach; ?>
</div>
</div>
<div id="toast-container"></div>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="assets/admin.js"></script>
<script>
document.querySelectorAll('.sortable-gallery').forEach(function(g){
  Sortable.create(g, { animation: 150 });
});
document.querySelectorAll('.save-prod-order').forEach(function(btn){
  btn.addEventListener('click', async function(){
    var cat = this.dataset.cat;
    var imgs = Array.from(document.getElementById('grid-' + cat).querySelectorAll('.gallery-item')).map(function(el){ return el.dataset.file; });
    var res = await palPost('api/save-products.php', { category: cat, images: imgs });
    showToast(res.message || 'Saved', res.ok ? 'success' : 'error');
  });
});
document.querySelectorAll('.gallery-delete').forEach(function(btn){
  btn.addEventListener('click', async function(){
    if (!confirm('Delete this product image?')) return;
    var res = await palPost('api/save-products.php', { delete: this.dataset.file, category: this.dataset.cat });
    if (res.ok) { this.closest('.gallery-item').remove(); showToast('Deleted', 'success'); }
    else showToast(res.message, 'error');
  });
});
</script>
</body>
</html>
