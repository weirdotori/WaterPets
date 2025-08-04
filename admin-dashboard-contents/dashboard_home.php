<?php
require_once "db.php"; 


//  STATS CARDS DATA

$totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalProducts = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalReviews = $conn->query("SELECT COUNT(*) FROM reviews")->fetchColumn();


//  ORDERS PER DAY (LAST 7 DAYS)

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


//  PRODUCTS PER CATEGORY

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

<div class="container py-4">
    <h2 class="mb-4">Dashboard Overview</h2>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text fs-2"><?= $totalUsers ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Products</h5>
                    <p class="card-text fs-2"><?= $totalProducts ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Orders</h5>
                    <p class="card-text fs-2"><?= $totalOrders ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Reviews</h5>
                    <p class="card-text fs-2"><?= $totalReviews ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">Orders in Last 7 Days</div>
                <div class="card-body">
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">Products by Category</div>
                <div class="card-body">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
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
            scales: {
                y: { beginAtZero: true, precision: 0 }
            }
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
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'right' }
            }
        }
    });
</script>
