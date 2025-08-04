<?php
require_once "db.php";

// Get ID from URL
$productID = $_GET['id'] ?? null;
if (!$productID) {
    echo "<div class='alert alert-danger'>No product selected.</div>";
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
    echo "<div class='alert alert-danger'>Product not found.</div>";
    exit;
}
?>

<h2>Product Details</h2>
<hr>

<div class="row">
    <!-- Left: Big Image -->
    <div class="col-md-5">
        <img src="<?= htmlspecialchars($product['image']) ?>" 
             alt="<?= htmlspecialchars($product['pName']) ?>" 
             class="img-fluid rounded shadow-sm" 
             style="max-height: 400px; object-fit: cover;">
    </div>

    <!-- Right: Product Info -->
    <div class="col-md-7">
        <h3 class="fw-bold"><?= htmlspecialchars($product['pName']) ?></h3>
        <p class="text-muted"><?= htmlspecialchars($product['cName']) ?></p>
        
        <h4 class="text-primary">$<?= number_format($product['price'], 2) ?></h4>
        
        <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>

        <p>
            <strong>Stock:</strong> 
            <?php if ($product['stock'] > 20): ?>
                <span class="badge bg-primary"><?= $product['stock'] ?> Units</span>
            <?php elseif ($product['stock'] > 0): ?>
                <span class="badge bg-warning text-dark"><?= $product['stock'] ?> Units</span>
            <?php else: ?>
                <span class="badge bg-danger">Out of Stock</span>
            <?php endif; ?>
        </p>

        <p><strong>Created At:</strong> <?= date('Y-m-d', strtotime($product['created_at'])) ?></p>

        <a href="adminDashboard.php?page=manage_products" class="btn btn-secondary">Back</a>
        <a href="adminDashboard.php?page=edit_product&id=<?= $product['productID'] ?>" class="btn btn-primary">Edit Product</a>
    </div>
</div>
