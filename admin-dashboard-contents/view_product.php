<?php
require_once "db.php";

// Get ID from URL
$productID = $_GET['id'] ?? null;
if (!$productID) {
    echo "<div class='alert-danger'>No product selected.</div>";
    exit;
}

// Fetch product
$stmt = $conn->prepare("
    SELECT p.productID, p.pName, p.description, p.price, p.stock, p.created_at, p.image, c.cName
    FROM products p
    JOIN categories c ON p.categoryID = c.categoryID
    WHERE p.productID = ?
");
$stmt->execute([$productID]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<div class='alert-danger'>Product not found.</div>";
    exit;
}
?>

<h2 class="section-title">Product Details</h2>
<hr>

<div class="product-details">
    <!-- Left: Big Image -->
    <div class="product-image">
        <img src="<?= htmlspecialchars($product['image']) ?>" 
             alt="<?= htmlspecialchars($product['pName']) ?>" 
             class="product-img-big">
    </div>

    <!-- Right: Product Info -->
    <div class="product-info-right">
        <h3 class="product-title"><?= htmlspecialchars($product['pName']) ?></h3>
        <p class="product-category"><?= htmlspecialchars($product['cName']) ?></p>

        <h4 class="product-price">$<?= number_format($product['price'], 2) ?></h4>

        <p class="product-desc"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

        <p class="product-stock">
            <strong>Stock:</strong>
            <?php if ($product['stock'] > 20): ?>
                <span class="badge stock-high"><?= $product['stock'] ?> Units</span>
            <?php elseif ($product['stock'] > 0): ?>
                <span class="badge stock-low"><?= $product['stock'] ?> Units</span>
            <?php else: ?>
                <span class="badge stock-out">Out of Stock</span>
            <?php endif; ?>
        </p>

        <p><strong>Created At:</strong> <?= date('Y-m-d', strtotime($product['created_at'])) ?></p>

        <a href="adminDashboard.php?page=manage_products" class="btn btn-secondary">Back</a>
        <a href="adminDashboard.php?page=edit_product&id=<?= $product['productID'] ?>" class="btn btn-primary">Edit Product</a>
    </div>
</div>
