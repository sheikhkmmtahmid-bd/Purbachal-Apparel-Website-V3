<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../auth.php';
sendSecurityHeaders();
header('Content-Type: application/json; charset=utf-8');
requireLogin();
requireCsrf();

$site = jsonRead(DATA_DIR . 'site.json');
$body = json_decode(file_get_contents('php://input'), true) ?? [];
$fields = ['company_name','company_name_short','tagline','address','email','phone1','phone1_tel','phone2','phone2_tel','bgmea','erc','map_embed','footer_desc'];
foreach ($fields as $f) { if (isset($body[$f])) $site[$f] = sanitize((string)$body[$f]); }
if (isset($body['social']) && is_array($body['social'])) {
    foreach (['facebook','linkedin','youtube','whatsapp'] as $s) {
        if (isset($body['social'][$s])) $site['social'][$s] = sanitize((string)$body['social'][$s]);
    }
}
jsonWrite(DATA_DIR . 'site.json', $site);
echo json_encode(['ok' => true, 'message' => 'Settings saved.']);
