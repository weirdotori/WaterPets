<?php
require_once "db.php";

if (!isset($_GET['orderID'])) {
    echo "<p>No order selected.</p>";
    exit;
}

$orderID = (int)$_GET['orderID'];

// Fetch order info
$stmt = $conn->prepare("
    SELECT orderID, userID, firstName, lastName, email, phone, country, streetAddress, city, state,
           deliveryType, totalPrice, orderStatus, created_at, updated_at
    FROM orders
    WHERE orderID = ?
");
$stmt->execute([$orderID]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<p>Order not found.</p>";
    exit;
}

// Fetch order items
$itemsStmt = $conn->prepare("
    SELECT od.productID, p.pName, od.orderQty, od.unitPrice
    FROM order_details od
    JOIN products p ON od.productID = p.productID
    WHERE od.orderID = ?
");
$itemsStmt->execute([$orderID]);
$orderItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch payment info
$payStmt = $conn->prepare("SELECT paymentMethod, paymentStatus FROM payment WHERE orderID = ?");
$payStmt->execute([$orderID]);
$payment = $payStmt->fetch(PDO::FETCH_ASSOC);
?>

<h2>Order #<?= $order['orderID'] ?> Details</h2>

<!-- Back Button -->
<button class="back-btn" onclick="window.location.href='?page=manage_orders'">← Back to Orders</button>

<div class="invoice-container">
    <section class="invoice-section">
        <h3>Customer Info</h3>
        <p><strong>Name:</strong> <?= htmlspecialchars($order['firstName'] . ' ' . $order['lastName']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($order['streetAddress'] . ', ' . $order['city'] . ', ' . $order['state'] . ', ' . $order['country']) ?></p>
    </section>

    <section class="invoice-section">
        <h3>Order Info</h3>
        <p><strong>Order ID:</strong> <?= $order['orderID'] ?></p>
        <p><strong>User ID:</strong> <?= $order['userID'] ?></p>
        <p><strong>Delivery Type:</strong> <?= htmlspecialchars($order['deliveryType']) ?></p>
        <p><strong>Order Date:</strong> <?= $order['created_at'] ?></p>
        <p><strong>Last Updated:</strong> <?= $order['updated_at'] ?></p>
        <form method="POST" class="status-form">
            <input type="hidden" name="orderID" value="<?= $order['orderID'] ?>">
            <label><strong>Status:</strong></label>
            <select name="orderStatus" onchange="this.form.submit()">
                <?php
                $statuses = ['Pending','Processing','Completed'];
                foreach ($statuses as $status) {
                    $selected = ($order['orderStatus'] === $status) ? 'selected' : '';
                    echo "<option value='$status' $selected>$status</option>";
                }
                ?>
            </select>
        </form>
    </section>

    <section class="invoice-section">
        <h3>Payment Info</h3>
        <p><strong>Method:</strong> <?= htmlspecialchars($payment['paymentMethod'] ?? 'N/A') ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($payment['paymentStatus'] ?? 'N/A') ?></p>
    </section>

    <section class="invoice-section">
        <h3>Items</h3>
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price ($)</th>
                    <th>Subtotal ($)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0; 
                foreach ($orderItems as $index => $item):
                    $subtotal = $item['orderQty'] * $item['unitPrice'];
                    $total += $subtotal;
                ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($item['pName']) ?></td>
                    <td><?= $item['orderQty'] ?></td>
                    <td><?= number_format($item['unitPrice'],2) ?></td>
                    <td><?= number_format($subtotal,2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right"><strong>Total:</strong></td>
                    <td><strong>$<?= number_format($total,2) ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </section>
</div>

<script>
// Status update handling (submit to same page)
document.querySelectorAll('.status-form select').forEach(select => {
    select.addEventListener('change', function() {
        this.form.submit();
    });
});
</script>
