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
$site = jsonRead(DATA_DIR . 'site.json');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Login | <?php echo e($site['company_name']); ?></title>
<link rel="stylesheet" href="assets/admin.css">
<?php echo csrfMeta(); ?>
</head>
<body class="login-page">
<div class="login-wrap">
  <div class="login-card">
    <div class="login-logo">
      <img src="../<?php echo e(!empty($site['logo']) ? 'uploads/logo/'.$site['logo'] : ''); ?>" alt="logo" onerror="this.style.display='none'">
      <h1>Admin Panel</h1>
      <p><?php echo e($site['company_name']); ?></p>
    </div>
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
            <svg id="eyeIcon" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
          </button>
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100 mt-4" <?php echo $locked ? 'disabled' : ''; ?>>Sign In</button>
    </form>
  </div>
</div>
<script>
document.querySelector('.toggle-pw').addEventListener('click', function(){
  const pw = document.getElementById('password');
  pw.type = pw.type === 'password' ? 'text' : 'password';
});
</script>
</body>
</html>
