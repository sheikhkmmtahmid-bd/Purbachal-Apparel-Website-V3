<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../auth.php';
sendSecurityHeaders();
header('Content-Type: application/json; charset=utf-8');
requireLogin();
requireCsrf();

$body = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$nav = jsonRead(DATA_DIR . 'nav.json');
if (isset($body['pages']) && is_array($body['pages'])) {
    $nav['pages'] = array_map(function($p){
        return ['label' => sanitize((string)($p['label']??''),60), 'file' => sanitize((string)($p['file']??''),60)];
    }, $body['pages']);
}
if (isset($body['cta'])) {
    $nav['cta'] = ['label' => sanitize((string)($body['cta']['label']??''),60), 'url' => sanitize((string)($body['cta']['url']??''),200)];
}
jsonWrite(DATA_DIR . 'nav.json', $nav);
echo json_encode(['ok' => true, 'message' => 'Navigation saved.']);
