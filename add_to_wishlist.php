<?php
session_start();
require 'db.php';

if (empty($_SESSION['user'])) {
    // Not logged in, redirect to login
    header('Location: userLogin.php');
    exit;
}

$userID = $_SESSION['user']['userID'];
$productID = $_GET['productID'] ?? null;

if (!$productID) {
    header('Location: fish.php'); // or fallback page
    exit;
}

// Insert if not exists
try {
    $stmt = $conn->prepare("INSERT IGNORE INTO wishlist (userID, productID) VALUES (?, ?)");
    $stmt->execute([$userID, $productID]);
} catch (Exception $e) {
    // Handle error if needed
}

// Redirect back to previous page or wishlist page
$redirect = $_SERVER['HTTP_REFERER'] ?? 'fish.php';
header("Location: $redirect");
exit;
