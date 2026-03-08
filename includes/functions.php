<?php
require_once 'config.php';

// Generate CSRF token and store in session
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Clean user input for output (prevents XSS)
function escape($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Redirect if not admin
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Upload image with validation
function uploadImage($file) {
    $targetDir = __DIR__ . '/../uploads/';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    // Check if file was uploaded without errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Upload error.'];
    }

    // Validate file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowedTypes)) {
        return ['error' => 'Only JPG, PNG, and WebP images are allowed.'];
    }

    // Validate size
    if ($file['size'] > $maxSize) {
        return ['error' => 'File size must be less than 5MB.'];
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFilename = uniqid('img_', true) . '.' . $extension;
    $targetFile = $targetDir . $newFilename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return ['success' => $newFilename];
    } else {
        return ['error' => 'Failed to move uploaded file.'];
    }
}
?>