<?php
session_start();
require 'db.php'; // PDO connection

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
$couponCode = null; // handle if you use coupons

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

$shippingFee = 0;
if ($deliveryType === 'shipping') {
    if ($city === 'Yangon') $shippingFee = 5.00;
    if ($city === 'Mandalay') $shippingFee = 8.00;
    if ($shippingSpeed === 'priority') $shippingFee += 3.00;
}

$totalPrice = $subtotal + $shippingFee;

// Save order info in session instead of database
$_SESSION['pending_order'] = [
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
    'shippingSpeed' => $shippingSpeed,
    'couponCode' => $couponCode,
    'cart' => $cart,
    'subtotal' => $subtotal,
    'shippingFee' => $shippingFee,
    'totalPrice' => $totalPrice,
];

// Redirect to payment page
header("Location: payment.php");
exit();
?>
