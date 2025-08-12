<?php
session_start();
if (empty($_SESSION['user'])) {
    // Not logged in, redirect to cart with flag
    header("Location: cart.php?login_required=1");
    exit();
}

// Calculate totals from session cart
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
    <meta charset="UTF-8" />
    <title>Checkout</title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet" />
    <link rel="stylesheet" href="/css/checkout_style.css" />
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="checkout-container">
        <form class="billing-form" action="process_checkout.php" method="POST" id="checkoutForm">
            <h2 class="mb-6 font-bold text-2xl">Billing Details</h2>
            <label for="first_name">First Name *</label>
            <input type="text" id="first_name" name="first_name" placeholder="Ex. John" required />

            <label for="last_name">Last Name *</label>
            <input type="text" id="last_name" name="last_name" placeholder="Ex. Doe" required />

            <label for="company">Company Name (Optional)</label>
            <input type="text" id="company" name="company" placeholder="Enter Company Name" />

            <label for="country">Country *</label>
            <select id="country" name="country" required>
                <option value="">Select Country</option>
                <option value="USA">United States</option>
                <option value="Canada">Canada</option>
                <!-- Add more countries here -->
            </select>

            <label for="street">Street Address *</label>
            <input type="text" id="street" name="street" placeholder="Enter Street Address" required />

            <label for="city">City *</label>
            <select id="city" name="city" required>
                <option value="">Select City</option>
                <option value="New York">New York</option>
                <option value="Los Angeles">Los Angeles</option>
                <!-- Add more cities -->
            </select>

            <label for="state">State *</label>
            <select id="state" name="state" required>
                <option value="">Select State</option>
                <option value="NY">New York</option>
                <option value="CA">California</option>
                <!-- Add more states -->
            </select>

            <label for="zip">Zip Code *</label>
            <input type="text" id="zip" name="zip" placeholder="Enter Zip Code" required />

            <label for="phone">Phone *</label>
            <input type="tel" id="phone" name="phone" placeholder="Enter Phone Number" required />

            <label for="email">Email *</label>
            <input type="email" id="email" name="email" placeholder="Enter Email" required />

            <button type="submit" class="proceed-btn">Proceed to Payment</button>
        </form>

        <div class="order-summary">
            <h3>Order Summary</h3>
            <div class="summary-row"><span>Items</span><span><?= $totalItems ?></span></div>
            <div class="summary-row"><span>Sub Total</span><span>$<?= number_format($subtotal, 2) ?></span></div>
            <div class="summary-row"><span>Shipping</span><span>$0.00</span></div>
            <div class="summary-row"><span>Taxes</span><span>$0.00</span></div>
            <div class="summary-row" style="color: #777;">Coupon Discount</div>
            <div class="summary-row" style="color: #777;">- $0.00</div>
            <div class="summary-total"><span>Total</span><span>$<?= number_format($subtotal, 2) ?></span></div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

</body>
</html>
