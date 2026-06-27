<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../auth.php';
sendSecurityHeaders();
header('Content-Type: application/json; charset=utf-8');
requireLogin();
requireCsrf();

$body = json_decode(file_get_contents('php://input'), true) ?? [];
$slug = preg_replace('/[^a-z0-9_-]/', '', strtolower((string)($body['slug']??'')));
if (!$slug) { echo json_encode(['ok' => false, 'message' => 'Invalid slug.']); exit; }
$path = DATA_DIR . 'pages/' . $slug . '.json';
if (!is_file($path)) { echo json_encode(['ok' => false, 'message' => 'Page not found.']); exit; }
$data = $body['data'] ?? [];
array_walk_recursive($data, function(&$v){ if (is_string($v)) $v = strip_tags($v); });
jsonWrite($path, $data);
echo json_encode(['ok' => true, 'message' => 'Page saved.']);
