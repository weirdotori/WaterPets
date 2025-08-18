<?php
session_start();
if (empty($_SESSION['user']) || empty($_SESSION['pending_order'])) {
    header("Location: cart.php");
    exit();
}

require 'db.php';

$orderID = $_SESSION['pending_order']['orderID'] ?? null;
if (!$orderID) die("No order ID found.");

// 1. Order info
$stmt = $conn->prepare("SELECT * FROM orders WHERE orderID = ?");
$stmt->execute([$orderID]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$order) die("Order not found.");

// 2. User info
$stmt = $conn->prepare("SELECT * FROM users WHERE userID = ?");
$stmt->execute([$order['userID']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 3. Items from SESSION cart
$cartItems = $_SESSION['cart'] ?? [];

// 4. Totals from session
$subtotal    = $_SESSION['pending_order']['subtotal'] ?? 0;
$discount    = $_SESSION['pending_order']['discount'] ?? 0;
$shippingFee = $_SESSION['pending_order']['shippingFee'] ?? 0;
$grandTotal  = $_SESSION['pending_order']['grandTotal'] ?? ($subtotal - $discount + $shippingFee);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment</title>
    <link rel="stylesheet" href="/css/payment_style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <section class="payment-section">
        <?php include 'header.php';?>
        <hr class="header-divider">

        <section class="container-grid">

            <!-- Left: Payment Options -->
            <div class="card">
                <h2 class="title">Select Payment Method</h2>

                <div id="payment-methods" class="payment-grid">
                    <button type="button" class="payment-btn" data-method="paypal">
                        <img src="/images/paypal.png" alt="PayPal" class="payment-img">
                        <span>PayPal</span>
                    </button>
                    <button type="button" class="payment-btn" data-method="visa">
                        <img src="/images/visa.png" alt="Visa" class="payment-img">
                        <span>Visa</span>
                    </button>
                    <button type="button" class="payment-btn" data-method="credit_card">
                        <img src="/images/credit-card.png" alt="Credit Card" class="payment-img">
                        <span>Credit Card</span>
                    </button>
                    <button type="button" class="payment-btn" data-method="cod">
                        <img src="/images/cash.png" alt="Cash" class="payment-img">
                        <span>Cash on Delivery</span>
                    </button>
                </div>

                <form action="payment_process.php" method="POST" id="paymentForm">
                    <input type="hidden" name="payment_method" id="payment_method">

                    <div id="payment-details">
                        <!-- PayPal -->
                        <div class="payment-detail hidden paypal-detail">
                            <label for="paypal_email">PayPal Email:</label>
                            <input type="email" name="paypal_email" id="paypal_email" placeholder="paypal@example.com">
                        </div>

                        <!-- Card -->
                        <div class="payment-detail hidden card-detail">
                            <label for="card_number">Card Number:</label>
                            <input type="text" name="card_number" id="card_number" placeholder="1234 5678 9012 3456" maxlength="19">

                            <label for="card_expiry">Expiry Date (MM/YY):</label>
                            <input type="text" name="card_expiry" id="card_expiry" placeholder="MM/YY" maxlength="5">

                            <label for="card_cvc">CVC:</label>
                            <input type="text" name="card_cvc" id="card_cvc" placeholder="123" maxlength="4">
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">Confirm Payment</button>
                </form>
            </div>

            <!-- Right: Order Summary -->
            <div class="card order-summary">
                <h2 class="title">Order Summary</h2>

                <p>Order ID: <?= htmlspecialchars($order['orderID']) ?></p>
                <p>User: <?= htmlspecialchars($user['username'] . ' (' . $user['email'] . ')') ?></p>
                <p>Delivery Type: <?= htmlspecialchars($order['deliveryType']) ?></p>
                <p>Address: <?= htmlspecialchars($order['streetAddress'] . ', ' . $order['city'] . ', ' . $order['state'] . ', ' . $order['country']) ?></p>
                <p>Phone: <?= htmlspecialchars($order['phone']) ?></p>
                <p>Email: <?= htmlspecialchars($order['email']) ?></p>
                <p>Coupon: <?= htmlspecialchars($order['couponCode'] ?? 'None') ?></p>
                <hr class="divider">

                <h3>Items:</h3>
                <ul class="item-list">
                    <?php if (!empty($cartItems)): ?>
                        <?php foreach ($cartItems as $item): ?>
                            <li class="item-row">
                                <div>
                                    <p class="item-name"><?= htmlspecialchars($item['name']) ?></p>
                                    <p class="item-small">Qty: <?= (int)$item['quantity'] ?></p>
                                    <p class="item-small">Unit Price: $<?= number_format($item['price'], 2) ?></p>
                                </div>
                                <span class="item-price">$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="item-small">No items found in your cart.</li>
                    <?php endif; ?>
                </ul>

                <hr class="divider">
                <div class="summary-row"><span>Subtotal:</span><span>$<?= number_format($subtotal, 2) ?></span></div>
                <div class="summary-row discount"><span>Discount:</span><span>- $<?= number_format($discount, 2) ?></span></div>
                <div class="summary-row"><span>Shipping:</span><span>$<?= number_format($shippingFee, 2) ?></span></div>
                <div class="summary-row grand-total"><span>Grand Total:</span><span>$<?= number_format($grandTotal, 2) ?></span></div>
            </div>

        </section>
    </section>

    <?php include 'footer.php'; ?>
    <?php include 'chatbot.php'; ?>
    <?php include 'backToTop.php'; ?>

    <script>
        const paymentButtons = document.querySelectorAll('.payment-btn');
        const hiddenInput = document.getElementById('payment_method');
        const paypalFields = document.querySelector('.paypal-detail');
        const cardFields = document.querySelector('.card-detail');

        paymentButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                paymentButtons.forEach(b => b.classList.remove('active-btn'));
                btn.classList.add('active-btn');

                const method = btn.dataset.method;
                hiddenInput.value = method;

                paypalFields.classList.add('hidden');
                cardFields.classList.add('hidden');

                if (method === 'paypal') {
                    paypalFields.classList.remove('hidden');
                    document.getElementById('paypal_email').required = true;
                    ['card_number', 'card_expiry', 'card_cvc'].forEach(id => document.getElementById(id).required = false);
                } else if (method === 'visa' || method === 'credit_card') {
                    cardFields.classList.remove('hidden');
                    document.getElementById('paypal_email').required = false;
                    ['card_number', 'card_expiry', 'card_cvc'].forEach(id => document.getElementById(id).required = true);
                } else {
                    document.getElementById('paypal_email').required = false;
                    ['card_number', 'card_expiry', 'card_cvc'].forEach(id => document.getElementById(id).required = false);
                }
            });
        });
    </script>

</body>

</html>