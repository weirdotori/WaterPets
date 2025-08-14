<?php
session_start();
require_once "db.php";

if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    exit('Unauthorized');
}

// Fetch orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE userID = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user']['userID']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (count($orders) === 0): ?>
    <p>You haven’t placed any orders yet.</p>
<?php else: ?>
    <div class="orders-list">
        <?php foreach ($orders as $order): ?>
            <div class="order-card mb-3 p-3 border rounded">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Order #<?= $order['orderID'] ?></strong><br>
                        Total: $<?= number_format($order['totalPrice'], 2) ?><br>
                        Status: <?= ucfirst($order['orderStatus']) ?><br>
                        Date: <?= date('d M Y', strtotime($order['created_at'])) ?>
                    </div>
                    <button class="btn btn-sm btn-outline-primary view-order-btn" data-order-id="<?= $order['orderID'] ?>">View Summary</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
