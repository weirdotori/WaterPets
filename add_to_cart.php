<?php
session_start();
require 'db.php';

$userID = $_SESSION['user']['userID'] ?? null; // Logged-in user's ID

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['productID'])) {
        $productID = intval($_POST['productID']);
        $quantityToAdd = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

        // Get product info
        $stmt = $conn->prepare("SELECT productID, pName, price, image FROM products WHERE productID = ?");
        $stmt->execute([$productID]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            // If user is logged in → store in DB
            if ($userID) {
                // Check if item already exists in cart
                $check = $conn->prepare("SELECT quantity FROM cart_items WHERE userID = ? AND productID = ?");
                $check->execute([$userID, $productID]);
                $existing = $check->fetch(PDO::FETCH_ASSOC);

                if ($existing) {
                    // Update quantity
                    $newQty = $existing['quantity'] + $quantityToAdd;
                    $update = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE userID = ? AND productID = ?");
                    $update->execute([$newQty, $userID, $productID]);
                } else {
                    // Insert new cart item
                    $insert = $conn->prepare("INSERT INTO cart_items (userID, productID, quantity) VALUES (?, ?, ?)");
                    $insert->execute([$userID, $productID, $quantityToAdd]);
                }
            }

            // Also store in session for immediate feedback
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

        if ($userID) {
            $update = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE userID = ? AND productID = ?");
            $update->execute([$newQuantity, $userID, $updateID]);
        }
    }
    elseif (isset($_POST['remove_product_id'])) {
        $removeID = intval($_POST['remove_product_id']);
        unset($_SESSION['cart'][$removeID]);

        if ($userID) {
            $delete = $conn->prepare("DELETE FROM cart_items WHERE userID = ? AND productID = ?");
            $delete->execute([$userID, $removeID]);
        }
    }
    elseif (isset($_POST['remove_all'])) {
        unset($_SESSION['cart']);

        if ($userID) {
            $deleteAll = $conn->prepare("DELETE FROM cart_items WHERE userID = ?");
            $deleteAll->execute([$userID]);
        }
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
