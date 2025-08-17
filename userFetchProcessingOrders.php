<?php
session_start();
require_once "db.php";

if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    exit('Unauthorized');
}

// Fetch only processing orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE userID = ? AND orderStatus = 'Processing' ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user']['userID']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (count($orders) === 0): ?>
    <p>No processing orders at the moment.</p>
<?php else: ?>
    <div class="orders-list">
        <?php foreach ($orders as $order): ?>
            <div class="order-card" id="order-<?= $order['orderID'] ?>">
                <div class="order-card-content">
                    <div class="order-details">
                        <strong>Order #<?= $order['orderID'] ?></strong><br>
                        Total: $<?= number_format($order['totalPrice'], 2) ?><br>
                        Status: <span class="order-status"><?= ucfirst($order['orderStatus']) ?></span><br>
                        Date: <?= date('d M Y', strtotime($order['created_at'])) ?>
                    </div>

                    <div class="order-actions">
                        <button class="cancel-order-btn" data-order-id="<?= $order['orderID'] ?>">Cancel Order</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

