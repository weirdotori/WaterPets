<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['productID'])) {
    $productID = intval($_POST['productID']);

    // Get product details from DB
    $stmt = $conn->prepare("SELECT productID, pName, price, image FROM products WHERE productID = ?");
    $stmt->execute([$productID]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // If cart doesn't exist yet, create it
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // If product already in cart, increment quantity
        if (isset($_SESSION['cart'][$productID])) {
            $_SESSION['cart'][$productID]['quantity']++;
        } else {
            $_SESSION['cart'][$productID] = [
                'id' => $product['productID'],
                'name' => $product['pName'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => 1
            ];
        }
    }
}

// Redirect back to previous page
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>
