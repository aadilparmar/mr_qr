<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn()) { http_response_code(401); echo json_encode(['error' => 'Unauthorized']); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['error' => 'Method not allowed']); exit; }

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$type = $input['type'] ?? 'text';
$data = $input['data'] ?? [];
$title = $input['title'] ?? '';
$size = min(1024, max(64, (int)($input['size'] ?? 256)));
$fgColor = $input['fg_color'] ?? '#000000';
$bgColor = $input['bg_color'] ?? '#ffffff';

try {
    $content = encodeQRContent($type, $data);
    if (empty($content)) throw new Exception('No content to encode');
    $filename = generateFilename($type);
    $settings = ['fg_color' => $fgColor, 'bg_color' => $bgColor, 'size' => $size];
    $id = saveQRCode($_SESSION['user_id'], $type, $title ?: $type, $content, $filename, $settings);
    echo json_encode(['success' => true, 'id' => $id, 'content' => $content, 'filename' => $filename, 'settings' => $settings]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
