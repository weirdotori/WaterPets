<?php
session_start();
require 'db.php';

if (empty($_SESSION['user'])) {
    header('Location: userLogin.php');
    exit;
}

$userID = $_SESSION['user']['userID'];

// Get wishlist products for this user, join with products table
$stmt = $conn->prepare("
    SELECT p.* 
    FROM products p
    INNER JOIN wishlist w ON p.productID = w.productID
    WHERE w.userID = ?
");
$stmt->execute([$userID]);
$wishlistProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your Wishlist - WaterPets</title>
    <link rel="stylesheet" href="/css/wishlist_style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <section class="wishlist-section">
        <?php include 'header.php'; ?>
        <hr class="header-divider">

        <div class="page-container">
            <h1>Your Wishlist</h1>

            <?php if (empty($wishlistProducts)): ?>
                <div class="empty-wishlist">
                    <img src="/images/wishlist.png" alt="Empty Wishlist" class="empty-wishlist-img">
                    <h2>Your Wishlist is Empty</h2>
                    <p>Browse our collection and add your favorite aquatic products to your wishlist!</p>
                    <a href="shop.php" class="btn-continue-shopping">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="product-grid">
                    <?php foreach ($wishlistProducts as $product): ?>
                        <div class="product-card">
                            <a href="fish.php?id=<?= $product['productID'] ?>">
                                <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['pName']) ?>" class="product-image">
                            </a>
                            <div class="card-body">
                                <h6><?= htmlspecialchars($product['pName']) ?></h6>
                                <p><?= htmlspecialchars($product['description']) ?></p>
                                <div class="price">$<?= number_format($product['price'], 2) ?></div>

                                <div class="card-actions">
                                    <form method="post" action="add_to_cart.php">
                                        <input type="hidden" name="productID" value="<?= $product['productID'] ?>">
                                        <?php if ($product['stock'] > 0): ?>
                                            <button type="submit" class="btn-add-cart">Add to Cart</button>
                                        <?php else: ?>
                                            <button type="submit" disabled class="btn-disabled" title="Out of stock">Out of Stock</button>
                                        <?php endif; ?>
                                    </form>

                                    <form method="post" action="remove_from_wishlist.php">
                                        <input type="hidden" name="productID" value="<?= $product['productID'] ?>">
                                        <button type="submit" class="remove-wishlist-btn" title="Remove from Wishlist">×</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>

</html>