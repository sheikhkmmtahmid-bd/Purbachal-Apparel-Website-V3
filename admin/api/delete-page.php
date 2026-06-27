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
$path = DATA_DIR . 'custom/' . $slug . '.json';
if (!is_file($path)) { echo json_encode(['ok' => false, 'message' => 'Page not found.']); exit; }
@unlink($path);
echo json_encode(['ok' => true, 'message' => 'Deleted.']);
