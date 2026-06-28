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

$path = DATA_DIR . 'pages/' . $slug . '.json';
if (!is_file($path)) { echo json_encode(['ok' => false, 'message' => 'Page not found.']); exit; }

// Load existing data to preserve any extra fields
$existing = jsonRead($path);

$sections = $body['sections'] ?? [];
$allowedTags = '<br><span><strong><em><b><i><a><p><ul><ol><li>';

// Sanitize all string values in sections
array_walk_recursive($sections, function (&$v) use ($allowedTags) {
    if (is_string($v)) $v = strip_tags($v, $allowedTags);
});

$existing['sections']  = $sections;
if (isset($body['title']))    $existing['title']    = sanitize((string)$body['title']);
if (isset($body['meta_desc'])) $existing['meta_desc'] = sanitize((string)$body['meta_desc']);

jsonWrite($path, $existing);
echo json_encode(['ok' => true, 'message' => 'Page saved.']);
