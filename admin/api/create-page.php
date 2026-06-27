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
$title = sanitize((string)($body['title']??''));
if (!$slug) { echo json_encode(['ok' => false, 'message' => 'Invalid slug.']); exit; }
$customDir = DATA_DIR . 'custom/';
if (!is_dir($customDir)) mkdir($customDir, 0755, true);
$path = $customDir . $slug . '.json';
if (is_file($path)) { echo json_encode(['ok' => false, 'message' => 'Page already exists.']); exit; }
jsonWrite($path, ['title' => $title, 'sections' => []]);
echo json_encode(['ok' => true, 'message' => 'Page created.', 'slug' => $slug]);
