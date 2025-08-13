<?php
session_start();
require 'db.php';

if (empty($_SESSION['user'])) {
    header('Location: userLogin.php');
    exit;
}

$userID = $_SESSION['user']['userID'];
$productID = $_POST['productID'] ?? null;

if ($productID) {
    $stmt = $conn->prepare("DELETE FROM wishlist WHERE userID = ? AND productID = ?");
    $stmt->execute([$userID, $productID]);
}

header('Location: wishlist.php');
exit;
