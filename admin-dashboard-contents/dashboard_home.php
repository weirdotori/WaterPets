<?php
require_once "db.php"; 

// STATS CARDS DATA
$totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalProducts = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalReviews = $conn->query("SELECT COUNT(*) FROM reviews")->fetchColumn();

// ORDERS PER DAY (LAST 7 DAYS)
$ordersPerDayStmt = $conn->prepare("
    SELECT DATE(created_at) as day, COUNT(*) as count
    FROM orders
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY day
    ORDER BY day ASC
");
$ordersPerDayStmt->execute();
$ordersData = $ordersPerDayStmt->fetchAll(PDO::FETCH_ASSOC);

$ordersDays = [];
$ordersCounts = [];
foreach ($ordersData as $row) {
    $ordersDays[] = $row['day'];
    $ordersCounts[] = (int)$row['count'];
}

// PRODUCTS PER CATEGORY
$productsCategoryStmt = $conn->prepare("
    SELECT cName, COUNT(p.productID) as count
    FROM categories c
    LEFT JOIN products p ON c.categoryID = p.categoryID
    GROUP BY c.categoryID
");
$productsCategoryStmt->execute();
$categoriesData = $productsCategoryStmt->fetchAll(PDO::FETCH_ASSOC);

$categoryNames = [];
$categoryCounts = [];
foreach ($categoriesData as $row) {
    $categoryNames[] = $row['cName'];
    $categoryCounts[] = (int)$row['count'];
}
?>

<div class="dashboard-container">
    <h2 class="section-title">Dashboard Overview</h2>

    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="card bg-primary">
            <div class="card-content">
                <h5>Total Users</h5>
                <p class="stat-number"><?= $totalUsers ?></p>
            </div>
        </div>
        <div class="card bg-success">
            <div class="card-content">
                <h5>Total Products</h5>
                <p class="stat-number"><?= $totalProducts ?></p>
            </div>
        </div>
        <div class="card bg-warning">
            <div class="card-content">
                <h5>Total Orders</h5>
                <p class="stat-number"><?= $totalOrders ?></p>
            </div>
        </div>
        <div class="card bg-danger">
            <div class="card-content">
                <h5>Total Reviews</h5>
                <p class="stat-number"><?= $totalReviews ?></p>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="charts-container">
        <div class="chart-card">
            <h4>Orders in Last 7 Days</h4>
            <canvas id="ordersChart"></canvas>
        </div>

        <div class="chart-card">
            <h4>Products by Category</h4>
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Orders Chart
    const ordersCtx = document.getElementById('ordersChart').getContext('2d');
    const ordersChart = new Chart(ordersCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($ordersDays) ?>,
            datasets: [{
                label: 'Orders',
                data: <?= json_encode($ordersCounts) ?>,
                backgroundColor: 'rgba(13, 110, 253, 0.2)',
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            scales: { y: { beginAtZero: true, precision: 0 } }
        }
    });

    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryChart = new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($categoryNames) ?>,
            datasets: [{
                label: 'Products',
                data: <?= json_encode($categoryCounts) ?>,
                backgroundColor: [
                    '#0d6efd', '#198754', '#ffc107', '#dc3545',
                    '#6610f2', '#fd7e14', '#6c757d', '#20c997'
                ]
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'right' } } }
    });
</script>
