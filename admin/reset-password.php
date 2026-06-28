<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/mailer.php';
require_once __DIR__ . '/auth.php';
sendSecurityHeaders();
startSecureSession();
generateCsrfToken();

if (!empty($_SESSION['pal_admin_logged'])) {
    header('Location: dashboard.php'); exit;
}

$site        = jsonRead(DATA_DIR . 'site.json');
$favFile     = $site['favicon'] ?? 'favicon.png';
$adminFavSrc = file_exists(dirname(__DIR__) . '/uploads/favicon/' . $favFile)
    ? '../uploads/favicon/' . $favFile
    : '../' . $favFile;
$error   = '';
$success = false;

$tokenRaw   = trim($_GET['token'] ?? '');
$stored     = jsonRead(DATA_DIR . '.reset_token.json');
$tokenValid = false;

if (
    $tokenRaw !== ''
    && !empty($stored['hash'])
    && empty($stored['used'])
    && !empty($stored['expires'])
    && $stored['expires'] > time()
    && hash_equals($stored['hash'], hash('sha256', $tokenRaw))
) {
    $tokenValid = true;
}

if (!$tokenValid && !$success) {
    $error = 'This password reset link is invalid or has expired. Please request a new one.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tokenValid) {
    requireCsrf();
    $newPass  = $_POST['new_password'] ?? '';
    $confPass = $_POST['confirm_password'] ?? '';

    if (strlen($newPass) < 10) {
        $error = 'Password must be at least 10 characters.';
    } elseif ($newPass !== $confPass) {
        $error = 'Passwords do not match.';
    } else {
        $creds             = jsonRead(DATA_DIR . '.credentials.json');
        $creds['password'] = password_hash($newPass, PASSWORD_BCRYPT, ['cost' => 12]);
        jsonWrite(DATA_DIR . '.credentials.json', $creds);

        $stored['used'] = true;
        jsonWrite(DATA_DIR . '.reset_token.json', $stored);

        if (!empty($creds['email'])) {
            $body = palMailTemplate('Password Reset Successful', '
                <p>Your PAL CMS admin password has been reset successfully on <strong>' . date('d M Y \a\t H:i') . ' (server time)</strong>.</p>
                <p>You can now <a href="index.php" style="color:#0E7E87;">log in</a> with your new password.</p>
                <p>If you did not perform this action, contact your web developer immediately.</p>
            ');
            palMail($creds['email'], 'Password Reset Successful - PAL CMS', $body);
        }

        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Reset Password | <?php echo e($site['company_name'] ?? 'PAL CMS'); ?></title>
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

    <?php if ($success): ?>
    <div class="alert alert-success">
      Password reset successfully. You can now log in with your new password.
    </div>
    <a href="index.php" class="btn btn-primary w-100 mt-3">Go to Login</a>

    <?php elseif ($error && !$tokenValid): ?>
    <div class="alert alert-danger"><?php echo e($error); ?></div>
    <div class="text-center mt-3">
      <a href="forgot-password.php" class="btn btn-outline w-100">Request New Reset Link</a>
    </div>

    <?php else: ?>
    <?php if ($error): ?>
    <div class="alert alert-danger"><?php echo e($error); ?></div>
    <?php endif; ?>
    <p class="small text-muted mb-3">Choose a strong password of at least 10 characters.</p>
    <form method="post" action="reset-password.php?token=<?php echo urlencode($tokenRaw); ?>" novalidate>
      <?php echo csrfField(); ?>
      <div class="form-group mb-3">
        <label class="form-label" for="new_password">New Password</label>
        <div class="input-icon-right">
          <input type="password" id="new_password" name="new_password" class="form-control" autocomplete="new-password" minlength="10" required>
          <button type="button" class="toggle-pw" aria-label="Show password">
            <svg class="eye-open" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            <svg class="eye-closed" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
          </button>
        </div>
      </div>
      <div class="form-group mb-3">
        <label class="form-label" for="confirm_password">Confirm New Password</label>
        <div class="input-icon-right">
          <input type="password" id="confirm_password" name="confirm_password" class="form-control" autocomplete="new-password" minlength="10" required>
          <button type="button" class="toggle-pw" aria-label="Show password">
            <svg class="eye-open" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            <svg class="eye-closed" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
          </button>
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100">Set New Password</button>
    </form>
    <?php endif; ?>
  </div>
</div>
<script>
document.querySelectorAll('.toggle-pw').forEach(function(btn) {
  btn.addEventListener('click', function() {
    var pw = this.previousElementSibling;
    if (!pw) return;
    var show = pw.type === 'password';
    pw.type = show ? 'text' : 'password';
    this.classList.toggle('showing', show);
  });
});
</script>
</body>
</html>
