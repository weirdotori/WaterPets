<?php
session_start();
require 'db.php';

if (empty($_SESSION['user']) || empty($_SESSION['pending_order'])) {
    header("Location: cart.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: payment.php");
    exit();
}

// Retrieve order data from session
$orderData = $_SESSION['pending_order'];

$paymentMethod = $_POST['payment_method'] ?? '';

// Validate payment method and details (as you already do)...

// Begin transaction to insert data
try {
    $conn->beginTransaction();

    $orderID = $orderData['orderID'] ?? null;
    if (!$orderID) {
        throw new Exception("No pending order ID found.");
    }

    // Update orderStatus to Completed for existing order
    $stmtOrder = $conn->prepare("
        UPDATE orders SET 
            orderStatus = 'Completed',
            totalPrice = :totalPrice,
            updated_at = NOW()
        WHERE orderID = :orderID
    ");
    $stmtOrder->execute([
        ':totalPrice' => $orderData['totalPrice'],
        ':orderID' => $orderID
    ]);

    // Insert order details
    $stmtDetails = $conn->prepare("INSERT INTO order_details (orderID, productID, orderQty, unitPrice) VALUES (?, ?, ?, ?)");
    foreach ($orderData['cart'] as $productID => $item) {
        $pid = $item['productID'] ?? $productID;
        $stmtDetails->execute([$orderID, $pid, $item['quantity'], $item['price']]);
    }

    // Reduce stock of products based on order quantities
    $stmtUpdateStock = $conn->prepare("UPDATE products SET stock = stock - :qty WHERE productID = :pid");
    foreach ($orderData['cart'] as $productID => $item) {
        $pid = $item['productID'] ?? $productID;
        $quantity = $item['quantity'];
        $stmtUpdateStock->execute([
            ':qty' => $quantity,
            ':pid' => $pid,
        ]);
    }


    // Insert payment
    $paymentStatus = 'Paid';
    $stmtPayment = $conn->prepare("INSERT INTO payment (paymentMethod, paymentStatus, orderID) VALUES (?, ?, ?)");
    $stmtPayment->execute([$paymentMethod, $paymentStatus, $orderID]);

    // Insert shipping if needed
    if ($orderData['deliveryType'] === 'shipping') {
        $shippingFee = $orderData['shippingFee'];
        $shippingSpeed = $orderData['shippingSpeed'] ?? 'standard';

        $stmtShipping = $conn->prepare("INSERT INTO shipping (shippingFees, orderID, shippingSpeed) VALUES (?, ?, ?)");
        $stmtShipping->execute([$shippingFee, $orderID, $shippingSpeed]);
    }

    $conn->commit();

    // Clear cart and pending order data
    unset($_SESSION['cart']);
    unset($_SESSION['pending_order']);

    header("Location: order_success.php?orderID=" . $orderID);
    exit();
} catch (Exception $e) {
    $conn->rollBack();
    die("Error processing payment: " . $e->getMessage());
}
