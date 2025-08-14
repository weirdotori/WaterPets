<?php
session_start();
require 'db.php';

if (empty($_SESSION['user'])) {
    echo json_encode(['valid' => false]);
    exit();
}

$couponCode = $_POST['coupon_code'] ?? '';
$userID = $_SESSION['user']['userID'];

$stmt = $conn->prepare("SELECT * FROM coupons WHERE userID = ? AND code = ? AND status = 'unused'");
$stmt->execute([$userID, $couponCode]);
$coupon = $stmt->fetch(PDO::FETCH_ASSOC);

if ($coupon) {
    $_SESSION['applied_coupon'] = $coupon; // Save for checkout_process
    echo json_encode(['valid' => true, 'discount' => $coupon['discount']]);
} else {
    echo json_encode(['valid' => false]);
}
