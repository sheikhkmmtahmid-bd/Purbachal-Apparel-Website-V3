<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../auth.php';
sendSecurityHeaders();
header('Content-Type: application/json; charset=utf-8');
requireLogin();
requireCsrf();

$body       = json_decode(file_get_contents('php://input'), true) ?? [];
$label      = sanitize((string)($body['label'] ?? ''), 80);
$rawSlug    = strtolower(trim((string)($body['slug'] ?? $label)));
$slug       = preg_replace('/[^a-z0-9-]/', '', str_replace([' ', '_'], '-', $rawSlug));
$slug       = preg_replace('/-+/', '-', trim($slug, '-'));
$navLabel   = sanitize((string)($body['nav_label'] ?? $label), 80);
$showNav    = (bool)($body['show_in_nav'] ?? true);
$parentSlug = preg_replace('/[^a-z0-9_-]/', '', strtolower((string)($body['parent_slug'] ?? '')));

if (!$label) {
    echo json_encode(['ok' => false, 'error' => 'Page name is required.']); exit;
}
if (strlen($slug) < 2 || strlen($slug) > 60) {
    echo json_encode(['ok' => false, 'error' => 'Slug must be 2-60 characters.']); exit;
}

$reserved = ['index','about','team','clients','certificates','products','sustainability','contact',
             'admin','includes','data','uploads','assets','styles','nav','favicon','error'];
if (in_array($slug, $reserved, true)) {
    echo json_encode(['ok' => false, 'error' => "The slug '{$slug}' is reserved."]); exit;
}

$siteRoot = realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR;
$phpFile  = $siteRoot . $slug . '.php';
$jsonFile = DATA_DIR . 'pages/' . $slug . '.json';

if (file_exists($phpFile) || file_exists($jsonFile)) {
    echo json_encode(['ok' => false, 'error' => "A page with slug '{$slug}' already exists."]); exit;
}

$pageData = [
    'title'     => $label . ' | Purbachal Apparel Limited',
    'meta_desc' => '',
    'nav_label' => $navLabel,
    'status'    => 'active',
    'sections'  => [],
];
if (!jsonWrite($jsonFile, $pageData)) {
    echo json_encode(['ok' => false, 'error' => 'Failed to create page data file.']); exit;
}

$es = addslashes($slug);
$el = addslashes($label);
$phpContent = '<?php' . "\n" .
'define(\'_PAL_CMS_\', true);' . "\n" .
'require_once __DIR__ . \'/includes/functions.php\';' . "\n" .
'require_once __DIR__ . \'/includes/security.php\';' . "\n" .
'require_once __DIR__ . \'/includes/section-renderer.php\';' . "\n" .
'sendSecurityHeaders();' . "\n" .
'$pageFile = \'' . $es . '.php\';' . "\n" .
'$d = jsonRead(DATA_DIR . \'pages/' . $es . '.json\');' . "\n" .
'if (($d[\'status\'] ?? \'active\') !== \'active\') {' . "\n" .
'    http_response_code(404);' . "\n" .
'    $pageTitle = \'404 - Page Not Found\';' . "\n" .
'    require_once __DIR__ . \'/includes/site-header.php\';' . "\n" .
'    echo \'<main><section class="section"><div class="container" style="text-align:center;padding:80px 0"><h1 style="font-size:5rem;font-weight:900;color:var(--teal)">404</h1><h2 style="margin-bottom:12px">Page Not Found</h2><p style="color:var(--gray-500);margin-bottom:32px">This page is not available.</p><a href="index.php" class="btn btn-primary">Return Home</a></div></section></main>\';' . "\n" .
'    require_once __DIR__ . \'/includes/site-footer.php\';' . "\n" .
'    exit;' . "\n" .
'}' . "\n" .
'$pageTitle       = $d[\'title\']    ?? \'' . $el . ' | Purbachal Apparel Limited\';' . "\n" .
'$pageDescription = $d[\'meta_desc\'] ?? \'\';' . "\n" .
'$sections        = $d[\'sections\']  ?? [];' . "\n" .
'require_once __DIR__ . \'/includes/site-header.php\';' . "\n" .
'?>' . "\n" .
'<main>' . "\n" .
'<?php pal_render_sections($sections); ?>' . "\n" .
'</main>' . "\n" .
'<?php require_once __DIR__ . \'/includes/site-footer.php\'; ?>' . "\n";

if (file_put_contents($phpFile, $phpContent, LOCK_EX) === false) {
    @unlink($jsonFile);
    echo json_encode(['ok' => false, 'error' => 'Failed to create page PHP file.']); exit;
}

$nav    = jsonRead(DATA_DIR . 'nav.json');
$maxOrd = 0;
foreach ($nav['pages'] as $p) {
    if (($p['order'] ?? 0) > $maxOrd) $maxOrd = $p['order'];
}

$newEntry = [
    'slug'        => $slug,
    'label'       => $navLabel,
    'file'        => $slug . '.php',
    'order'       => $maxOrd + 1,
    'builtin'     => false,
    'status'      => 'active',
    'show_in_nav' => $showNav,
    'dropdown'    => [],
];

if ($parentSlug && $showNav) {
    foreach ($nav['pages'] as &$p) {
        if ($p['slug'] === $parentSlug) {
            $p['dropdown'][] = ['slug' => $slug, 'label' => $navLabel, 'file' => $slug . '.php'];
            $newEntry['show_in_nav'] = false;
            break;
        }
    }
    unset($p);
}

$nav['pages'][] = $newEntry;
if (!jsonWrite(DATA_DIR . 'nav.json', $nav)) {
    echo json_encode(['ok' => false, 'error' => 'Page files created but nav update failed.']); exit;
}

echo json_encode(['ok' => true, 'slug' => $slug, 'file' => $slug . '.php', 'message' => "Page '{$label}' created."]);
