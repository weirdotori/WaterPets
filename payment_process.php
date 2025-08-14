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

// Begin transaction to insert data
try {
    $conn->beginTransaction();

    $orderID = $orderData['orderID'] ?? null;
    if (!$orderID) {
        throw new Exception("No pending order ID found.");
    }

    // Set order & payment status based on payment method
    if ($paymentMethod === 'cod') {
        $orderStatus = 'Processing'; // Not yet shipped/finished
        $paymentStatus = 'Pending';  // Waiting for payment on delivery
    } else {
        $orderStatus = 'Completed';
        $paymentStatus = 'Paid';
    }


    // Update order
    $stmtOrder = $conn->prepare("
        UPDATE orders SET 
            orderStatus = :orderStatus,
            totalPrice = :totalPrice,
            updated_at = NOW()
        WHERE orderID = :orderID
    ");
    $stmtOrder->execute([
        ':orderStatus' => $orderStatus,
        ':totalPrice'  => $orderData['totalPrice'],
        ':orderID'     => $orderID
    ]);

    // Insert order details
    $stmtDetails = $conn->prepare("INSERT INTO order_details (orderID, productID, orderQty, unitPrice) VALUES (?, ?, ?, ?)");
    foreach ($orderData['cart'] as $productID => $item) {
        $pid = $item['productID'] ?? $productID;
        $stmtDetails->execute([$orderID, $pid, $item['quantity'], $item['price']]);
    }

    // Reduce stock
    $stmtUpdateStock = $conn->prepare("UPDATE products SET stock = stock - :qty WHERE productID = :pid");
    foreach ($orderData['cart'] as $productID => $item) {
        $pid = $item['productID'] ?? $productID;
        $stmtUpdateStock->execute([
            ':qty' => $item['quantity'],
            ':pid' => $pid
        ]);
    }

    // Insert payment record
    $stmtPayment = $conn->prepare("INSERT INTO payment (paymentMethod, paymentStatus, orderID) VALUES (?, ?, ?)");
    $stmtPayment->execute([$paymentMethod, $paymentStatus, $orderID]);

    // Insert shipping if needed
    if ($orderData['deliveryType'] === 'shipping') {
        $shippingFee = $orderData['shippingFee'];
        $shippingSpeed = $orderData['shippingSpeed'] ?? 'standard';
        $stmtShipping = $conn->prepare("INSERT INTO shipping (shippingFees, orderID, shippingSpeed) VALUES (?, ?, ?)");
        $stmtShipping->execute([$shippingFee, $orderID, $shippingSpeed]);
    }

    // After committing the order
    if ($orderStatus === 'Completed') {
        // 1. Count completed orders for this user
        $stmtCount = $conn->prepare("SELECT COUNT(*) FROM orders WHERE userID = ? AND orderStatus = 'Completed'");
        $stmtCount->execute([$_SESSION['user']['userID']]);
        $completedOrders = (int)$stmtCount->fetchColumn();

        // 2. Generate coupon every 3 orders
        if ($completedOrders % 3 === 0) {
            // Generate random coupon code
            $randomCode = strtoupper(substr(md5(uniqid()), 0, 8));
            $discount = 10; // 10%

            // Insert coupon into 'coupons' table
            $stmtCoupon = $conn->prepare("
                    INSERT INTO coupons (userID, code, discount, status, created_at) 
                    VALUES (?, ?, ?, 'unused', NOW())");
            $stmtCoupon->execute([$_SESSION['user']['userID'], $randomCode, $discount]);
        }
    }

    // Clear cart items from DB
    $stmtDeleteCart = $conn->prepare("DELETE FROM cart_items WHERE userID = ?");
    $stmtDeleteCart->execute([$_SESSION['user']['userID']]);


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
