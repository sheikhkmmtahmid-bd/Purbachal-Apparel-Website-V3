<?php
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', dirname(__DIR__, 2) . '/data/php_errors.log');

// Guarantee a JSON response even on fatal error / OOM / parse error
register_shutdown_function(function () {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR], true)) {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
        // ob_get_level() > 0 means something buffered output; flush it away so only JSON remains
        while (ob_get_level()) {
            ob_end_clean();
        }
        echo json_encode([
            'ok'      => false,
            'message' => 'PHP fatal: ' . $err['message'] . ' in ' . basename($err['file']) . ':' . $err['line'],
        ]);
    }
});

define('_PAL_CMS_', true);
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../auth.php';
sendSecurityHeaders();
header('Content-Type: application/json; charset=utf-8');
requireLogin();
requireCsrf();

try {
    $allowed = ['gallery', 'pages', 'kids', 'mens', 'womens'];
    $dest    = preg_replace('/[^a-z]/', '', strtolower((string)($_POST['dest'] ?? 'gallery')));
    if (!in_array($dest, $allowed, true)) {
        echo json_encode(['ok' => false, 'message' => 'Invalid destination.']); exit;
    }
    if (empty($_FILES['file'])) {
        echo json_encode(['ok' => false, 'message' => 'No file provided.']); exit;
    }
    $full = UPLOAD_DIR . $dest . '/';
    if (!is_dir($full) && !mkdir($full, 0755, true)) {
        echo json_encode(['ok' => false, 'message' => 'Cannot create upload directory.']); exit;
    }
    $res = validateAndProcessUpload($_FILES['file'], $full);
    if (!$res['ok']) {
        echo json_encode(['ok' => false, 'message' => $res['error']]); exit;
    }
    echo json_encode([
        'ok'       => true,
        'message'  => 'Uploaded.',
        'filename' => $res['filename'],
        'url'      => '../uploads/' . $dest . '/' . $res['filename'],
        'path'     => 'uploads/' . $dest . '/' . $res['filename'],
    ]);
} catch (Throwable $e) {
    echo json_encode(['ok' => false, 'message' => 'Upload error: ' . $e->getMessage()]);
}
