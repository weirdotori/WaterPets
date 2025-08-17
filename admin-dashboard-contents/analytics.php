<?php
require_once "db.php"; // PDO connection


// ---- Cards ---- //
// Total Revenue
$totalRevenue = $conn->query("SELECT SUM(totalPrice) FROM orders")->fetchColumn() ?: 0;

// Conversion Rate: orders / total users
$totalOrders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$conversionRate = $totalUsers > 0 ? round(($totalOrders / $totalUsers) * 100, 2) : 0;

// Average Order Value
$avgOrderValue = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

// User active rate: let's assume users logged in in last 7 days
$activeUsers = $conn->query("SELECT COUNT(*) FROM users WHERE last_login >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn() ?: 0;
$userActiveRate = $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 2) : 0;

// ---- Charts ---- //

// Sales over time (daily last 30 days)
$salesOverTimeStmt = $conn->query("
    SELECT DATE(created_at) as day, SUM(totalPrice) as total 
    FROM orders 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(created_at)
");
$salesOverTime = $salesOverTimeStmt->fetchAll(PDO::FETCH_ASSOC);

// Revenue Distribution by product category
$revenueByCategoryStmt = $conn->query("
    SELECT c.cName, SUM(od.orderQty * od.unitPrice) as revenue
    FROM order_details od
    JOIN products p ON od.productID = p.productID
    JOIN categories c ON p.categoryID = c.categoryID
    GROUP BY c.cName
");
$revenueByCategory = $revenueByCategoryStmt->fetchAll(PDO::FETCH_ASSOC);

// Orders vs Revenue (Area Chart daily last 30 days)
$ordersRevenueStmt = $conn->query("
    SELECT DATE(created_at) as day, COUNT(*) as orders_count, SUM(totalPrice) as revenue
    FROM orders
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(created_at)
");
$ordersRevenue = $ordersRevenueStmt->fetchAll(PDO::FETCH_ASSOC);

// Top selling products (by quantity)
$topProductsStmt = $conn->query("
    SELECT p.pName, SUM(od.orderQty) as totalSold
    FROM order_details od
    JOIN products p ON od.productID = p.productID
    GROUP BY p.pName
    ORDER BY totalSold DESC
    LIMIT 5
");
$topProducts = $topProductsStmt->fetchAll(PDO::FETCH_ASSOC);

// Order Status Pie Chart
$orderStatusStmt = $conn->query("
    SELECT orderStatus, COUNT(*) as total 
    FROM orders 
    GROUP BY orderStatus
");
$orderStatus = $orderStatusStmt->fetchAll(PDO::FETCH_ASSOC);

// Customer Growth (last 30 days)
$customerGrowthStmt = $conn->query("
    SELECT DATE(created_at) as day, COUNT(*) as total 
    FROM users 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(created_at)
");
$customerGrowth = $customerGrowthStmt->fetchAll(PDO::FETCH_ASSOC);

// Review Ratings Distribution
$reviewRatingsStmt = $conn->query("
    SELECT rating, COUNT(*) as total 
    FROM reviews 
    GROUP BY rating
");
$reviewRatings = $reviewRatingsStmt->fetchAll(PDO::FETCH_ASSOC);

// Inquiry Status Donut Chart
$inquiryStatusStmt = $conn->query("
    SELECT status, COUNT(*) as total 
    FROM inquiry
    GROUP BY status
");
$inquiryStatus = $inquiryStatusStmt->fetchAll(PDO::FETCH_ASSOC);

// Product Stock Levels by category
$productStockStmt = $conn->query("
    SELECT c.cName, SUM(p.stock) as totalStock
    FROM products p
    JOIN categories c ON p.categoryID = c.categoryID
    GROUP BY c.cName
");
$productStock = $productStockStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="analytics-container">

    <!-- Cards -->
    <div class="analytics-cards">
        <div class="analytics-card">Total Revenue <span>$<?= number_format($totalRevenue, 2) ?></span></div>
        <div class="analytics-card">Conversion Rate <span><?= $conversionRate ?>%</span></div>
        <div class="analytics-card">Average Order Value <span>$<?= number_format($avgOrderValue, 2) ?></span></div>
        <div class="analytics-card">User Active Rate <span><?= $userActiveRate ?>%</span></div>
    </div>

    <div class="analytics-section-title"> Analytical Charts</div>

    <!-- Charts -->
    <div class="analytics-charts">

        <div class="analytics-chart-wrapper">
            <h4 class="chart-title">Sales Over Time (Last 30 Days)</h4>
            <canvas id="salesOverTimeChart"></canvas>
        </div>

        <div class="analytics-chart-wrapper">
            <h4 class="chart-title">Revenue Distribution by Product Category</h4>
            <canvas id="revenueDistributionChart"></canvas>
        </div>

        <div class="analytics-chart-wrapper">
            <h4 class="chart-title">Orders vs Revenue (Last 30 Days)</h4>
            <canvas id="ordersRevenueChart"></canvas>
        </div>

        <div class="analytics-chart-wrapper">
            <h4 class="chart-title">Top Selling Products</h4>
            <canvas id="topProductsChart"></canvas>
        </div>

        <div class="analytics-chart-wrapper">
            <h4 class="chart-title">Order Status Distribution</h4>
            <canvas id="orderStatusChart"></canvas>
        </div>

        <div class="analytics-chart-wrapper">
            <h4 class="chart-title">Customer Growth (Last 30 Days)</h4>
            <canvas id="customerGrowthChart"></canvas>
        </div>

        <div class="analytics-chart-wrapper">
            <h4 class="chart-title">Review Ratings Distribution</h4>
            <canvas id="reviewRatingsChart"></canvas>
        </div>

        <div class="analytics-chart-wrapper">
            <h4 class="chart-title">Inquiry Status</h4>
            <canvas id="inquiryStatusChart"></canvas>
        </div>

        <div class="analytics-chart-wrapper">
            <h4 class="chart-title">Product Stock Levels by Category</h4>
            <canvas id="productStockChart"></canvas>
        </div>

    </div>

</div>


<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Sales over time
    new Chart(document.getElementById('salesOverTimeChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($salesOverTime, 'day')) ?>,
            datasets: [{
                label: 'Sales',
                data: <?= json_encode(array_column($salesOverTime, 'total')) ?>,
                borderColor: 'blue',
                backgroundColor: 'rgba(0,123,255,0.1)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Revenue Distribution
    new Chart(document.getElementById('revenueDistributionChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($revenueByCategory, 'cName')) ?>,
            datasets: [{
                label: 'Revenue',
                data: <?= json_encode(array_column($revenueByCategory, 'revenue')) ?>,
                backgroundColor: '#2ecc71'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Orders vs Revenue (Area)
    new Chart(document.getElementById('ordersRevenueChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($ordersRevenue, 'day')) ?>,
            datasets: [{
                    label: 'Orders',
                    data: <?= json_encode(array_column($ordersRevenue, 'orders_count')) ?>,
                    borderColor: 'orange',
                    backgroundColor: 'rgba(255,165,0,0.1)',
                    yAxisID: 'y'
                },
                {
                    label: 'Revenue',
                    data: <?= json_encode(array_column($ordersRevenue, 'revenue')) ?>,
                    borderColor: 'green',
                    backgroundColor: 'rgba(0,128,0,0.1)',
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    position: 'left'
                },
                y1: {
                    type: 'linear',
                    position: 'right'
                }
            }
        }
    });

    // Top Products
    new Chart(document.getElementById('topProductsChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($topProducts, 'pName')) ?>,
            datasets: [{
                label: 'Quantity Sold',
                data: <?= json_encode(array_column($topProducts, 'totalSold')) ?>,
                backgroundColor: '#3498db'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Order Status Pie
    new Chart(document.getElementById('orderStatusChart'), {
        type: 'pie',
        data: {
            labels: <?= json_encode(array_column($orderStatus, 'orderStatus')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($orderStatus, 'total')) ?>,
                backgroundColor: ['#3498db', '#2ecc71', '#e74c3c', '#f1c40f']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Customer Growth
    new Chart(document.getElementById('customerGrowthChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($customerGrowth, 'day')) ?>,
            datasets: [{
                label: 'New Customers',
                data: <?= json_encode(array_column($customerGrowth, 'total')) ?>,
                borderColor: 'purple',
                backgroundColor: 'rgba(128,0,128,0.1)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Review Ratings
    new Chart(document.getElementById('reviewRatingsChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($reviewRatings, 'rating')) ?>,
            datasets: [{
                label: 'Reviews Count',
                data: <?= json_encode(array_column($reviewRatings, 'total')) ?>,
                backgroundColor: '#f1c40f'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Inquiry Status Donut
    new Chart(document.getElementById('inquiryStatusChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($inquiryStatus, 'status')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($inquiryStatus, 'total')) ?>,
                backgroundColor: ['#9b59b6', '#1abc9c']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Product Stock Levels
    new Chart(document.getElementById('productStockChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($productStock, 'cName')) ?>,
            datasets: [{
                label: 'Stock',
                data: <?= json_encode(array_column($productStock, 'totalStock')) ?>,
                backgroundColor: '#e67e22'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>