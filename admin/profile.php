<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/mailer.php';
require_once __DIR__ . '/auth.php';
sendSecurityHeaders();
requireLogin();
generateCsrfToken();

function eyeIconSvg(): string {
    return '<svg class="eye-open" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>'
          . '<svg class="eye-closed" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>';
}

$pageTitle = 'My Account';
$creds   = jsonRead(DATA_DIR . '.credentials.json');
$success = '';
$error   = '';
$section = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'change_email') {
        $section  = 'email';
        $newEmail = sanitize($_POST['new_email'] ?? '', 200);
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } else {
            $creds['email'] = $newEmail;
            jsonWrite(DATA_DIR . '.credentials.json', $creds);
            $success = 'Admin email updated.';
        }

    } elseif ($action === 'change_username') {
        $section  = 'username';
        $newUser  = sanitize($_POST['new_username'] ?? '', 64);
        $curPass  = $_POST['current_password_u'] ?? '';
        if (strlen($newUser) < 3) {
            $error = 'Username must be at least 3 characters.';
        } elseif (!preg_match('/^[a-zA-Z0-9._@\-]+$/', $newUser)) {
            $error = 'Username may only contain letters, numbers, dots, underscores, hyphens, and @.';
        } elseif (!password_verify($curPass, $creds['password'])) {
            $error = 'Current password is incorrect.';
        } else {
            $creds['username'] = $newUser;
            jsonWrite(DATA_DIR . '.credentials.json', $creds);
            $_SESSION['pal_admin_user'] = $newUser;
            $success = 'Username updated to <strong>' . e($newUser) . '</strong>.';
        }

    } elseif ($action === 'change_password') {
        $section  = 'password';
        $curPass  = $_POST['current_password_p'] ?? '';
        $newPass  = $_POST['new_password'] ?? '';
        $confPass = $_POST['confirm_password'] ?? '';
        if (!password_verify($curPass, $creds['password'])) {
            $error = 'Current password is incorrect.';
        } elseif (strlen($newPass) < 10) {
            $error = 'New password must be at least 10 characters.';
        } elseif ($newPass !== $confPass) {
            $error = 'New passwords do not match.';
        } else {
            $creds['password'] = password_hash($newPass, PASSWORD_BCRYPT, ['cost' => 12]);
            jsonWrite(DATA_DIR . '.credentials.json', $creds);
            if (!empty($creds['email'])) {
                $body = palMailTemplate('Admin Password Changed', '
                    <p>Your PAL CMS admin password was changed on <strong>' . date('d M Y \a\t H:i') . ' (server time)</strong>.</p>
                    <p>If you did not make this change, contact your web developer immediately.</p>
                ');
                palMail($creds['email'], 'Admin Password Changed - PAL CMS', $body);
            }
            session_destroy();
            header('Location: index.php?notice=password_changed');
            exit;
        }
    }

    if (!$error) {
        $creds = jsonRead(DATA_DIR . '.credentials.json');
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

  <?php if ($success): ?>
  <div class="alert alert-success"><?php echo $success; ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
  <div class="alert alert-danger"><?php echo e($error); ?></div>
  <?php endif; ?>

  <div class="profile-grid">

    <!-- Admin Email -->
    <div class="card">
      <div class="card-header">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        &nbsp;Admin Email
      </div>
      <div class="card-body">
        <p class="small text-muted mb-3">This email receives password change alerts and password reset links. Set it before using Forgot Password.</p>
        <form method="post">
          <?php echo csrfField(); ?>
          <input type="hidden" name="action" value="change_email">
          <div class="form-group mb-3">
            <label class="form-label" for="new_email">Email Address</label>
            <input type="email" id="new_email" name="new_email" class="form-control" value="<?php echo e($creds['email'] ?? ''); ?>" placeholder="you@example.com" required>
          </div>
          <button type="submit" class="btn btn-primary">Save Email</button>
        </form>
      </div>
    </div>

    <!-- Change Username -->
    <div class="card">
      <div class="card-header">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        &nbsp;Change Username
      </div>
      <div class="card-body">
        <p class="small text-muted mb-3">Current username: <strong><?php echo e($creds['username'] ?? ''); ?></strong></p>
        <form method="post">
          <?php echo csrfField(); ?>
          <input type="hidden" name="action" value="change_username">
          <div class="form-group mb-3">
            <label class="form-label" for="new_username">New Username</label>
            <input type="text" id="new_username" name="new_username" class="form-control" placeholder="admin, your name, or email" autocomplete="username" required>
          </div>
          <div class="form-group mb-3">
            <label class="form-label" for="current_password_u">Current Password</label>
            <div class="input-icon-right">
              <input type="password" id="current_password_u" name="current_password_u" class="form-control" autocomplete="current-password" required>
              <button type="button" class="toggle-pw" data-target="current_password_u" aria-label="Show password">
                <?php echo eyeIconSvg(); ?>
              </button>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Update Username</button>
        </form>
      </div>
    </div>

    <!-- Change Password -->
    <div class="card">
      <div class="card-header">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        &nbsp;Change Password
      </div>
      <div class="card-body">
        <p class="small text-muted mb-3">Minimum 10 characters. After changing, you will be required to log in again. A notification email will be sent to your admin email (if set).</p>
        <form method="post">
          <?php echo csrfField(); ?>
          <input type="hidden" name="action" value="change_password">
          <div class="form-group mb-3">
            <label class="form-label" for="current_password_p">Current Password</label>
            <div class="input-icon-right">
              <input type="password" id="current_password_p" name="current_password_p" class="form-control" autocomplete="current-password" required>
              <button type="button" class="toggle-pw" data-target="current_password_p" aria-label="Show password">
                <?php echo eyeIconSvg(); ?>
              </button>
            </div>
          </div>
          <div class="form-group mb-3">
            <label class="form-label" for="new_password">New Password</label>
            <div class="input-icon-right">
              <input type="password" id="new_password" name="new_password" class="form-control" autocomplete="new-password" required minlength="10">
              <button type="button" class="toggle-pw" data-target="new_password" aria-label="Show password">
                <?php echo eyeIconSvg(); ?>
              </button>
            </div>
          </div>
          <div class="form-group mb-3">
            <label class="form-label" for="confirm_password">Confirm New Password</label>
            <div class="input-icon-right">
              <input type="password" id="confirm_password" name="confirm_password" class="form-control" autocomplete="new-password" required minlength="10">
              <button type="button" class="toggle-pw" data-target="confirm_password" aria-label="Show password">
                <?php echo eyeIconSvg(); ?>
              </button>
            </div>
          </div>
          <button type="submit" class="btn btn-danger">Change Password &amp; Log Out</button>
        </form>
      </div>
    </div>

  </div><!-- /.profile-grid -->
</div>
</div>
<div id="toast-container"></div>
<script src="assets/admin.js"></script>
<script>
document.querySelectorAll('.toggle-pw').forEach(function(btn) {
  btn.addEventListener('click', function() {
    var pw = document.getElementById(this.getAttribute('data-target'));
    if (!pw) return;
    var show = pw.type === 'password';
    pw.type = show ? 'text' : 'password';
    this.classList.toggle('showing', show);
  });
});
</script>
</body>
</html>
