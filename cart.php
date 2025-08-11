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
    <!-- Tailwind css -->
    <script src="https://cdn.tailwindcss.com"></script>
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
                <div class="cart-main">
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
                                    <td class="product-cell">
                                        <!-- remove product cross btns -->
                                        <form method="post" action="add_to_cart.php" class="remove-form" title="Remove item">
                                            <input type="hidden" name="remove_product_id" value="<?= $id ?>">
                                            <button type="submit" class="remove-btn">&times;</button>
                                        </form>

                                        <!-- product image -->
                                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="cart-img">
                                        <span><?= htmlspecialchars($item['name']) ?></span>
                                    </td>

                                    <td>$<?= number_format($item['price'], 2) ?></td>
                                    <td>
                                        <div class="quantity-control" data-id="<?= $id ?>" data-price="<?= $item['price'] ?>">
                                            <button type="button" class="qty-minus">-</button>
                                            <input type="number" class="qty-input" value="<?= $item['quantity'] ?>" min="1">
                                            <button type="button" class="qty-plus">+</button>
                                        </div>
                                    </td>
                                    <td class="item-subtotal">$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="order-summary">
                        <h3>Order Summary</h3>
                        <p class="total-items">Items: <?= $totalItems ?></p>
                        <p class="order-subtotal">Subtotal: $<?= number_format($subtotal, 2) ?></p>

                        <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>

                        <!-- Remove all form -->
                        <form method="post" action="add_to_cart.php" style="margin-top: 10px;">
                            <input type="hidden" name="remove_all" value="1">
                            <button type="submit" style="background:#ff4d4d; color:#fff; border:none; padding:8px 16px; border-radius:5px; cursor:pointer;">
                                Remove All Items
                            </button>
                        </form>

                    </div>
                </div>
            <?php endif; ?>
        </div>


    </section>
    <?php include 'footer.php'; ?>
    <?php include 'backToTop.php'; ?>
</body>

</html>

<script>
    document.querySelectorAll('.quantity-control').forEach(control => {
        const minusBtn = control.querySelector('.qty-minus');
        const plusBtn = control.querySelector('.qty-plus');
        const input = control.querySelector('.qty-input');
        const productId = control.dataset.id;
        const price = parseFloat(control.dataset.price);
        const subtotalCell = control.closest('tr').querySelector('.item-subtotal');

        function updateQuantity(newQty) {
            fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams({
                        update_product_id: productId,
                        new_quantity: newQty
                    })
                })
                .then(res => res.json())
                .then(data => {
                    // Update item subtotal
                    subtotalCell.textContent = '$' + (price * newQty).toFixed(2);
                    // Update totals
                    document.querySelector('.order-subtotal').textContent = 'Subtotal: $' + data.subtotal;
                    document.querySelector('.total-items').textContent = 'Items: ' + data.totalItems;

                });
        }

        minusBtn.addEventListener('click', () => {
            let newQty = Math.max(1, parseInt(input.value) - 1);
            input.value = newQty;
            updateQuantity(newQty);
        });

        plusBtn.addEventListener('click', () => {
            let newQty = parseInt(input.value) + 1;
            input.value = newQty;
            updateQuantity(newQty);
        });

        input.addEventListener('change', () => {
            let newQty = Math.max(1, parseInt(input.value));
            input.value = newQty;
            updateQuantity(newQty);
        });
    });
</script>