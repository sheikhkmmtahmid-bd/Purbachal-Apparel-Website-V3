<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../auth.php';
sendSecurityHeaders();
header('Content-Type: application/json; charset=utf-8');
requireLogin();
requireCsrf();

$body = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$footer = jsonRead(DATA_DIR . 'footer.json');
if (isset($body['brand_desc'])) $footer['brand_desc'] = sanitize((string)$body['brand_desc'], 500);
if (isset($body['pdf'])) {
    $footer['pdf'] = [
        'label' => sanitize((string)($body['pdf']['label']??'')),
        'title' => sanitize((string)($body['pdf']['title']??'')),
        'url'   => sanitize((string)($body['pdf']['url']??'')),
    ];
}
jsonWrite(DATA_DIR . 'footer.json', $footer);
echo json_encode(['ok' => true, 'message' => 'Footer saved.']);
