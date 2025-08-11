<?php
session_start();

// Calculate totals
$items = $_SESSION['cart'] ?? [];
$subtotal = 0;
$totalItems = 0;
foreach ($items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
    $totalItems += $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Cart</title>
    <!-- Css Style -->
    <link rel="stylesheet" href="/css/cart_style.css">

</head>

<body>
    <section class="cart-section">
    <?php include 'header.php'; ?>
    <hr class="header-divider">
    <div class="cart-container">
        <h1>Shopping Cart</h1>

        <?php if (empty($items)): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $id => $item): ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="cart-img">
                                <?= htmlspecialchars($item['name']) ?>
                            </td>
                            <td>$<?= number_format($item['price'], 2) ?></td>
                            <td>
                                <?= $item['quantity'] ?>
                            </td>
                            <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="order-summary">
                <h3>Order Summary</h3>
                <p>Items: <?= $totalItems ?></p>
                <p>Subtotal: $<?= number_format($subtotal, 2) ?></p>
                <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
            </div>
        <?php endif; ?>
    </div>

    
    </section>
    <?php include 'footer.php'; ?>
    <?php include 'backToTop.php'; ?>
</body>

</html>