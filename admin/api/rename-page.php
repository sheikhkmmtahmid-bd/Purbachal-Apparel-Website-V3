<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../auth.php';
sendSecurityHeaders();
header('Content-Type: application/json; charset=utf-8');
requireLogin();
requireCsrf();

$body     = json_decode(file_get_contents('php://input'), true) ?? [];
$slug     = preg_replace('/[^a-z0-9_-]/', '', strtolower((string)($body['slug'] ?? '')));
$newLabel = sanitize((string)($body['label'] ?? ''), 80);

if (!$slug || !$newLabel) {
    echo json_encode(['ok' => false, 'error' => 'Slug and new label are required.']); exit;
}

$jsonFile = DATA_DIR . 'pages/' . $slug . '.json';
if (!is_file($jsonFile)) {
    echo json_encode(['ok' => false, 'error' => 'Page not found.']); exit;
}

$pageData = jsonRead($jsonFile);

// Check builtin from nav.json (page JSONs don't store this field)
$nav = jsonRead(DATA_DIR . 'nav.json');
$isBuiltin = true;
foreach ($nav['pages'] as $p) {
    if ($p['slug'] === $slug) { $isBuiltin = $p['builtin'] ?? true; break; }
    foreach ($p['dropdown'] ?? [] as $ch) {
        if ($ch['slug'] === $slug) { $isBuiltin = $ch['builtin'] ?? true; break 2; }
    }
}
if ($isBuiltin) {
    echo json_encode(['ok' => false, 'error' => 'Cannot rename system pages.']); exit;
}

// Update nav_label in page JSON
$pageData['nav_label'] = $newLabel;
jsonWrite($jsonFile, $pageData);

// Update label in nav.json (already loaded above)
foreach ($nav['pages'] as &$p) {
    if ($p['slug'] === $slug) {
        $p['label'] = $newLabel;
        break;
    }
    foreach ($p['dropdown'] ?? [] as &$child) {
        if ($child['slug'] === $slug) {
            $child['label'] = $newLabel;
            break 2;
        }
    }
    unset($child);
}
unset($p);
jsonWrite(DATA_DIR . 'nav.json', $nav);

echo json_encode(['ok' => true, 'message' => "Page renamed to '{$newLabel}'."]);
