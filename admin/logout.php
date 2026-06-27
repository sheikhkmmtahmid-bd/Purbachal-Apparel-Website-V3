<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../includes/security.php';
startSecureSession();
session_unset();
session_destroy();
header('Location: index.php');
exit;
