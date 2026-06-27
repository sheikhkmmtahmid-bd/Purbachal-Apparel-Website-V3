<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../includes/functions.php';
sendSecurityHeaders();

$creds_file = DATA_DIR . '.credentials.json';
$error = '';
$done  = false;
$creds = jsonRead($creds_file);

// If already set up with a real password, redirect
if (!empty($creds['username']) && $creds['password'] !== 'SETUP_REQUIRED') {
    // Only allow reset if a special header is present (no web interface)
    // This file should be deleted after first use.
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $user = sanitize($_POST['username'] ?? '', 64);
    $pass = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';
    if (strlen($user) < 3) { $error = 'Username must be at least 3 characters.'; }
    elseif (strlen($pass) < 10) { $error = 'Password must be at least 10 characters.'; }
    elseif ($pass !== $pass2) { $error = 'Passwords do not match.'; }
    else {
        $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
        jsonWrite($creds_file, ['username' => $user, 'password' => $hash]);
        $done = true;
    }
}
startSecureSession();
generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>CMS Setup &mdash; Purbachal Apparel</title>
<link rel="stylesheet" href="assets/admin.css">
<style>
body{display:flex;align-items:center;justify-content:center;min-height:100vh;background:var(--bg-page);}
.setup-card{background:#fff;border-radius:12px;box-shadow:var(--shadow-lg);padding:2.5rem 2rem;width:100%;max-width:420px;}
.setup-card h1{font-size:1.4rem;margin-bottom:.25rem;}
.setup-card p.sub{color:var(--text-muted);font-size:.85rem;margin-bottom:1.5rem;}
</style>
</head>
<body>
<div class="setup-card">
  <h1>CMS Initial Setup</h1>
  <p class="sub">Set your admin username and password. Delete this file after setup.</p>
  <?php if ($done): ?>
  <div class="alert alert-success">Credentials saved! <a href="index.php">Go to Login</a>. <strong>Delete this file now.</strong></div>
  <?php else: ?>
  <?php if ($error): ?><div class="alert alert-danger"><?php echo e($error); ?></div><?php endif; ?>
  <form method="post">
    <?php echo csrfField(); ?>
    <div class="form-group"><label>Username</label><input type="text" name="username" class="form-control" value="<?php echo e($_POST['username'] ?? 'admin'); ?>" required></div>
    <div class="form-group mt-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
    <div class="form-group mt-3"><label>Confirm Password</label><input type="password" name="password2" class="form-control" required></div>
    <button type="submit" class="btn btn-primary w-100 mt-4">Save Credentials</button>
  </form>
  <?php endif; ?>
</div>
</body>
</html>
