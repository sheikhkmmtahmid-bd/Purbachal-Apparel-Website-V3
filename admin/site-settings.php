<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';
sendSecurityHeaders();
requireLogin();
$pageTitle = 'Site Settings';
$site = jsonRead(DATA_DIR . 'site.json');
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $fields = ['company_name','company_name_short','tagline','address','email','phone1','phone1_tel','phone2','phone2_tel','bgmea','erc','map_embed','footer_desc'];
    $soc = ['facebook','linkedin','youtube','whatsapp'];
    foreach ($fields as $f) $site[$f] = sanitize($_POST[$f] ?? '');
    foreach ($soc as $s) $site['social'][$s] = sanitize($_POST['social_'.$s] ?? '');
    // Handle logo upload
    if (!empty($_FILES['logo']['name'])) {
        $res = validateAndProcessUpload($_FILES['logo'], UPLOAD_DIR . 'logo/');
        if ($res['ok']) {
            if (!empty($site['logo'])) @unlink(UPLOAD_DIR . 'logo/' . $site['logo']);
            $site['logo'] = $res['filename'];
        } else { $error = $res['error']; }
    }
    // Handle favicon upload
    if (!empty($_FILES['favicon']['name'])) {
        $res = validateAndProcessUpload($_FILES['favicon'], UPLOAD_DIR . 'favicon/');
        if ($res['ok']) {
            if (!empty($site['favicon'])) @unlink(UPLOAD_DIR . 'favicon/' . $site['favicon']);
            $site['favicon'] = $res['filename'];
        } else { $error = $res['error']; }
    }
    if (!$error) {
        jsonWrite(DATA_DIR . 'site.json', $site);
        $success = true;
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
  <?php if ($success): ?><div class="alert alert-success">Settings saved successfully.</div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><?php echo e($error); ?></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data" id="settingsForm">
    <?php echo csrfField(); ?>
    <div class="row">
      <div class="col-md-8">
        <div class="card mb-4">
          <div class="card-header">Company Info</div>
          <div class="card-body">
            <?php $fields = [
              ['name'=>'company_name','label'=>'Company Name'],
              ['name'=>'company_name_short','label'=>'Short Name'],
              ['name'=>'tagline','label'=>'Tagline'],
              ['name'=>'address','label'=>'Address'],
              ['name'=>'email','label'=>'Email'],
              ['name'=>'phone1','label'=>'Phone 1 (Display)'],
              ['name'=>'phone1_tel','label'=>'Phone 1 (tel: link)'],
              ['name'=>'phone2','label'=>'Phone 2 (Display)'],
              ['name'=>'phone2_tel','label'=>'Phone 2 (tel: link)'],
              ['name'=>'bgmea','label'=>'BGMEA Number'],
              ['name'=>'erc','label'=>'ERC Number'],
            ]; foreach ($fields as $f): ?>
            <div class="form-group mb-3">
              <label><?php echo e($f['label']); ?></label>
              <input type="text" name="<?php echo e($f['name']); ?>" class="form-control" value="<?php echo e($site[$f['name']] ?? ''); ?>">
            </div>
            <?php endforeach; ?>
            <div class="form-group mb-3">
              <label>Footer Description</label>
              <textarea name="footer_desc" class="form-control" rows="3"><?php echo e($site['footer_desc'] ?? ''); ?></textarea>
            </div>
            <div class="form-group mb-3">
              <label>Google Maps Embed URL</label>
              <input type="url" name="map_embed" class="form-control" value="<?php echo e($site['map_embed'] ?? ''); ?>">
            </div>
          </div>
        </div>
        <div class="card mb-4">
          <div class="card-header">Social Links</div>
          <div class="card-body">
            <?php $soc = ['facebook'=>'Facebook','linkedin'=>'LinkedIn','youtube'=>'YouTube','whatsapp'=>'WhatsApp (wa.me link)'];
            foreach ($soc as $k => $label): ?>
            <div class="form-group mb-3">
              <label><?php echo e($label); ?></label>
              <input type="url" name="social_<?php echo e($k); ?>" class="form-control" value="<?php echo e($site['social'][$k] ?? ''); ?>">
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card mb-4">
          <div class="card-header">Logo</div>
          <div class="card-body text-center">
            <?php if (!empty($site['logo'])): ?>
            <img src="../uploads/logo/<?php echo e($site['logo']); ?>" class="img-preview mb-2" alt="logo">
            <?php else: ?><p class="text-muted small">No logo uploaded. Default will be used.</p><?php endif; ?>
            <input type="file" name="logo" class="form-control" accept="image/*">
            <p class="text-muted small mt-1">JPG/PNG/WEBP, max 10MB</p>
          </div>
        </div>
        <div class="card mb-4">
          <div class="card-header">Favicon</div>
          <div class="card-body text-center">
            <?php if (!empty($site['favicon'])): ?>
            <img src="../uploads/favicon/<?php echo e($site['favicon']); ?>" width="64" height="64" class="mb-2" alt="favicon">
            <?php else: ?><p class="text-muted small">No favicon uploaded.</p><?php endif; ?>
            <input type="file" name="favicon" class="form-control" accept="image/*">
            <p class="text-muted small mt-1">PNG recommended, max 10MB</p>
          </div>
        </div>
      </div>
    </div>
    <button type="submit" class="btn btn-primary">Save Settings</button>
  </form>
</div>
</div>
<div id="toast-container"></div>
<script src="assets/admin.js"></script>
</body>
</html>
