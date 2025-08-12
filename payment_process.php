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

    // Insert order
    $stmtOrder = $conn->prepare("INSERT INTO orders 
      (deliveryType, userID, totalPrice, orderStatus, created_at, firstName, lastName, country, streetAddress, city, state, phone, email, couponCode)
      VALUES (?, ?, ?, 'Completed', NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmtOrder->execute([
        $orderData['deliveryType'],
        $orderData['userID'],
        $orderData['totalPrice'],
        $orderData['firstName'],
        $orderData['lastName'],
        $orderData['country'],
        $orderData['streetAddress'],
        $orderData['city'],
        $orderData['state'],
        $orderData['phone'],
        $orderData['email'],
        $orderData['couponCode'],
    ]);

    $orderID = $conn->lastInsertId();

    // Insert order_details
    $stmtDetails = $conn->prepare("INSERT INTO order_details (orderID, productID, orderQty, unitPrice) VALUES (?, ?, ?, ?)");

    foreach ($orderData['cart'] as $productID => $item) {
        $pid = $item['productID'] ?? $productID;
        $stmtDetails->execute([$orderID, $pid, $item['quantity'], $item['price']]);
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

    // Redirect to success page
    header("Location: order_success.php?orderID=" . $orderID);
    exit();

} catch (Exception $e) {
    $conn->rollBack();
    die("Error processing payment: " . $e->getMessage());
}
