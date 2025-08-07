<?php
session_start();
require_once "db.php";

$productID = $_GET['id'] ?? null;

if (!$productID) {
    echo "<p>Product not found.</p>";
    return;
}

$stmt = $conn->prepare("
    SELECT products.*, categories.cName AS categoryName 
    FROM products 
    JOIN categories ON products.categoryID = categories.categoryID 
    WHERE products.productID = ?
");

$stmt->execute([$productID]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<p>Product not found.</p>";
    return;
}

// Fetch reviews for this product
$reviewStmt = $conn->prepare("
    SELECT r.reviewComment, r.rating, r.created_at, u.username 
    FROM reviews r
    JOIN users u ON r.userID = u.userID
    WHERE r.productID = ?
    ORDER BY r.created_at DESC
");
$reviewStmt->execute([$productID]);
$reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);

// Show review message if exists
if (isset($_SESSION['review_success'])) {
    echo "<p style='color: green'>" . $_SESSION['review_success'] . "</p>";
    unset($_SESSION['review_success']);
}
if (isset($_SESSION['review_error'])) {
    echo "<p style='color: red'>" . $_SESSION['review_error'] . "</p>";
    unset($_SESSION['review_error']);
}
?>

<!-- CSS -->
<link rel="stylesheet" href="/css/product_detail_style.css">


<div class="product-container">
    <div class="product-grid">

        <!-- Product Image -->
        <div class="product-image">
            <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['pName']) ?>">
        </div>

        <!-- Product Details -->
        <div class="product-details">
            <h1 class="product-title"><?= htmlspecialchars($product['pName']) ?></h1>
            <p class="product-description"><?= htmlspecialchars($product['description']) ?></p>

            <?php if ($product['categoryName'] === 'Fish'): ?>
                <p><strong>Water Type:</strong> <?= htmlspecialchars($product['waterType']) ?></p>
                <p><strong>Difficulty:</strong> <?= htmlspecialchars($product['difficulty']) ?></p>
                <p><strong>Species:</strong> <?= htmlspecialchars($product['species']) ?></p>
                <p><strong>Aggression Level:</strong> <?= htmlspecialchars($product['aggressionLevel']) ?></p>
            <?php elseif ($product['categoryName'] === 'Coral Reefs'): ?>
                <p><strong>Lighting Requirement:</strong> <?= htmlspecialchars($product['lighting'] ?? 'N/A') ?></p>
                <p><strong>Flow Level:</strong> <?= htmlspecialchars($product['flow'] ?? 'N/A') ?></p>
            <?php elseif ($product['categoryName'] === 'Supplies' || $product['categoryName'] === 'Equipment'): ?>
                <p><strong>Type:</strong> <?= htmlspecialchars($product['equipmentType'] ?? 'N/A') ?></p>
                <p><strong>Brand:</strong> <?= htmlspecialchars($product['brand'] ?? 'N/A') ?></p>
            <?php endif; ?>

            <div class="product-price">
                Price: $<span id="unit-price"><?= number_format($product['price'], 2) ?></span>
            </div>

            <!-- Quantity selector -->
            <div class="quantity-selector">
                <button type="button" onclick="decreaseQuantity()">−</button>
                <input type="number" id="quantity" name="quantity" value="1" min="1">
                <button type="button" onclick="increaseQuantity()">+</button>
            </div>

            <!-- Total price display -->
            <div class="total-price">
                Total: $<span id="total-price"><?= number_format($product['price'], 2) ?></span>
            </div>

            <!-- Add to cart and wishlist -->
            <form method="post" action="add_to_cart.php" class="action-buttons">
                <input type="hidden" name="productID" value="<?= $product['productID'] ?>">
                <input type="hidden" name="quantity" id="hidden-quantity" value="1">

                <button type="submit" class="add-to-cart-btn">Add to Cart</button>

                <!-- Wishlist Icon (PNG as clickable button or link) -->
                <a href="add_to_wishlist.php?productID=<?= $product['productID'] ?>" class="wishlist-icon" title="Add to Wishlist">
                    <img src="/images/wishlist.png" alt="Wishlist" class="w-10 h-10">
                </a>
            </form>


            <a href="<?= $product['categoryName'] ?>.php" class="back-link">← Back to <?= ucfirst($product['categoryName']) ?></a>
        </div>

    </div>

    <!-- Review Section -->
    <div class="review-actions">
        <a href="#" id="toggle-reviews">-- View Reviews --</a> |
        <a href="#" id="toggle-form">-- Give Review --</a>
    </div>

    <div id="review-form" class="hidden">
        <form method="post" action="submit_review.php">
            <input type="hidden" name="productID" value="<?= $product['productID'] ?>">

            <!-- Star Rating -->
            <div class="star-rating">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" required>
                    <label for="star<?= $i ?>">★</label>
                <?php endfor; ?>
            </div>

            <!-- Comment Box -->
            <textarea name="reviewComment" placeholder="Write your review..." required></textarea>

            <button type="submit">Submit Review</button>
        </form>
    </div>

    <!-- Past reviews -->
    <div id="reviews-container" class="hidden">
        <h3>Customer Reviews</h3>

        <?php if (empty($reviews)): ?>
            <p>No reviews yet.</p>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review">
                    <p><strong><?= htmlspecialchars($review['username']) ?></strong>
                        (<?= htmlspecialchars($review['created_at']) ?>)</p>

                    <p>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?= $i <= $review['rating'] ? '★' : '☆' ?>
                        <?php endfor; ?>
                    </p>

                    <p><?= nl2br(htmlspecialchars($review['reviewComment'])) ?></p>
                </div>
                <hr>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>



