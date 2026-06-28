<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../auth.php';
sendSecurityHeaders();
header('Content-Type: application/json; charset=utf-8');
requireLogin();
requireCsrf();

$body   = json_decode(file_get_contents('php://input'), true) ?? [];
$slug   = preg_replace('/[^a-z0-9_-]/', '', strtolower((string)($body['slug'] ?? '')));
$action = (string)($body['action'] ?? ''); // 'suspend' or 'activate'

if (!$slug || !in_array($action, ['suspend', 'activate'], true)) {
    echo json_encode(['ok' => false, 'error' => 'Invalid request.']); exit;
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
    echo json_encode(['ok' => false, 'error' => 'Cannot change status of system pages.']); exit;
}

$pageData  = jsonRead($jsonFile);
$newStatus = $action === 'suspend' ? 'suspended' : 'active';

$pageData['status'] = $newStatus;
if (!jsonWrite($jsonFile, $pageData)) {
    echo json_encode(['ok' => false, 'error' => 'Failed to update page status.']); exit;
}

// Update nav.json (already loaded above)
$found = false;
foreach ($nav['pages'] as &$p) {
    if ($p['slug'] === $slug) {
        $p['status'] = $newStatus;
        $found = true;
        break;
    }
    foreach ($p['dropdown'] ?? [] as &$child) {
        if ($child['slug'] === $slug) {
            $child['status'] = $newStatus;
            $found = true;
            break 2;
        }
    }
    unset($child);
}
unset($p);

if ($found) jsonWrite(DATA_DIR . 'nav.json', $nav);

$msg = $action === 'suspend' ? 'Page suspended.' : 'Page activated.';
echo json_encode(['ok' => true, 'status' => $newStatus, 'message' => $msg]);
