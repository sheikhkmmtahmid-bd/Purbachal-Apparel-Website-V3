<?php
ini_set('display_errors', '0');
ob_start();
define('_PAL_CMS_', true);
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../auth.php';
sendSecurityHeaders();
header('Content-Type: application/json; charset=utf-8');
requireLogin();
requireCsrf();

if (empty($_FILES['file'])) { ob_end_clean(); echo json_encode(['ok' => false, 'message' => 'No file.']); exit; }
$res = validateAndProcessUpload($_FILES['file'], UPLOAD_DIR . 'logo/');
if (!$res['ok']) { ob_end_clean(); echo json_encode(['ok' => false, 'message' => $res['error']]); exit; }
$site = jsonRead(DATA_DIR . 'site.json');
if (!empty($site['logo'])) @unlink(UPLOAD_DIR . 'logo/' . $site['logo']);
$site['logo'] = $res['filename'];
jsonWrite(DATA_DIR . 'site.json', $site);
ob_end_clean();
echo json_encode(['ok' => true, 'message' => 'Logo uploaded.', 'filename' => $res['filename']]);
