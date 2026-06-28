<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../auth.php';
sendSecurityHeaders();
header('Content-Type: application/json; charset=utf-8');
requireLogin();
requireCsrf();

$body     = json_decode(file_get_contents('php://input'), true) ?? [];
$teamData = jsonRead(DATA_DIR . 'pages/team.json');
$gallery  = $teamData['gallery_items'] ?? [];

if (!empty($body['delete'])) {
    $file    = basename((string)$body['delete']);
    $gallery = array_values(array_filter($gallery, function ($item) use ($file) {
        $fn = is_array($item) ? ($item['filename'] ?? '') : $item;
        return $fn !== $file;
    }));
    @unlink(UPLOAD_DIR . 'gallery/' . $file);
    $teamData['gallery_items'] = $gallery;
    jsonWrite(DATA_DIR . 'pages/team.json', $teamData);
    echo json_encode(['ok' => true, 'message' => 'Deleted.']); exit;
}

if (!empty($body['items']) && is_array($body['items'])) {
    $clean = [];
    foreach ($body['items'] as $item) {
        if (!is_array($item) || empty($item['filename'])) continue;
        $clean[] = [
            'filename' => basename((string)$item['filename']),
            'alt'      => mb_substr(trim((string)($item['alt'] ?? '')), 0, 200),
        ];
    }
    $teamData['gallery_items'] = $clean;
    jsonWrite(DATA_DIR . 'pages/team.json', $teamData);
    echo json_encode(['ok' => true, 'message' => 'Gallery saved.']); exit;
}

echo json_encode(['ok' => false, 'message' => 'No action.']);
