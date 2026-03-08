<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireAdminLogin();

$message = '';
$error = '';

// Handle Add Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid CSRF token.';
    } else {
        if ($_POST['action'] === 'add') {
            $name = trim($_POST['name']);
            $description = trim($_POST['description']);
            $price = floatval($_POST['price']);
            $is_new = isset($_POST['is_new']) ? 1 : 0;

            // Upload image
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload = uploadImage($_FILES['image']);
                if (isset($upload['error'])) {
                    $error = $upload['error'];
                } else {
                    $image_url = $upload['success'];
                    // Insert into DB
                    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image_url, is_new) VALUES (?, ?, ?, ?, ?)");
                    if ($stmt->execute([$name, $description, $price, $image_url, $is_new])) {
                        $message = 'Product added successfully.';
                    } else {
                        $error = 'Failed to add product.';
                    }
                }
            } else {
                $error = 'Please upload an image.';
            }
        } elseif ($_POST['action'] === 'delete') {
            $id = intval($_POST['id']);
            // First get image to delete file
            $stmt = $pdo->prepare("SELECT image_url FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch();
            if ($product) {
                $filePath = __DIR__ . '/../uploads/' . $product['image_url'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            if ($stmt->execute([$id])) {
                $message = 'Product deleted.';
            } else {
                $error = 'Delete failed.';
            }
        } elseif ($_POST['action'] === 'toggle_new') {
            $id = intval($_POST['id']);
            $current = intval($_POST['current']);
            $newVal = $current ? 0 : 1;
            $stmt = $pdo->prepare("UPDATE products SET is_new = ? WHERE id = ?");
            $stmt->execute([$newVal, $id]);
            $message = 'Product updated.';
        }
    }
}

// Fetch all products
$products = $pdo->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll();

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products - Shades Array Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-products">
    <nav class="admin-nav">
        <h1>Manage Products</h1>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="container">
        <?php if ($message): ?>
            <div class="success-msg"><?php echo escape($message); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error-msg"><?php echo escape($error); ?></div>
        <?php endif; ?>

        <h2>Add New Product</h2>
        <form method="POST" enctype="multipart/form-data" class="product-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="price">Price (₦)</label>
                <input type="number" step="0.01" id="price" name="price" required>
            </div>
            <div class="form-group">
                <label for="image">Image (max 5MB, JPG/PNG/WebP)</label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" required>
            </div>
            <div class="form-group checkbox">
                <label>
                    <input type="checkbox" name="is_new"> Mark as "New"
                </label>
            </div>
            <button type="submit" class="btn-primary">Add Product</button>
        </form>

        <h2>Existing Products</h2>
        <table class="products-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>New</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td><?php echo $p['id']; ?></td>
                    <td><img src="<?php echo BASE_URL . '/uploads/' . escape($p['image_url']); ?>" width="50" height="50" style="object-fit: cover;"></td>
                    <td><?php echo escape($p['name']); ?></td>
                    <td>₦<?php echo number_format($p['price'], 2); ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="action" value="toggle_new">
                            <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                            <input type="hidden" name="current" value="<?php echo $p['is_new']; ?>">
                            <button type="submit" class="btn-toggle <?php echo $p['is_new'] ? 'active' : ''; ?>">
                                <?php echo $p['is_new'] ? 'New' : 'Set New'; ?>
                            </button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Delete this product?');">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                            <button type="submit" class="btn-delete">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>