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

// Validate payment inputs based on selected method
if ($paymentMethod === 'paypal') {
    $paypalEmail = $_POST['paypal_email'] ?? '';
    if (empty($paypalEmail)) {
        die("Please enter your PayPal email.");
    }
} elseif ($paymentMethod === 'visa' || $paymentMethod === 'credit_card') {
    $cardNumber = $_POST['card_number'] ?? '';
    $cardExpiry = $_POST['card_expiry'] ?? '';
    $cardCvc = $_POST['card_cvc'] ?? '';

    if (empty($cardNumber) || empty($cardExpiry) || empty($cardCvc)) {
        die("Please enter your card details.");
    }

    // Optional: you can also add simple format checks here
    if (!preg_match('/^\d{16}$/', str_replace(' ', '', $cardNumber))) {
        die("Card number is invalid.");
    }
    if (!preg_match('/^\d{2}\/\d{2}$/', $cardExpiry)) {
        die("Expiry date is invalid.");
    }
    if (!preg_match('/^\d{3,4}$/', $cardCvc)) {
        die("CVC is invalid.");
    }
} elseif ($paymentMethod === 'cod') {
    // No extra validation needed for Cash on Delivery
} else {
    die("Please select a payment method.");
}


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

    // After placing the order
    if ($orderStatus === 'Completed') {
        // Count completed orders for this user
        $stmtCount = $conn->prepare("SELECT COUNT(*) FROM orders WHERE userID = ? AND orderStatus = 'Completed'");
        $stmtCount->execute([$_SESSION['user']['userID']]);
        $completedOrders = (int)$stmtCount->fetchColumn();

        // Generate coupon every 3 orders
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

    // Instead of redirect, show popup
    echo "
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const popup = document.createElement('div');
            popup.style.position = 'fixed';
            popup.style.top = '0';
            popup.style.left = '0';
            popup.style.width = '100%';
            popup.style.height = '100%';
            popup.style.background = 'rgba(0,0,0,0.5)';
            popup.style.display = 'flex';
            popup.style.alignItems = 'center';
            popup.style.justifyContent = 'center';
            popup.style.zIndex = '9999';

            popup.innerHTML = `
                <div style='background:#fff; padding:30px; border-radius:12px; text-align:center; max-width:400px;'>
                    <h2>Thank you for shopping with us!</h2>
                    <p>Your order has been placed successfully.</p>
                    <button id='closePopup' style='margin-top:20px; padding:10px 20px; border:none; background:#007bff; color:#fff; border-radius:8px; cursor:pointer;'>Close</button>
                </div>
            `;

            document.body.appendChild(popup);

            document.getElementById('closePopup').addEventListener('click', function() {
                window.location.href = 'home.php'; // Redirect to homepage
            });
        });
    </script>
    ";
    exit();
} catch (Exception $e) {
    $conn->rollBack();
    die("Error processing payment: " . $e->getMessage());
}
