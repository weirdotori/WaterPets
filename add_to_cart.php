<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['productID'])) {
        $productID = intval($_POST['productID']);
        $quantityToAdd = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

        $stmt = $conn->prepare("SELECT productID, pName, price, image FROM products WHERE productID = ?");
        $stmt->execute([$productID]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            if (isset($_SESSION['cart'][$productID])) {
                $_SESSION['cart'][$productID]['quantity'] += $quantityToAdd;
            } else {
                $_SESSION['cart'][$productID] = [
                    'productID' => $product['productID'],
                    'name' => $product['pName'],
                    'price' => $product['price'],
                    'image' => $product['image'],
                    'quantity' => $quantityToAdd
                ];
            }
        }
    } 
    elseif (isset($_POST['update_product_id']) && isset($_POST['new_quantity'])) {
        $updateID = intval($_POST['update_product_id']);
        $newQuantity = max(1, intval($_POST['new_quantity']));

        if (isset($_SESSION['cart'][$updateID])) {
            $_SESSION['cart'][$updateID]['quantity'] = $newQuantity;
        }
    } 
    elseif (isset($_POST['remove_product_id'])) {
        unset($_SESSION['cart'][$_POST['remove_product_id']]);
    } 
    elseif (isset($_POST['remove_all'])) {
        unset($_SESSION['cart']);
    }
}

// If it's an AJAX request, return JSON
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    $items = $_SESSION['cart'] ?? [];
    $subtotal = 0;
    $totalItems = 0;
    foreach ($items as $item) {
        $subtotal += $item['price'] * $item['quantity'];
        $totalItems += $item['quantity'];
    }
    echo json_encode([
        'cart' => $items,
        'subtotal' => number_format($subtotal, 2),
        'totalItems' => $totalItems
    ]);
    exit;
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
