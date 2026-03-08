<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireAdminLogin();

// Get total products
$stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
$totalProducts = $stmt->fetch()['total'];

// Get total inventory value (sum of product prices)
$stmtValue = $pdo->query("SELECT SUM(price) as total_value FROM products");
$totalValue = $stmtValue->fetch()['total_value'];

// Format total value
$totalValueFormatted = $totalValue ? '₦' . number_format($totalValue, 2) : '₦0.00';

$csrf_token = generateCSRFToken(); // for any forms if needed
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Shades Array Admin</title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body.admin-dashboard {
            font-family: Arial, sans-serif;
            background: #f4f5f7;
            margin: 0;
            padding: 0;
        }
        .admin-nav {
            background: #1a2b4c;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
        }
        .admin-nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: 500;
        }
        .dashboard-stats {
            display: flex;
            gap: 20px;
            padding: 30px;
            flex-wrap: wrap;
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px 25px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            flex: 1 1 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 200px;
            text-align: center;
        }
        .stat-card i {
            font-size: 2rem;
            color: #c6a43f;
            margin-bottom: 10px;
        }
        .stat-card h3 {
            margin: 0;
            font-size: 1rem;
            color: #555;
            margin-bottom: 5px;
        }
        .stat-card .stat-number {
            font-size: 1.6rem;
            font-weight: bold;
            color: #1a2b4c;
        }
        .dashboard-links {
            padding: 0 30px 30px 30px;
        }
        .btn-primary {
            background: #c6a43f;
            color: #fff;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: 0.2s;
        }
        .btn-primary:hover {
            background: #a58b34;
        }
    </style>
</head>
<body class="admin-dashboard">
    <nav class="admin-nav">
        <h1>Shades Array Admin</h1>
        <div>
            <a href="products.php">Manage Products</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <!-- Stats Cards -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <i class="fas fa-box"></i>
            <h3>Total Products</h3>
            <p class="stat-number"><?php echo $totalProducts; ?></p>
        </div>
        <div class="stat-card">
            <i class="fas fa-star"></i>
            <h3></h3>
            <p class="stat-number">Sis, this is just the beginning. Keep pushing,<br> 
                keep shining, your dreams are closer than you think</p> <!-- Dummy number -->
        </div>
        <div class="stat-card">
            <i class="fas fa-clock"></i>
            <h3>New This Month</h3>
            <p class="stat-number">4</p> <!-- Dummy number -->
        </div>
        <div class="stat-card">
            <i class="fas fa-credit-card"></i>
            <h3>Inventory Value</h3>
            <p class="stat-number"><?php echo $totalValueFormatted; ?></p>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="dashboard-links">
        <a href="products.php" class="btn-primary">Go to Products</a>
    </div>
</body>
</html>