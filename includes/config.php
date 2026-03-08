<?php
// Start session for admin and cart? (Cart uses localStorage, but session for admin)
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'shades_array');
define('DB_USER', 'root');      // change as needed
define('DB_PASS', '');          // change as needed

// Base URL (adjust if not in root)
define('BASE_URL', 'http://localhost/shades-array');

// Create connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Set timezone
date_default_timezone_set('Africa/Lagos'); // Nigerian time for ₦
?>