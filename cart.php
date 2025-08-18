<?php
session_start();

require 'db.php'; // Make sure you have DB connection

$userID = $_SESSION['user']['userID'] ?? null;

if ($userID) {
    // Load cart from DB
    $stmt = $conn->prepare("
        SELECT p.productID, p.pName, p.price, p.image, c.quantity
        FROM cart_items c
        JOIN products p ON c.productID = p.productID
        WHERE c.userID = ?
    ");
    $stmt->execute([$userID]);
    $items = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $items[$row['productID']] = [
            'productID' => $row['productID'],
            'name' => $row['pName'],
            'price' => $row['price'],
            'image' => $row['image'],
            'quantity' => $row['quantity']
        ];
    }
    $_SESSION['cart'] = $items; // Sync DB cart with session
} else {
    // Guest user → show only session cart
    $items = $_SESSION['cart'] ?? [];
}


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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body>
    <section class="cart-section">
        <?php include 'header.php'; ?>

        <hr class="header-divider">
        <div class="cart-container">
            <h1>Shopping Cart</h1>

            <?php if (empty($items)): ?>
                <div class="empty-cart">
                    <img src="/images/cart.png" alt="Empty Cart" class="empty-cart-img">
                    <h2>Your Cart is Empty</h2>
                    <p>Looks like you haven't added anything to your cart yet.</p>
                    <a href="fish.php" class="btn-continue-shopping">Continue Shopping</a>
                </div>
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
                                Clear Cart
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
    // Quantitiy js
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

    // proceed to checkout button login check
    document.querySelector('.checkout-btn').addEventListener('click', function(e) {
        e.preventDefault();

        <?php if (!isset($_SESSION['user'])): ?>
            // Show modal to login/register
            showLoginModal();
        <?php else: ?>
            // Redirect to checkout page if logged in
            window.location.href = 'checkout.php';
        <?php endif; ?>
    });

    function showLoginModal() {
        const modal = document.createElement('div');
        modal.innerHTML = `
      <div class="modal-overlay">
        <div class="modal-content">
          <h2>Please log in first</h2>
          <p>You need to be logged in to proceed to checkout.</p>
          <div class="modal-buttons">
            <a href="userLogin.php" class="btn btn-primary">Log In</a>
            <a href="register.php" class="btn btn-secondary">Register</a>
            <button id="closeModalBtn" class="btn btn-close">Close</button>
          </div>
        </div>
      </div>
    `;
        document.body.appendChild(modal);

        document.getElementById('closeModalBtn').addEventListener('click', () => {
            modal.remove();
        });
    }
</script>