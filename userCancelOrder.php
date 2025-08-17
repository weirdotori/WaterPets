<?php
session_start();
require_once "db.php";

header('Content-Type: application/json');

if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['orderID'])) {
    $orderID = $_POST['orderID'];

    // Verify order belongs to the logged-in user and is processing
    $stmt = $conn->prepare("SELECT orderStatus FROM orders WHERE orderID = ? AND userID = ?");
    $stmt->execute([$orderID, $_SESSION['user']['userID']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Order not found.']);
        exit;
    }

    if ($order['orderStatus'] !== 'Processing') {
        echo json_encode(['success' => false, 'message' => 'Only processing orders can be cancelled.']);
        exit;
    }

    // 1. Restore stock for all products in this order
    $detailsStmt = $conn->prepare("SELECT productID, orderQty FROM order_details WHERE orderID = ?");
    $detailsStmt->execute([$orderID]);
    $orderItems = $detailsStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($orderItems as $item) {
        $updateStock = $conn->prepare("UPDATE products SET stock = stock + ? WHERE productID = ?");
        $updateStock->execute([$item['orderQty'], $item['productID']]);
    }

    // 2. Update order status to Cancelled
    $updateOrder = $conn->prepare("UPDATE orders SET orderStatus = 'Cancelled', updated_at = NOW() WHERE orderID = ?");
    $updateOrder->execute([$orderID]);

    echo json_encode(['success' => true, 'message' => 'Order has been cancelled and stock restored.']);
}
?>
