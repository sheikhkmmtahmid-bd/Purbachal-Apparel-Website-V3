<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../auth.php';
sendSecurityHeaders();
header('Content-Type: application/json; charset=utf-8');
requireLogin();
requireCsrf();

$body = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$prods = jsonRead(DATA_DIR . 'pages/products.json');
$validCats = ['kids', 'mens', 'womens'];
$cat = preg_replace('/[^a-z]/', '', strtolower((string)($body['category']??'')));
if (!in_array($cat, $validCats)) { echo json_encode(['ok' => false, 'message' => 'Invalid category.']); exit; }
if (!empty($body['delete'])) {
    $file = basename((string)$body['delete']);
    $prods[$cat] = array_values(array_filter($prods[$cat]??[], fn($f) => $f !== $file));
    @unlink(UPLOAD_DIR . 'products/' . $cat . '/' . $file);
    jsonWrite(DATA_DIR . 'pages/products.json', $prods);
    echo json_encode(['ok' => true, 'message' => 'Deleted.']); exit;
}
if (!empty($body['images']) && is_array($body['images'])) {
    $prods[$cat] = array_values(array_map('basename', $body['images']));
    jsonWrite(DATA_DIR . 'pages/products.json', $prods);
    echo json_encode(['ok' => true, 'message' => 'Products saved.']); exit;
}
echo json_encode(['ok' => false, 'message' => 'No action.']);
