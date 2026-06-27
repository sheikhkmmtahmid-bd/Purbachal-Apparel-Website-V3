<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../auth.php';
sendSecurityHeaders();
header('Content-Type: application/json; charset=utf-8');
requireLogin();
requireCsrf();

$body = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$teamData = jsonRead(DATA_DIR . 'pages/team.json');
$gallery = $teamData['gallery'] ?? [];
if (!empty($body['delete'])) {
    $file = basename((string)$body['delete']);
    $gallery = array_values(array_filter($gallery, fn($f) => $f !== $file));
    @unlink(UPLOAD_DIR . 'gallery/' . $file);
    $teamData['gallery'] = $gallery;
    jsonWrite(DATA_DIR . 'pages/team.json', $teamData);
    echo json_encode(['ok' => true, 'message' => 'Deleted.']); exit;
}
if (!empty($body['images']) && is_array($body['images'])) {
    $teamData['gallery'] = array_values(array_map('basename', $body['images']));
    jsonWrite(DATA_DIR . 'pages/team.json', $teamData);
    echo json_encode(['ok' => true, 'message' => 'Gallery order saved.']); exit;
}
echo json_encode(['ok' => false, 'message' => 'No action.']);
