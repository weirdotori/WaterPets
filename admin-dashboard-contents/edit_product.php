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
    $waterType   = $_POST['waterType'] ?? '';
    $difficulty  = $_POST['difficulty'] ?? '';
    $species     = $_POST['species'] ?? '';
    $aggressionLevel = $_POST['aggressionLevel'] ?? '';
    $coralType = $_POST['coralType'] ?? '';
    $lighting = $_POST['lighting'] ?? '';
    $waterFlow = $_POST['waterFlow'] ?? '';
    $color = $_POST['color'] ?? '';

    if (!$productID) {
        echo "<div class='alert-danger'>Invalid product ID.</div>";
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
            SET pName = ?, description = ?, price = ?, stock = ?, categoryID = ?, waterType = ?, difficulty = ?, species = ?, aggressionLevel = ?, coralType = ?, lighting = ?, waterFlow = ?, color = ?, image = ?
            WHERE productID = ?
        ");
        $success = $stmt->execute([$pName, $description, $price, $stock, $categoryID, $waterType, $difficulty, $species, $aggressionLevel, $coralType, $lighting, $waterFlow, $color, $imagePath, $productID]);
    } else {
        $stmt = $conn->prepare("
            UPDATE products
            SET pName = ?, description = ?, price = ?, stock = ?, categoryID = ?, waterType = ?, difficulty = ?, species = ?, aggressionLevel = ?, coralType = ?, lighting = ?, waterFlow = ?, color = ?
            WHERE productID = ?
        ");
        $success = $stmt->execute([$pName, $description, $price, $stock, $categoryID, $waterType, $difficulty, $species, $aggressionLevel, $coralType, $lighting, $waterFlow, $color, $productID]);
    }

    if ($success) {
        echo "<script>alert('Product updated successfully!'); window.location.href='adminDashboard.php?page=manage_products';</script>";
        exit;
    } else {
        echo "<div class='alert-danger'>Failed to update product.</div>";
    }
}

// If not submitted, show edit form
$productID = $_GET['id'] ?? null;
if (!$productID) {
    echo "<div class='alert-danger'>No product selected.</div>";
    exit;
}

$stmt = $conn->prepare("
    SELECT productID, pName, description, price, stock, image, categoryID, waterType, difficulty, species, aggressionLevel, coralType, lighting, waterFlow, color
    FROM products
    WHERE productID = ?
");
$stmt->execute([$productID]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<div class='alert-danger'>Product not found.</div>";
    exit;
}
?>

<h2 class="section-title">Edit Product</h2>
<p>Update the product details below:</p>

<form method="POST" enctype="multipart/form-data" class="edit-product-form">
    <input type="hidden" name="productID" value="<?= htmlspecialchars($product['productID']) ?>">

    <label>Product Name</label>
    <input type="text" name="pName" value="<?= htmlspecialchars($product['pName']) ?>" required>

    <label>Description</label>
    <textarea name="description" required><?= htmlspecialchars($product['description']) ?></textarea>

    <label>Price ($)</label>
    <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>

    <label>Stock</label>
    <input type="number" name="stock" value="<?= htmlspecialchars($product['stock']) ?>" required>

    <label>Category</label>
    <select name="categoryID" required>
        <?php
        $cats = $conn->query("SELECT categoryID, cName FROM categories")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cats as $cat) {
            $selected = ($cat['categoryID'] == $product['categoryID']) ? 'selected' : '';
            echo "<option value='{$cat['categoryID']}' $selected>{$cat['cName']}</option>";
        }
        ?>
    </select>

    <!-- Category Fields -->
    <label>Water Type</label>
    <select name="waterType">
        <option value="">Select Water Type</option>
        <option value="Freshwater" <?= ($product['waterType'] == 'Freshwater') ? 'selected' : '' ?>>Freshwater</option>
        <option value="Saltwater" <?= ($product['waterType'] == 'Saltwater') ? 'selected' : '' ?>>Saltwater</option>
    </select>

    <label>Difficulty</label>
    <select name="difficulty">
        <option value="">Select Difficulty</option>
        <option value="Beginner" <?= ($product['difficulty'] == 'Beginner') ? 'selected' : '' ?>>Beginner</option>
        <option value="Intermediate" <?= ($product['difficulty'] == 'Intermediate') ? 'selected' : '' ?>>Intermediate</option>
        <option value="Expert" <?= ($product['difficulty'] == 'Expert') ? 'selected' : '' ?>>Expert</option>
    </select>

    <label>Species</label>
    <input type="text" name="species" value="<?= htmlspecialchars($product['species']) ?>">

    <label>Aggression Level</label>
    <select name="aggressionLevel">
        <option value="">Select Aggression Level</option>
        <option value="Peaceful" <?= ($product['aggressionLevel'] == 'Peaceful') ? 'selected' : '' ?>>Peaceful</option>
        <option value="Semi-Aggressive" <?= ($product['aggressionLevel'] == 'Semi-Aggressive') ? 'selected' : '' ?>>Semi-Aggressive</option>
        <option value="Aggressive" <?= ($product['aggressionLevel'] == 'Aggressive') ? 'selected' : '' ?>>Aggressive</option>
    </select>

    <label>Coral Type</label>
    <select name="coralType">
        <option value="">Select Coral Type</option>
        <option value="Soft Coral" <?= ($product['coralType'] == 'Soft Coral') ? 'selected' : '' ?>>Soft Coral</option>
        <option value="LPS" <?= ($product['coralType'] == 'LPS') ? 'selected' : '' ?>>LPS (Large Polyp Stony)</option>
        <option value="SPS" <?= ($product['coralType'] == 'SPS') ? 'selected' : '' ?>>SPS (Small Polyp Stony)</option>
        <option value="Stone" <?= ($product['coralType'] == 'Stone') ? 'selected' : '' ?>>Stone</option>
    </select>

    <label>Lighting</label>
    <select name="lighting">
        <option value="">Select Lighting</option>
        <option value="Low" <?= ($product['lighting'] == 'Low') ? 'selected' : '' ?>>Low</option>
        <option value="Moderate" <?= ($product['lighting'] == 'Moderate') ? 'selected' : '' ?>>Moderate</option>
        <option value="High" <?= ($product['lighting'] == 'High') ? 'selected' : '' ?>>High</option>
    </select>

    <label>Water Flow</label>
    <select name="waterFlow">
        <option value="">Select Water Flow</option>
        <option value="Low" <?= ($product['waterFlow'] == 'Low') ? 'selected' : '' ?>>Low</option>
        <option value="Moderate" <?= ($product['waterFlow'] == 'Moderate') ? 'selected' : '' ?>>Moderate</option>
        <option value="High" <?= ($product['waterFlow'] == 'High') ? 'selected' : '' ?>>High</option>
    </select>

    <label>Color</label>
    <input type="text" name="color" value="<?= htmlspecialchars($product['color']) ?>">

    <label>Current Image</label>
    <img src="<?= htmlspecialchars($product['image']) ?>" alt="Product" class="product-edit-img">

    <label>Change Image</label>
    <input type="file" name="image">

    <button type="submit" class="btn btn-primary">Save Changes</button>
    <a href="adminDashboard.php?page=manage_products" class="btn btn-secondary">Cancel</a>
</form>
