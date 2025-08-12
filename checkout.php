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
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/css/checkout_style.css" />
</head>

<body>

    <section class="checkout-section">
        <?php include 'header.php'; ?>
        <hr class="header-divider">

        <div class="checkout-container">
            <form class="billing-form" action="checkout_process.php" method="POST" id="checkoutForm">
                <h2 class="mb-6 font-bold text-2xl">Billing Details</h2>
                <label for="first_name">First Name *</label>
                <input type="text" id="first_name" name="first_name" placeholder="Ex. John" required />

                <label for="last_name">Last Name *</label>
                <input type="text" id="last_name" name="last_name" placeholder="Ex. Doe" required />

                <label for="country">Country *</label>
                <select id="country" name="country" required>
                    <option value="">Select Country</option>
                    <option value="Myanmar">Myanmar</option>
                    <option value="Thailand">Thailand</option>
                    <!-- Add more countries here -->
                </select>

                <label for="street">Street Address *</label>
                <input type="text" id="street" name="street" placeholder="Enter Street Address" required />

                <label for="city">City *</label>
                <select id="city" name="city" required>
                    <option value="">Select City</option>
                    <option value="Yangon">Yangon</option>
                    <option value="Mandalay">Mandalay</option>
                    <!-- Add more cities -->
                </select>

                <label for="state">State *</label>
                <select id="state" name="state" required>
                    <option value="">Select State</option>
                    <option value="NY">Shan</option>
                    <option value="CA">Kachin</option>
                    <!-- Add more states -->
                </select>

                <label for="phone">Phone *</label>
                <input type="tel" id="phone" name="phone" placeholder="Enter Phone Number" required />

                <label for="email">Email *</label>
                <input type="email" id="email" name="email" placeholder="Enter Email" required />

                <!-- Delivery Type radio btn -->
                <label class="block font-semibold mt-4">Delivery Type *</label>
                <div class="flex items-center gap-4 mt-2">
                    <label>
                        <input type="radio" name="delivery_type" value="shipping" required> Shipping
                    </label>
                    <label>
                        <input type="radio" name="delivery_type" value="pickup" required> Pickup
                    </label>
                </div>

                <!-- Shipping speed only show when delivery is shipping -->
                <div id="shipping_speed_wrapper" class="mt-4 hidden">
                    <label for="shipping_speed">Shipping Speed *</label>
                    <select id="shipping_speed" name="shipping_speed">
                        <option value="">Select Speed</option>
                        <option value="standard">Standard</option>
                        <option value="priority">Priority</option>
                    </select>
                </div>


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

                <div class="summary-row" style="color: #777;">
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

    <?php include 'backToTop.php'; ?>

    <script>
        /* DOM refs */
        const deliveryRadios = document.querySelectorAll('input[name="delivery_type"]');
        const citySelect = document.getElementById('city');
        const speedSelect = document.getElementById('shipping_speed');
        const shippingWrapper = document.getElementById('shipping_speed_wrapper');

        const shippingEl = document.getElementById('summary-shipping');
        const subtotalEl = document.getElementById('summary-subtotal');
        const totalEl = document.getElementById('summary-total');

        /* pass PHP subtotal safely to JS (no formatting) */
        const baseSubtotal = Number(<?= json_encode((float)$subtotal) ?>) || 0;

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
            // show cart subtotal (unchanged)
            subtotalEl.textContent = `$${baseSubtotal.toFixed(2)}`;
            // show shipping
            shippingEl.textContent = `$${fee.toFixed(2)}`;
            // total = subtotal + shipping
            totalEl.textContent = `$${(baseSubtotal + fee).toFixed(2)}`;
        }

        /* show/hide speed when delivery type changes */
        deliveryRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                if (radio.value === 'shipping' && radio.checked) {
                    shippingWrapper.classList.remove('hidden');
                    speedSelect.required = true; // <--- add required
                } else if (radio.checked) {
                    shippingWrapper.classList.add('hidden');
                    speedSelect.value = '';
                    speedSelect.required = false; // <--- remove required
                }
                updateSummary();
            });
        });


        /* update when city or speed changes */
        citySelect.addEventListener('change', updateSummary);
        speedSelect.addEventListener('change', updateSummary);

        /* initial render (ensures values show on load) */
        updateSummary();
    </script>
</body>

</html>