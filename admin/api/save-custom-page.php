<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../auth.php';
sendSecurityHeaders();
header('Content-Type: application/json; charset=utf-8');
requireLogin();
requireCsrf();

$body = json_decode(file_get_contents('php://input'), true) ?? [];
$slug = preg_replace('/[^a-z0-9_-]/', '', strtolower((string)($body['slug'] ?? '')));
if (!$slug) { echo json_encode(['ok' => false, 'message' => 'Invalid slug.']); exit; }

$customDir = DATA_DIR . 'custom/';
$path = $customDir . $slug . '.json';
if (!is_file($path)) { echo json_encode(['ok' => false, 'message' => 'Page not found.']); exit; }

$title = sanitize((string)($body['title'] ?? ''), 200);
$sections = is_array($body['sections'] ?? null) ? $body['sections'] : [];

$clean = [];
foreach ($sections as $s) {
    $type = preg_replace('/[^a-z_]/', '', (string)($s['type'] ?? ''));
    if (!in_array($type, ['hero', 'text_block', 'cta_banner', 'image_grid'], true)) continue;
    $d = is_array($s['data'] ?? null) ? $s['data'] : [];
    array_walk_recursive($d, function (&$v) { if (is_string($v)) $v = strip_tags($v); });
    $clean[] = [
        'id'   => preg_replace('/[^a-z0-9_]/', '', (string)($s['id'] ?? '')),
        'type' => $type,
        'data' => $d,
    ];
}

jsonWrite($path, ['title' => $title, 'sections' => $clean]);
echo json_encode(['ok' => true, 'message' => 'Page saved.']);
