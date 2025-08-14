<?php
session_start();
require 'db.php'; // conn connection

if (empty($_SESSION['user'])) {
    header("Location: cart.php?login_required=1");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: checkout.php");
    exit();
}

// Get form fields safely
$firstName = $_POST['first_name'] ?? '';
$lastName = $_POST['last_name'] ?? '';
$country = $_POST['country'] ?? '';
$streetAddress = $_POST['street'] ?? '';
$city = $_POST['city'] ?? '';
$state = $_POST['state'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$deliveryType = $_POST['delivery_type'] ?? '';
$shippingSpeed = $_POST['shipping_speed'] ?? null;

$couponCode = $_POST['coupon_code'] ?? null;





// Basic validation
if (!$firstName || !$lastName || !$country || !$streetAddress || !$city || !$state || !$phone || !$email || !$deliveryType) {
    die("Please fill all required fields.");
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    die("Your cart is empty.");
}

// Calculate subtotal and shipping fee
$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

// SHipping Fee
$shippingFee = 0;
if ($deliveryType === 'shipping') {
    if ($city === 'Yangon') $shippingFee = 5.00;
    if ($city === 'Mandalay') $shippingFee = 8.00;
    if ($shippingSpeed === 'priority') $shippingFee += 3.00;
}

// Total before discount
$discount = 0;
$totalPrice = $subtotal + $shippingFee;

// Apply coupon if valid
if (!empty($_SESSION['applied_coupon']) && $_SESSION['applied_coupon']['code'] === $couponCode) {
    $discount = $_SESSION['applied_coupon']['discount']; // 10%
    $totalPrice = $totalPrice * (1 - $discount / 100);

    // Mark coupon as used in DB
    $stmt = $conn->prepare("UPDATE coupons SET status='used' WHERE couponID=?");
    $stmt->execute([$_SESSION['applied_coupon']['couponID']]);

    unset($_SESSION['applied_coupon']);
}

// Check if pending order exists
$stmt = $conn->prepare("SELECT orderID FROM orders WHERE userID = ? AND orderStatus = 'pending' ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$_SESSION['user']['userID']]);
$existingOrder = $stmt->fetchColumn();

if ($existingOrder) {
    // Update existing pending order
    $stmt = $conn->prepare("
        UPDATE orders SET 
            firstName = :firstName,
            lastName = :lastName,
            country = :country,
            streetAddress = :streetAddress,
            city = :city,
            state = :state,
            phone = :phone,
            email = :email,
            deliveryType = :deliveryType,
            couponCode = :couponCode,
            totalPrice = :totalPrice,
            created_at = NOW()
        WHERE orderID = :orderID
    ");

    $stmt->execute([
        ':firstName' => $firstName,
        ':lastName' => $lastName,
        ':country' => $country,
        ':streetAddress' => $streetAddress,
        ':city' => $city,
        ':state' => $state,
        ':phone' => $phone,
        ':email' => $email,
        ':deliveryType' => $deliveryType,
        ':couponCode' => $couponCode,
        ':totalPrice' => $totalPrice,
        ':orderID' => $existingOrder
    ]);

    $pendingOrderID = $existingOrder;
} else {
    // Insert new pending order
    $stmt = $conn->prepare("
        INSERT INTO orders 
        (userID, firstName, lastName, country, streetAddress, city, state, phone, email, deliveryType, couponCode, totalPrice, orderStatus, created_at)
        VALUES 
        (:userID, :firstName, :lastName, :country, :streetAddress, :city, :state, :phone, :email, :deliveryType, :couponCode, :totalPrice, 'pending', NOW())
    ");

    $stmt->execute([
        ':userID' => $_SESSION['user']['userID'],
        ':firstName' => $firstName,
        ':lastName' => $lastName,
        ':country' => $country,
        ':streetAddress' => $streetAddress,
        ':city' => $city,
        ':state' => $state,
        ':phone' => $phone,
        ':email' => $email,
        ':deliveryType' => $deliveryType,
        ':couponCode' => $couponCode,
        ':totalPrice' => $totalPrice
    ]);

    $pendingOrderID = $conn->lastInsertId();
}



// Save order info in session instead of database
$_SESSION['pending_order'] = [
    'orderID' => $pendingOrderID,
    'userID' => $_SESSION['user']['userID'],
    'firstName' => $firstName,
    'lastName' => $lastName,
    'country' => $country,
    'streetAddress' => $streetAddress,
    'city' => $city,
    'state' => $state,
    'phone' => $phone,
    'email' => $email,
    'deliveryType' => $deliveryType,
    'shippingSpeed' => $shippingSpeed, // keep it here for later use after payment
    'couponCode' => $couponCode,
    'cart' => $cart,
    'subtotal' => $subtotal,
    'shippingFee' => $shippingFee,
    'totalPrice' => $totalPrice,
];


// Redirect to payment page
header("Location: payment.php");
exit();
