<?php
if (!defined('_PAL_CMS_')) die('Direct access not permitted.');
require_once __DIR__ . '/../includes/security.php';

function requireLogin(): void {
    startSecureSession();
    generateCsrfToken();
    $adminBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/') . '/';
    if (empty($_SESSION['pal_admin_logged'])) {
        header('Location: ' . (defined('ADMIN_URL') ? ADMIN_URL : $adminBase) . 'index.php');
        exit;
    }
    // 30-minute inactivity timeout
    $timeout = 30 * 60;
    if (isset($_SESSION['pal_last_active']) && (time() - $_SESSION['pal_last_active']) > $timeout) {
        session_unset();
        session_destroy();
        header('Location: ' . (defined('ADMIN_URL') ? ADMIN_URL : $adminBase) . 'index.php?timeout=1');
        exit;
    }
    $_SESSION['pal_last_active'] = time();
}
