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
$creds = jsonRead(DATA_DIR . '.credentials.json');
$sent  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $emailIn    = strtolower(trim($_POST['email'] ?? ''));
    $adminEmail = strtolower(trim($creds['email'] ?? ''));

    if ($adminEmail && $emailIn === $adminEmail) {
        $token     = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        jsonWrite(DATA_DIR . '.reset_token.json', [
            'hash'    => $tokenHash,
            'expires' => time() + 3600,
            'used'    => false,
        ]);

        $proto     = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host      = $_SERVER['HTTP_HOST'] ?? '';
        $dir       = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        $resetUrl  = $proto . '://' . $host . $dir . '/reset-password.php?token=' . urlencode($token);

        $body = palMailTemplate('Password Reset Request', '
            <p>A password reset was requested for the Purbachal Apparel CMS admin account.</p>
            <p>Click the button below to set a new password. This link expires in <strong>1 hour</strong>.</p>
            <p style="margin:24px 0;"><a href="' . htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8') . '" class="btn">Reset Password</a></p>
            <p style="font-size:.82rem;color:#64748b;word-break:break-all;">If the button does not work, copy this link into your browser:<br>' . htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8') . '</p>
            <p>If you did not request this, you can safely ignore this email.</p>
        ');
        palMail($creds['email'], 'Password Reset Request - PAL CMS', $body);
    }

    $sent = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Forgot Password | <?php echo e($site['company_name'] ?? 'PAL CMS'); ?></title>
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

    <?php if ($sent): ?>
    <div class="alert alert-success">
      If that email matches our records, a password reset link has been sent. Please check your inbox (and spam folder).
    </div>
    <div class="text-center mt-3">
      <a href="index.php" class="btn btn-outline w-100">Back to Login</a>
    </div>

    <?php else: ?>
    <p class="small text-muted mb-3">Enter the admin email address you configured in <strong>My Account</strong>. A reset link will be sent to that address.</p>
    <form method="post" novalidate>
      <?php echo csrfField(); ?>
      <div class="form-group mb-3">
        <label class="form-label" for="email">Admin Email Address</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="admin@example.com" required autocomplete="email">
      </div>
      <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
    </form>
    <div class="text-center mt-3">
      <a href="index.php" class="small" style="color:var(--text-muted);text-decoration:none;">Back to Login</a>
    </div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
