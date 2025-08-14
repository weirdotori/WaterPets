<?php
require_once "db.php";

// Handle Delete Order
if (isset($_GET['delete'])) {
    $orderID = (int)$_GET['delete'];
    $conn->prepare("DELETE FROM order_details WHERE orderID = ?")->execute([$orderID]);
    $conn->prepare("DELETE FROM payment WHERE orderID = ?")->execute([$orderID]);
    $conn->prepare("DELETE FROM orders WHERE orderID = ?")->execute([$orderID]);
    echo "<script>alert('Order deleted successfully'); window.location.href='?page=manage_orders';</script>";
    exit;
}

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['orderID'], $_POST['orderStatus'])) {
    $stmt = $conn->prepare("UPDATE orders SET orderStatus = ?, updated_at = NOW() WHERE orderID = ?");
    $stmt->execute([$_POST['orderStatus'], $_POST['orderID']]);
    echo "<script>window.location.href='?page=manage_orders';</script>";
    exit;
}

// Fetch orders with item count
$stmt = $conn->prepare("
    SELECT o.orderID, o.userID, o.firstName, o.email, o.city, o.totalPrice, o.orderStatus, o.created_at,
           (SELECT COUNT(*) FROM order_details od WHERE od.orderID = o.orderID) AS itemCount
    FROM orders o
    ORDER BY o.created_at DESC
");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Manage Orders</h2>
<p>View, update, or delete orders below:</p>

<table class="orders-table">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>User ID</th>
            <th>Customer Name</th>
            <th>Email</th>
            <th>City</th>
            <th>Items</th>
            <th>Total ($)</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= htmlspecialchars($order['orderID']) ?></td>
            <td><?= htmlspecialchars($order['userID']) ?></td>
            <td><?= htmlspecialchars($order['firstName']) ?></td>
            <td><?= htmlspecialchars($order['email']) ?></td>
            <td><?= htmlspecialchars($order['city']) ?></td>
            <td><?= $order['itemCount'] ?></td>
            <td><?= number_format($order['totalPrice'], 2) ?></td>
            <td>
                <form method="POST" class="status-form">
                    <input type="hidden" name="orderID" value="<?= $order['orderID'] ?>">
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
            </td>
            <td><?= htmlspecialchars($order['created_at']) ?></td>
            <td>
                <div class="action-dropdown">
                    <button class="action-btn">Actions ▼</button>
                    <div class="dropdown-content">
                        <a href="?page=view_order&orderID=<?= $order['orderID'] ?>">View Details</a>
                        <a href="?page=manage_orders&delete=<?= $order['orderID'] ?>" onclick="return confirm('Delete this order?')">Delete</a>
                    </div>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script>
// Dropdown toggle
document.querySelectorAll('.action-btn').forEach(btn => {
    btn.addEventListener('click', function(e){
        e.stopPropagation();
        const dropdown = this.nextElementSibling;
        document.querySelectorAll('.dropdown-content').forEach(d => { if(d !== dropdown) d.classList.remove('show'); });
        dropdown.classList.toggle('show');
    });
});
document.addEventListener('click', () => {
    document.querySelectorAll('.dropdown-content').forEach(d => d.classList.remove('show'));
});
</script>
