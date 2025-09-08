<?php
require_once '../config/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

requireAdmin();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    if (!isset($_FILES['image'])) {
        throw new Exception('No file uploaded');
    }

    $file = $_FILES['image'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Upload error: ' . $file['error']);
    }

    // Validate mime type and extension
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp'
    ];

    if (!isset($allowed[$mime])) {
        throw new Exception('Unsupported file type');
    }

    // Max size: 5MB
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception('File too large (max 5MB)');
    }

    $uploadsDir = __DIR__ . '/../public/uploads';
    if (!is_dir($uploadsDir)) {
        if (!mkdir($uploadsDir, 0775, true) && !is_dir($uploadsDir)) {
            throw new Exception('Failed to create uploads directory');
        }
    }

    // Generate unique filename
    $ext = $allowed[$mime];
    $base = bin2hex(random_bytes(8));
    $filename = $base . '.' . $ext;
    $destPath = $uploadsDir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        throw new Exception('Failed to save file');
    }

    // Build public URL/path relative to site root
    $publicPath = 'public/uploads/' . $filename;

    echo json_encode([
        'success' => true,
        'path' => $publicPath,
        'mime' => $mime,
        'size' => $file['size']
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>