</div>

<!-- Update price based on quantity -->
<script>
    const unitPrice = parseFloat(document.getElementById("unit-price").innerText);
    const quantityInput = document.getElementById("quantity");
    const totalPriceDisplay = document.getElementById("total-price");
    const hiddenQuantity = document.getElementById("hidden-quantity");

    function updateTotalPrice() {
        let quantity = parseInt(quantityInput.value) || 1;
        if (quantity < 1) quantity = 1;
        const total = (unitPrice * quantity).toFixed(2);
        totalPriceDisplay.innerText = total;
        hiddenQuantity.value = quantity;
    }

    function increaseQuantity() {
        quantityInput.value = parseInt(quantityInput.value || 1) + 1;
        updateTotalPrice();
    }

    function decreaseQuantity() {
        if (parseInt(quantityInput.value) > 1) {
            quantityInput.value = parseInt(quantityInput.value) - 1;
            updateTotalPrice();
        }
    }

    // Update on manual input too
    quantityInput.addEventListener('input', updateTotalPrice);
</script>

<!-- Review hide -->
<script>
    const toggleReviewLink = document.getElementById("toggle-reviews");
    const reviewBox = document.getElementById("reviews-container");

    toggleReviewLink.addEventListener("click", function(e) {
        e.preventDefault();
        reviewBox.classList.toggle("hidden");
        toggleReviewLink.textContent = reviewBox.classList.contains("hidden") ? "-- View Reviews --" : "-- Hide Reviews --";
    });

    const toggleFormLink = document.getElementById("toggle-form");
    const reviewForm = document.getElementById("review-form");

    toggleFormLink.addEventListener("click", function(e) {
        e.preventDefault();
        reviewForm.classList.toggle("hidden");
        toggleFormLink.textContent = reviewForm.classList.contains("hidden") ? "-- Give Review --" : "-- Hide Review Form --";
    });
</script>

<!-- After submitting review -->
<script>
    document.querySelector("#review-form form").addEventListener("submit", async function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        const response = await fetch('submit_review.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        const messageDiv = document.createElement("p");
        messageDiv.textContent = result.message;
        messageDiv.style.color = result.success ? "green" : "red";

        this.prepend(messageDiv);

        if (result.success) {
            this.reset();
        }
    });
</script>