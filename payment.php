<?php
session_start();
if (empty($_SESSION['user']) || empty($_SESSION['pending_order'])) {
    header("Location: cart.php");
    exit();
}


require 'db.php';
$orderID = $_SESSION['pending_order']['orderID'] ?? null;
if (!$orderID) {
    die("No order ID found.");
}

// Fetch order info (optional, to show summary)
// $orderID = $_SESSION['orderID'];
// $stmt = $conn->prepare("SELECT * FROM orders WHERE orderID = ?");
// $stmt->execute([$orderID]);
// $order = $stmt->fetch();

// if (!$order) {
//     die("Order not found.");
// }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Payment</title>
    <!-- Css Style -->
    <link rel="stylesheet" href="/css/payment_style.css">
    <!-- Tailwind css -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <section class="payment-section">
        <?php include 'header.php'; ?>
        <hr class="header-divider">

        <h2>Payment for Order #<?= htmlspecialchars($orderID) ?></h2>
        <form action="payment_process.php" method="POST" id="paymentForm">
            <label>
                Payment Method:
                <select name="payment_method" id="payment_method" required>
                    <option value="">Select Payment Method</option>
                    <option value="paypal">PayPal</option>
                    <option value="visa">Visa</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="cod">Cash on Delivery</option>
                </select>
            </label>

            <div id="payment-details">

                <!-- PayPal requires maybe email -->
                <div class="payment-detail paypal-detail hidden">
                    <label for="paypal_email">PayPal Email:</label>
                    <input type="email" name="paypal_email" id="paypal_email" placeholder="paypal@example.com" />
                </div>

                <!-- Visa / Credit Card inputs -->
                <div class="payment-detail card-detail hidden">
                    <label for="card_number">Card Number:</label>
                    <input type="text" name="card_number" id="card_number" placeholder="1234 5678 9012 3456" maxlength="19" />

                    <label for="card_expiry">Expiry Date (MM/YY):</label>
                    <input type="text" name="card_expiry" id="card_expiry" placeholder="MM/YY" maxlength="5" />

                    <label for="card_cvc">CVC:</label>
                    <input type="text" name="card_cvc" id="card_cvc" placeholder="123" maxlength="4" />
                </div>
            </div>

            <button type="submit">Confirm Payment</button>
        </form>
    </section>

    <?php include 'footer.php'; ?>

    <?php include 'backToTop.php'; ?>

    <script>
        const paymentMethodSelect = document.getElementById('payment_method');
        const paypalFields = document.querySelector('.paypal-detail');
        const cardFields = document.querySelector('.card-detail');

        paymentMethodSelect.addEventListener('change', () => {
            const val = paymentMethodSelect.value;
            paypalFields.classList.add('hidden');
            cardFields.classList.add('hidden');

            if (val === 'paypal') {
                paypalFields.classList.remove('hidden');
                // Make paypal email required
                document.getElementById('paypal_email').required = true;
                ['card_number', 'card_expiry', 'card_cvc'].forEach(id => {
                    document.getElementById(id).required = false;
                });
            } else if (val === 'visa' || val === 'credit_card') {
                cardFields.classList.remove('hidden');
                document.getElementById('paypal_email').required = false;
                ['card_number', 'card_expiry', 'card_cvc'].forEach(id => {
                    document.getElementById(id).required = true;
                });
            } else {
                document.getElementById('paypal_email').required = false;
                ['card_number', 'card_expiry', 'card_cvc'].forEach(id => {
                    document.getElementById(id).required = false;
                });
            }
        });
    </script>
</body>

</html>