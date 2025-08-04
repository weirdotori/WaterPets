<?php
require_once "db.php"; // Adjust path if needed

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productID   = $_POST['productID'] ?? null;
    $pName       = $_POST['pName'] ?? '';
    $description = $_POST['description'] ?? '';
    $price       = $_POST['price'] ?? 0;
    $stock       = $_POST['stock'] ?? 0;
    $categoryID  = $_POST['categoryID'] ?? null;

    if (!$productID) {
        echo "<div class='alert alert-danger'>Invalid product ID.</div>";
        exit;
    }

    // Handle image upload (optional)
    $imagePath = null;
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/products/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $targetFile;
        }
    }

    // Update query
    if ($imagePath) {
        $stmt = $conn->prepare("
            UPDATE products
            SET pName = ?, description = ?, price = ?, stock = ?, categoryID = ?, image = ?
            WHERE productID = ?
        ");
        $success = $stmt->execute([$pName, $description, $price, $stock, $categoryID, $imagePath, $productID]);
    } else {
        $stmt = $conn->prepare("
            UPDATE products
            SET pName = ?, description = ?, price = ?, stock = ?, categoryID = ?
            WHERE productID = ?
        ");
        $success = $stmt->execute([$pName, $description, $price, $stock, $categoryID, $productID]);
    }

    if ($success) {
        echo "<script>alert('Product updated successfully!'); window.location.href='adminDashboard.php?page=manage_products';</script>";
        exit;
    } else {
        echo "<div class='alert alert-danger'>Failed to update product.</div>";
    }
}

// If not submitted, show edit form
$productID = $_GET['id'] ?? null;
if (!$productID) {
    echo "<div class='alert alert-danger'>No product selected.</div>";
    exit;
}

$stmt = $conn->prepare("
    SELECT productID, pName, description, price, stock, image, categoryID
    FROM products
    WHERE productID = ?
");
$stmt->execute([$productID]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<div class='alert alert-danger'>Product not found.</div>";
    exit;
}
?>

<h2>Edit Product</h2>
<p>Update the product details below:</p>

<form method="POST" enctype="multipart/form-data" class="mt-3">
    <input type="hidden" name="productID" value="<?= htmlspecialchars($product['productID']) ?>">

    <div class="mb-3">
        <label class="form-label">Product Name</label>
        <input type="text" name="pName" value="<?= htmlspecialchars($product['pName']) ?>" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" required><?= htmlspecialchars($product['description']) ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Price ($)</label>
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']) ?>" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Stock</label>
        <input type="number" name="stock" value="<?= htmlspecialchars($product['stock']) ?>" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Category</label>
        <select name="categoryID" class="form-select" required>
            <?php
            $cats = $conn->query("SELECT categoryID, cName FROM categories")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($cats as $cat) {
                $selected = ($cat['categoryID'] == $product['categoryID']) ? 'selected' : '';
                echo "<option value='{$cat['categoryID']}' $selected>{$cat['cName']}</option>";
            }
            ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Current Image</label><br>
        <img src="<?= htmlspecialchars($product['image']) ?>" alt="Product" style="width:100px; height:100px; object-fit:cover;">
    </div>

    <div class="mb-3">
        <label class="form-label">Change Image</label>
        <input type="file" name="image" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Save Changes</button>
    <a href="adminDashboard.php?page=manage_products" class="btn btn-secondary">Cancel</a>
</form>
