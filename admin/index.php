<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';
sendSecurityHeaders();
startSecureSession();
generateCsrfToken();

$error   = '';
$locked  = false;
$timeout = isset($_GET['timeout']);
$notice  = ($_GET['notice'] ?? '') === 'password_changed';

if (!empty($_SESSION['pal_admin_logged'])) {
    header('Location: dashboard.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    if (isRateLimited()) {
        $locked = true;
        $secs   = getRateLimitSeconds();
        $error  = 'Too many failed attempts. Try again in ' . ceil($secs/60) . ' minute(s).';
    } else {
        $username = sanitize($_POST['username'] ?? '', 64);
        $password = $_POST['password'] ?? '';
        $creds    = jsonRead(DATA_DIR . '.credentials.json');
        $ok = !empty($creds['username'])
             && !empty($creds['password'])
             && $creds['password'] !== 'SETUP_REQUIRED'
             && $username === $creds['username']
             && password_verify($password, $creds['password']);
        if ($ok) {
            clearLoginAttempts();
            session_regenerate_id(true);
            $_SESSION['pal_admin_logged'] = true;
            $_SESSION['pal_admin_user']   = $username;
            $_SESSION['pal_last_active']  = time();
            header('Location: dashboard.php'); exit;
        } else {
            recordFailedLogin();
            $error = 'Login failed. Please check your credentials.';
        }
    }
}
$site        = jsonRead(DATA_DIR . 'site.json');
$favFile     = $site['favicon'] ?? 'favicon.png';
$adminFavSrc = file_exists(dirname(__DIR__) . '/uploads/favicon/' . $favFile)
    ? '../uploads/favicon/' . $favFile
    : '../' . $favFile;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Login | <?php echo e($site['company_name']); ?></title>
<link rel="stylesheet" href="assets/admin.css">
<link rel="icon" type="image/png" sizes="32x32" href="../<?php echo e($site['favicon32'] ?? 'favicon-32.png'); ?>">
<link rel="icon" type="image/png" href="../<?php echo e($site['favicon'] ?? 'favicon.png'); ?>">
<?php echo csrfMeta(); ?>
</head>
<body class="login-page">
<div class="login-wrap">
  <div class="login-card">
    <div class="login-logo">
      <div class="login-logo-brand">
        <img src="<?php echo e($adminFavSrc); ?>" alt="">
        <div class="login-logo-brand-text">
          <strong>PURBACHAL</strong>
          <span>APPAREL LIMITED</span>
        </div>
      </div>
      <p>Admin Panel</p>
    </div>
    <?php if ($notice): ?>
    <div class="alert alert-success">Password changed. Please log in with your new password.</div>
    <?php endif; ?>
    <?php if ($timeout): ?>
    <div class="alert alert-warning">Session expired due to inactivity.</div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="alert alert-danger"><?php echo e($error); ?></div>
    <?php endif; ?>
    <form method="post" id="loginForm" novalidate>
      <?php echo csrfField(); ?>
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" class="form-control" autocomplete="username" required>
      </div>
      <div class="form-group mt-3">
        <label for="password">Password</label>
        <div class="input-icon-right">
          <input type="password" id="password" name="password" class="form-control" autocomplete="current-password" required>
          <button type="button" class="toggle-pw" aria-label="Toggle password">
            <svg class="eye-open" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            <svg class="eye-closed" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
          </button>
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100 mt-4" <?php echo $locked ? 'disabled' : ''; ?>>Sign In</button>
      <div class="text-center mt-3">
        <a href="forgot-password.php" class="small" style="color:var(--text-muted);text-decoration:none;">Forgot password?</a>
      </div>
    </form>
  </div>
</div>
<script>
document.querySelector('.toggle-pw').addEventListener('click', function(){
  const pw = document.getElementById('password');
  const show = pw.type === 'password';
  pw.type = show ? 'text' : 'password';
  this.classList.toggle('showing', show);
});
</script>
</body>
</html>
