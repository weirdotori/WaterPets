<?php
require_once "db.php"; // your PDO connection

// ---- Cards Data ---- //
$totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalProducts = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalReviews = $conn->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
$pendingInquiries = $conn->query("SELECT COUNT(*) FROM inquiry WHERE status='pending'")->fetchColumn();
$lowStockProducts = $conn->query("SELECT COUNT(*) FROM products WHERE stock < 5")->fetchColumn();

// Revenue (last 7 days)
$revenueStmt = $conn->query("SELECT SUM(totalPrice) 
                             FROM orders 
                             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$revenue7Days = $revenueStmt->fetchColumn() ?: 0;

// ---- Recent Orders ---- //
$recentOrders = $conn->query("
    SELECT orderID, firstName, lastName, totalPrice, orderStatus, created_at 
    FROM orders 
    ORDER BY created_at DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// ---- Recent Activity ---- //
$recentActivity = $conn->query("
    (SELECT username AS activity, created_at FROM users ORDER BY created_at DESC LIMIT 5)
    UNION
    (SELECT CONCAT('Order #', orderID, ' placed') AS activity, created_at FROM orders ORDER BY created_at DESC LIMIT 5)
    ORDER BY created_at DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// ---- User Growth (last 7 days) ---- //
$userGrowthStmt = $conn->query("
    SELECT DATE(created_at) as reg_date, COUNT(*) as total 
    FROM users 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
    GROUP BY DATE(created_at)
");
$userGrowth = $userGrowthStmt->fetchAll(PDO::FETCH_ASSOC);

// ---- Order Status Distribution ---- //
$orderStatusStmt = $conn->query("SELECT orderStatus, COUNT(*) as total FROM orders GROUP BY orderStatus");
$orderStatus = $orderStatusStmt->fetchAll(PDO::FETCH_ASSOC);

// ---- Inquiry Status Distribution ---- //
$inqStatusStmt = $conn->query("SELECT status, COUNT(*) as total FROM inquiry GROUP BY status");
$inqStatus = $inqStatusStmt->fetchAll(PDO::FETCH_ASSOC);

// ---- Revenue Overview ---- //
$revenueOverviewStmt = $conn->query("
    SELECT DATE(created_at) as rev_date, SUM(totalPrice) as total 
    FROM orders 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)
    GROUP BY DATE(created_at)
");
$revenueOverview = $revenueOverviewStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="dashboard-container">
  
  <!-- Cards -->
  <div class="cards">
    <div class="card">Users <span><?= $totalUsers ?></span></div>
    <div class="card">Products <span><?= $totalProducts ?></span></div>
    <div class="card">Orders <span><?= $totalOrders ?></span></div>
    <div class="card">Reviews <span><?= $totalReviews ?></span></div>
    <div class="card">Pending Inquiries <span><?= $pendingInquiries ?></span></div>
    <div class="card">Low Stock <span><?= $lowStockProducts ?></span></div>
    <div class="card">Revenue (7 days) <span>$<?= number_format($revenue7Days,2) ?></span></div>
  </div>

  <!-- Recent Orders -->
  <div class="section">
    <h2>Recent Orders</h2>
    <table>
      <tr><th>ID</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr>
      <?php foreach($recentOrders as $o): ?>
        <tr>
          <td><?= $o['orderID'] ?></td>
          <td><?= $o['firstName']." ".$o['lastName'] ?></td>
          <td>$<?= $o['totalPrice'] ?></td>
          <td><?= $o['orderStatus'] ?></td>
          <td><?= $o['created_at'] ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>

  <!-- Recent Activity -->
  <div class="section">
    <h2>Recent Activity</h2>
    <ul>
      <?php foreach($recentActivity as $a): ?>
        <li><?= $a['activity'] ?> (<?= $a['created_at'] ?>)</li>
      <?php endforeach; ?>
    </ul>
  </div>

  <!-- Charts -->
  <div class="charts">
  <div class="chart-wrapper"><canvas id="userGrowthChart"></canvas></div>
  <div class="chart-wrapper"><canvas id="orderStatusChart"></canvas></div>
  <div class="chart-wrapper"><canvas id="inquiryStatusChart"></canvas></div>
  <div class="chart-wrapper"><canvas id="revenueOverviewChart"></canvas></div>
</div>

</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// User Growth
new Chart(document.getElementById('userGrowthChart'), {
  type: 'line',
  data: {
    labels: <?= json_encode(array_column($userGrowth, 'reg_date')) ?>,
    datasets: [{label: 'Users', data: <?= json_encode(array_column($userGrowth, 'total')) ?>, borderColor: 'blue'}]
  }
});

// Order Status
new Chart(document.getElementById('orderStatusChart'), {
  type: 'pie',
  data: {
    labels: <?= json_encode(array_column($orderStatus, 'orderStatus')) ?>,
    datasets: [{data: <?= json_encode(array_column($orderStatus, 'total')) ?>, backgroundColor: ['#3498db','#2ecc71','#e74c3c','#f1c40f']}]
  }
});

// Inquiry Status
new Chart(document.getElementById('inquiryStatusChart'), {
  type: 'pie',
  data: {
    labels: <?= json_encode(array_column($inqStatus, 'status')) ?>,
    datasets: [{data: <?= json_encode(array_column($inqStatus, 'total')) ?>, backgroundColor: ['#9b59b6','#1abc9c','#e67e22']}]
  }
});

// Revenue Overview
new Chart(document.getElementById('revenueOverviewChart'), {
  type: 'line',
  data: {
    labels: <?= json_encode(array_column($revenueOverview, 'rev_date')) ?>,
    datasets: [{label: 'Revenue', data: <?= json_encode(array_column($revenueOverview, 'total')) ?>, borderColor: 'green'}]
  }
});
</script>

