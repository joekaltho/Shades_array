<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT id, name, description, price, image_url, is_new FROM products ORDER BY created_at DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format price and escape output
    foreach ($products as &$product) {
        $product['name'] = escape($product['name']);
        $product['description'] = escape($product['description']);
        $product['price_formatted'] = '₦' . number_format($product['price'], 2); // For display
        $product['image_url'] = BASE_URL . '/uploads/' . escape($product['image_url']);
        $product['is_new'] = (bool)$product['is_new'];

        // ✅ Keep raw price for JS calculations
        $product['price'] = (float)$product['price'];
    }

    echo json_encode(['success' => true, 'products' => $products]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Could not fetch products.']);
}
?>