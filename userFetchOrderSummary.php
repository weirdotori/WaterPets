<?php
session_start();
require_once "db.php";

if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    exit('Unauthorized');
}

$orderID = $_POST['orderID'] ?? 0;
$userID = $_SESSION['user']['userID'];

// Fetch order
$stmt = $conn->prepare("SELECT * FROM orders WHERE orderID = ? AND userID = ?");
$stmt->execute([$orderID, $userID]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$order) exit('Order not found.');

// Fetch items
$stmt = $conn->prepare("
    SELECT od.*, p.pName
    FROM order_details od
    JOIN products p ON od.productID = p.productID
    WHERE od.orderID = ?
");
$stmt->execute([$orderID]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch shipping & payment
$stmt = $conn->prepare("SELECT * FROM shipping WHERE orderID = ?");
$stmt->execute([$orderID]);
$shipping = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM payment WHERE orderID = ?");
$stmt->execute([$orderID]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

// Calculate subtotal
$subtotal = 0;
foreach ($items as $item) {
    $subtotal += $item['unitPrice'] * $item['orderQty'];
}
?>

<div class="order-summary-card">
    <h3>Order Summary #<?= $order['orderID'] ?></h3>
    <p><strong>Date:</strong> <?= date('d M Y', strtotime($order['created_at'])) ?></p>
    <p><strong>Status:</strong> <?= ucfirst($order['orderStatus']) ?></p>

    <h5>Customer Information</h5>
    <p>
        <?= $order['firstName'] ?> <?= $order['lastName'] ?><br>
        <?= $order['streetAddress'] ?>, <?= $order['city'] ?>, <?= $order['state'] ?>, <?= $order['country'] ?><br>
        Phone: <?= $order['phone'] ?><br>
        Email: <?= $order['email'] ?>
    </p>

    <h5>Products</h5>
    <div class="order-table-container">
        <table class="order-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['pName']) ?></td>
                    <td><?= $item['orderQty'] ?></td>
                    <td>$<?= number_format($item['unitPrice'], 2) ?></td>
                    <td>$<?= number_format($item['unitPrice'] * $item['orderQty'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <p class="order-summary-totals">
        <strong>Subtotal:</strong> $<?= number_format($subtotal,2) ?><br>
        <strong>Shipping:</strong> $<?= number_format($shipping['shippingFees'] ?? 0,2) ?><br>
        <?php if($order['couponCode']): ?>
            <strong>Coupon Discount:</strong> -$<?= number_format($subtotal*0.1,2) ?><br>
        <?php endif; ?>
        <strong>Total:</strong> $<?= number_format($order['totalPrice'],2) ?>
    </p>

    <h5>Payment Information</h5>
    <p>
        Method: <?= $payment['paymentMethod'] ?? 'N/A' ?><br>
        Status: <?= $payment['paymentStatus'] ?? 'N/A' ?>
    </p>

    <div class="order-summary-actions">
        <button onclick="window.print()" class="btn-primary">Print Invoice</button>
        <button onclick="goBackToOrders()" class="btn-secondary">Back</button>
    </div>
</div>

