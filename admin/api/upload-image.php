<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../auth.php';
sendSecurityHeaders();
header('Content-Type: application/json; charset=utf-8');
requireLogin();
requireCsrf();

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
