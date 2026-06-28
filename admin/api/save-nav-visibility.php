<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../auth.php';
sendSecurityHeaders();
header('Content-Type: application/json; charset=utf-8');
requireLogin();
requireCsrf();

$body       = json_decode(file_get_contents('php://input'), true) ?? [];
$slug       = preg_replace('/[^a-z0-9_-]/', '', strtolower((string)($body['slug'] ?? '')));
$showInNav  = (bool)($body['show_in_nav'] ?? true);

if (!$slug) {
    echo json_encode(['ok' => false, 'error' => 'Invalid slug.']); exit;
}

$jsonFile = DATA_DIR . 'pages/' . $slug . '.json';
if (!is_file($jsonFile)) {
    echo json_encode(['ok' => false, 'error' => 'Page not found.']); exit;
}

$pageData = jsonRead($jsonFile);
if ($pageData['builtin'] ?? false) {
    echo json_encode(['ok' => false, 'error' => 'Cannot modify system pages.']); exit;
}

// Update nav.json show_in_nav flag
$nav   = jsonRead(DATA_DIR . 'nav.json');
$found = false;
foreach ($nav['pages'] as &$p) {
    if ($p['slug'] === $slug) {
        $p['show_in_nav'] = $showInNav;
        $found = true;
        break;
    }
    foreach ($p['dropdown'] ?? [] as &$child) {
        if ($child['slug'] === $slug) {
            $child['show_in_nav'] = $showInNav;
            $found = true;
            break 2;
        }
    }
    unset($child);
}
unset($p);

if (!$found) {
    echo json_encode(['ok' => false, 'error' => 'Page not found in nav.']); exit;
}

jsonWrite(DATA_DIR . 'nav.json', $nav);

echo json_encode(['ok' => true, 'show_in_nav' => $showInNav]);
