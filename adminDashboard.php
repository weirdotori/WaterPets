<?php
session_start();
require_once "db.php";

// Security: Only allow logged-in admins
if (!isset($_SESSION['adminLogin']) || $_SESSION['role'] !== 'admin') {
    header("Location: adminLogin.php");
    exit();
}

// Query statistics
$totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalProducts = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalReviews = $conn->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/admin-style.css">
</head>
<body>

<div class="admin-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>WaterPets</h2>
        <ul>
            <li><a href="admin_dashboard.php" class="active">Dashboard</a></li>
            <li><a href="admin_account.php">Account</a></li>
            <li><a href="admin_reports.php">Reports</a></li>
            <li><a href="manage_orders.php">Orders</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="manage_products.php">Manage Products</a></li>
            <li><a href="view_messages.php">Messages</a></li>
            <li><a href="admin_reviews.php">Reviews</a></li>
            <li><a href="logout.php" class="logout">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="dashboard-header">
            <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> 👋</h2>
            <p class="text-muted">Admin Dashboard Overview</p>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card stat-card bg-primary text-white p-3">
                    <h5>Total Users</h5>
                    <h3><?= $totalUsers ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-success text-white p-3">
                    <h5>Total Products</h5>
                    <h3><?= $totalProducts ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-warning text-dark p-3">
                    <h5>Total Orders</h5>
                    <h3><?= $totalOrders ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-danger text-white p-3">
                    <h5>Total Reviews</h5>
                    <h3><?= $totalReviews ?></h3>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="mt-5">
            <h4>Quick Links</h4>
            <div class="d-flex flex-wrap gap-3">
                <a href="manage_users.php" class="btn btn-outline-primary">Manage Users</a>
                <a href="manage_products.php" class="btn btn-outline-success">Manage Products</a>
                <a href="manage_orders.php" class="btn btn-outline-warning">Manage Orders</a>
                <a href="view_messages.php" class="btn btn-outline-danger">View Messages</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
