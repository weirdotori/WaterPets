<?php
session_start();
require 'db.php'; // add your PDO connection

if (empty($_SESSION['user'])) {
    header("Location: cart.php?login_required=1");
    exit();
}

$userID = $_SESSION['user']['userID'];

// Fetch existing Processing order for this user (latest)
$stmt = $conn->prepare("SELECT * FROM orders WHERE userID = ? AND orderStatus = 'Processing' ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$userID]);
// Use session pending order first
$pendingOrder = $_SESSION['pending_order'] ?? null;

// If not in session, just empty strings
$firstName = $pendingOrder['firstName'] ?? '';
$lastName  = $pendingOrder['lastName'] ?? '';
$country   = $pendingOrder['country'] ?? '';
$streetAddress = $pendingOrder['streetAddress'] ?? '';
$city      = $pendingOrder['city'] ?? '';
$state     = $pendingOrder['state'] ?? '';
$phone     = $pendingOrder['phone'] ?? '';
$email     = $pendingOrder['email'] ?? '';
$deliveryType = $pendingOrder['deliveryType'] ?? '';
$couponCode   = $pendingOrder['couponCode'] ?? '';
$shippingSpeed = $pendingOrder['shippingSpeed'] ?? '';

// Your cart calculation as before
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
    <meta charset="UTF-8"/>
    <title>Checkout</title>
    <link rel="stylesheet" href="/css/checkout_style.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


</head>
<body>


<section class="checkout-section">
    <?php include 'header.php';?>

        <hr class="header-divider">
        <div class="checkout-container">
            <form class="billing-form" action="checkout_process.php" method="POST" id="checkoutForm">
                <h2 class="form-title">Billing Details</h2>

                <label for="first_name">First Name *</label>
                <input type="text" id="first_name" name="first_name" placeholder="Ex. John"
                    value="<?= htmlspecialchars($firstName) ?>" required />

                <label for="last_name">Last Name *</label>
                <input type="text" id="last_name" name="last_name" placeholder="Ex. Doe"
                    value="<?= htmlspecialchars($lastName) ?>" required />

                <label for="country">Country *</label>
                <select id="country" name="country" required>
                    <option value="">Select Country</option>
                    <option value="Myanmar" <?= $country === 'Myanmar' ? 'selected' : '' ?>>Myanmar</option>
                    <option value="Thailand" <?= $country === 'Thailand' ? 'selected' : '' ?>>Thailand</option>
                </select>

                <label for="street">Street Address *</label>
                <input type="text" id="street" name="street" placeholder="Enter Street Address"
                    value="<?= htmlspecialchars($streetAddress) ?>" required />

                <label for="city">City *</label>
                <select id="city" name="city" required>
                    <option value="">Select City</option>
                    <option value="Yangon" <?= $city === 'Yangon' ? 'selected' : '' ?>>Yangon</option>
                    <option value="Mandalay" <?= $city === 'Mandalay' ? 'selected' : '' ?>>Mandalay</option>
                </select>

                <label for="state">Region/State *</label>
                <select id="state" name="state" required>
                    <option value="">Select State</option>
                    <option value="YG" <?= $state === 'YG' ? 'selected' : '' ?>>Yangon</option>
                    <option value="SH" <?= $state === 'SH' ? 'selected' : '' ?>>Shan</option>
                    <option value="KA" <?= $state === 'KA' ? 'selected' : '' ?>>Kachin</option>
                </select>

                <label for="phone">Phone *</label>
                <input type="tel" id="phone" name="phone" placeholder="Enter Phone Number"
                    value="<?= htmlspecialchars($phone) ?>" required />

                <label for="email">Email *</label>
                <input type="email" id="email" name="email" placeholder="Enter Email"
                    value="<?= htmlspecialchars($email) ?>" required />

                <!-- Delivery Type -->
                <p class="field-label">Delivery Type *</p>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="delivery_type" value="shipping" checked required> Shipping
                    </label>
                </div>

                <!-- Shipping speed -->
                <div id="shipping_speed_wrapper" class="shipping-speed">
                    <label for="shipping_speed">Shipping Speed *</label>
                    <select id="shipping_speed" name="shipping_speed">
                        <option value="">Select Speed</option>
                        <option value="standard">Standard</option>
                        <option value="priority">Priority</option>
                    </select>
                </div>

                <!-- Coupon -->
                <label for="coupon_code">Coupon Code</label>
                <div class="coupon-row">
                    <input type="text" id="coupon_code" name="coupon_code" placeholder="Enter coupon code"
                        value="<?= htmlspecialchars($couponCode ?? '') ?>">
                    <button type="button" id="applyCouponBtn" class="apply-btn">Apply</button>
                </div>
                <p id="couponMsg" class="coupon-msg"></p>

                <button type="submit" class="proceed-btn">Proceed to Payment</button>
            </form>

            <div class="order-summary">
                <h3>Order Summary</h3>

                <div class="summary-row">
                    <span>Items</span>
                    <span id="summary-items"><?= $totalItems ?></span>
                </div>

                <div class="summary-row">
                    <span>Sub Total</span>
                    <span id="summary-subtotal">$<?= number_format($subtotal, 2) ?></span>
                </div>

                <div class="summary-row">
                    <span>Shipping</span>
                    <span id="summary-shipping">$0.00</span>
                </div>

                <div class="summary-row">
                    <span>Taxes</span>
                    <span id="summary-taxes">$0.00</span>
                </div>

                <div class="summary-row discount">
                    <span>Coupon Discount</span>
                    <span id="summary-coupon">- $0.00</span>
                </div>

                <div class="summary-total">
                    <span>Total</span>
                    <span id="summary-total">$<?= number_format($subtotal, 2) ?></span>
                </div>
            </div>
        </div>
        </section>

    <?php include 'footer.php'; ?>
    <?php include 'chatbot.php'; ?>
    <?php include 'backToTop.php'; ?>

    <script>
        /* DOM refs */
        // const deliveryRadios = document.querySelectorAll('input[name="delivery_type"]');
        const citySelect = document.getElementById('city');
        const speedSelect = document.getElementById('shipping_speed');
        const shippingWrapper = document.getElementById('shipping_speed_wrapper');

        const shippingEl = document.getElementById('summary-shipping');
        const subtotalEl = document.getElementById('summary-subtotal');
        const totalEl = document.getElementById('summary-total');

        /* pass PHP subtotal safely to JS (no formatting) */
        const baseSubtotal = Number(<?= json_encode((float)$subtotal) ?>) || 0;
        let appliedCoupon = {
            code: '', // coupon code
            percent: 0 // discount percentage
        };

        /* shipping rules */
        function calculateShippingFee() {
            const deliveryType = document.querySelector('input[name="delivery_type"]:checked')?.value;
            if (deliveryType !== 'shipping') return 0;

            let fee = 0;
            if (citySelect.value === 'Yangon') fee = 5.00;
            if (citySelect.value === 'Mandalay') fee = 8.00;

            if (speedSelect.value === 'priority') fee += 3.00;
            return fee;
        }

        function updateSummary() {
            const fee = calculateShippingFee();
            subtotalEl.textContent = `$${baseSubtotal.toFixed(2)}`;
            shippingEl.textContent = `$${fee.toFixed(2)}`;

            const discountAmount = (baseSubtotal + fee) * (appliedCoupon.percent / 100);
            document.getElementById('summary-coupon').textContent = `- $${discountAmount.toFixed(2)}`;

            totalEl.textContent = `$${(baseSubtotal + fee - discountAmount).toFixed(2)}`;
        }


        /* show/hide speed when delivery type changes */
        // deliveryRadios.forEach(radio => {
        //     radio.addEventListener('change', () => {
        //         if (radio.value === 'shipping' && radio.checked) {
        //             shippingWrapper.classList.remove('hidden');
        //             speedSelect.required = true; // <--- add required
        //         } else if (radio.checked) {
        //             shippingWrapper.classList.add('hidden');
        //             speedSelect.value = '';
        //             speedSelect.required = false; // <--- remove required
        //         }
        //         updateSummary();
        //     });
        // });


        /* update when city or speed changes */
        citySelect.addEventListener('change', updateSummary);
        speedSelect.addEventListener('change', updateSummary);

        /* initial render (ensures values show on load) */
        updateSummary();
    </script>

    <!-- Coupon code AJAX Verification -->
    <script>
        document.getElementById('applyCouponBtn').addEventListener('click', function() {
            const code = document.getElementById('coupon_code').value.trim();
            if (!code) return alert('Please enter a coupon code');

            fetch('verify_coupon.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'coupon_code=' + encodeURIComponent(code)
                })
                .then(res => res.json())
                .then(data => {
                    const msg = document.getElementById('couponMsg');
                    if (data.valid) {
                        msg.style.color = 'green';
                        msg.textContent = `Coupon applied: ${data.discount}% OFF`;

                        // store coupon in JS variable
                        appliedCoupon.code = code;
                        appliedCoupon.percent = Number(data.discount);

                        // update totals immediately
                        updateSummary();
                    } else {
                        msg.style.color = 'red';
                        msg.textContent = 'Invalid or used coupon code';

                        appliedCoupon.code = '';
                        appliedCoupon.percent = 0;
                        updateSummary();
                    }
                });
        });
    </script>

</body>

</html>