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

if (!$slug) {
    echo json_encode(['ok' => false, 'error' => 'Invalid slug.']); exit;
}

$jsonFile = DATA_DIR . 'pages/' . $slug . '.json';
if (!is_file($jsonFile)) {
    echo json_encode(['ok' => false, 'error' => 'Page not found.']); exit;
}

// Check builtin from nav.json (page JSONs don't store this field)
$nav      = jsonRead(DATA_DIR . 'nav.json');
$isBuiltin = true;
foreach ($nav['pages'] as $p) {
    if ($p['slug'] === $slug) { $isBuiltin = $p['builtin'] ?? true; break; }
    foreach ($p['dropdown'] ?? [] as $ch) {
        if ($ch['slug'] === $slug) { $isBuiltin = $ch['builtin'] ?? true; break 2; }
    }
}
if ($isBuiltin) {
    echo json_encode(['ok' => false, 'error' => 'Cannot delete system pages.']); exit;
}

$pageData = jsonRead($jsonFile);

// Must be suspended first
if (($pageData['status'] ?? 'active') !== 'suspended') {
    echo json_encode(['ok' => false, 'error' => 'Page must be suspended before it can be deleted.']); exit;
}

$siteRoot = realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR;
$phpFile  = $siteRoot . $slug . '.php';

// Remove PHP file
if (is_file($phpFile) && !@unlink($phpFile)) {
    echo json_encode(['ok' => false, 'error' => 'Failed to delete page PHP file.']); exit;
}

// Remove JSON file
if (!@unlink($jsonFile)) {
    echo json_encode(['ok' => false, 'error' => 'Failed to delete page data file.']); exit;
}

// Remove from nav.json (already loaded above)
$nav['pages'] = array_values(array_filter($nav['pages'], fn($p) => $p['slug'] !== $slug));
foreach ($nav['pages'] as &$p) {
    if (!empty($p['dropdown'])) {
        $p['dropdown'] = array_values(array_filter($p['dropdown'], fn($c) => $c['slug'] !== $slug));
    }
}
unset($p);
jsonWrite(DATA_DIR . 'nav.json', $nav);

echo json_encode(['ok' => true, 'message' => "Page '{$slug}' permanently deleted."]);
