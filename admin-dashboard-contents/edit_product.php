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
    $foodType = $_POST['foodType'] ?? '';
    $substrateType = $_POST['substrateType'] ?? '';
    $supplies = $_POST['supplies'] ?? '';
    $filterType = $_POST['filterType'] ?? '';
    $heaterWatt = $_POST['heaterWatt'] ?? '';
    $pumpSize = $_POST['pumpSize'] ?? '';
    $tankShape = $_POST['tankShape'] ?? '';
    $equipment = $_POST['equipment'] ?? '';

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
            SET pName = ?, description = ?, price = ?, stock = ?, categoryID = ?, waterType = ?, difficulty = ?, species = ?, aggressionLevel = ?, coralType = ?, lighting = ?, waterFlow = ?, color = ?, foodType = ?, substrateType = ?, supplies = ?, filterType = ?, heaterWatt = ?, pumpSize = ?, tankShape = ?, equipment = ?, image = ?
            WHERE productID = ?
        ");
        $success = $stmt->execute([$pName, $description, $price, $stock, $categoryID, $waterType, $difficulty, $species, $aggressionLevel, $coralType, $lighting, $waterFlow, $color, $foodType, $substrateType, $supplies, $filterType, $heaterWatt, $pumpSize, $tankShape, $equipment, $imagePath, $productID]);
    } else {
        $stmt = $conn->prepare("
            UPDATE products
            SET pName = ?, description = ?, price = ?, stock = ?, categoryID = ?, waterType = ?, difficulty = ?, species = ?, aggressionLevel = ?, coralType = ?, lighting = ?, waterFlow = ?, color = ? foodType = ?, substrateType = ?, supplies = ?, filterType = ?, heaterWatt = ?, pumpSize = ?, tankShape = ?, equipment = ?
            WHERE productID = ?
        ");
        $success = $stmt->execute([$pName, $description, $price, $stock, $categoryID, $waterType, $difficulty, $species, $aggressionLevel, $coralType, $lighting, $waterFlow, $color, $foodType, $substrateType, $supplies, $filterType, $heaterWatt, $pumpSize, $tankShape, $equipment, $productID]);
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
    SELECT productID, pName, description, price, stock, image, categoryID, waterType, difficulty, species, aggressionLevel, coralType, lighting, waterFlow, color, foodType, substrateType, supplies, filterType, heaterWatt, pumpSize, tankShape, equipment
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

    <label>Food Type</label>
    <select name="foodType">
        <option value="">Select Food Type</option>
        <option value="Flakes" <?= ($product['foodType'] == 'Flakes') ? 'selected' : '' ?>>Flakes</option>
        <option value="Pellets" <?= ($product['foodType'] == 'Pellets') ? 'selected' : '' ?>>Pellets</option>
        <option value="Freeze-dried" <?= ($product['foodType'] == 'Freeze-dried') ? 'selected' : '' ?>>Freeze-dried</option>
        <option value="Frozen" <?= ($product['foodType'] == 'Frozen') ? 'selected' : '' ?>>Frozen</option>
    </select>
    
    <label>Substrate Type</label>
    <select name="substrateType">
        <option value="">Select Substrate Type</option>
        <option value="Sand" <?= ($product['substrateType'] == 'Sand') ? 'selected' : '' ?>>Sand</option>
        <option value="Gravel" <?= ($product['substrateType'] == 'Gravel') ? 'selected' : '' ?>>Gravel</option>
        <option value="Soil" <?= ($product['substrateType'] == 'Soil') ? 'selected' : '' ?>>Soil</option>
    </select>

    <label>Supplies</label>
    <input type="text" name="supplies" value="<?= htmlspecialchars($product['supplies']) ?>">

    <label>Filter Type</label>
    <select name="filterType">
        <option value="">Select Filter Type</option>
        <option value="Internal" <?= ($product['filterType'] == 'Internal') ? 'selected' : '' ?>>Internal</option>
        <option value="External" <?= ($product['filterType'] == 'External') ? 'selected' : '' ?>>External</option>
        <option value="Hang-on" <?= ($product['filterType'] == 'Hang-on') ? 'selected' : '' ?>>Hang-on</option>
    </select>

    <label>Heater Watt</label>
    <select name="heaterWatt">
        <option value="">Select Heater Watt</option>
        <option value="Small (50–100W)" <?= ($product['heaterWatt'] == 'Small (50–100W)') ? 'selected' : '' ?>>Small (50–100W)</option>
        <option value="Medium (100–200W)" <?= ($product['heaterWatt'] == 'Medium (100–200W)') ? 'selected' : '' ?>>Medium (100–200W)</option>
        <option value="Large (200+W)" <?= ($product['heaterWatt'] == 'Large (200+W)') ? 'selected' : '' ?>>Large (200+W)</option>
    </select>

    <label>Pump Size</label>
    <select name="pumpSize">
        <option value="">Select Pump Size</option>
        <option value="Small" <?= ($product['pumpSize'] == 'Small') ? 'selected' : '' ?>>Small</option>
        <option value="Medium" <?= ($product['pumpSize'] == 'Medium') ? 'selected' : '' ?>>Medium</option>
        <option value="Large" <?= ($product['pumpSize'] == 'Large') ? 'selected' : '' ?>>Large</option>
    </select>

    <label>Tank Shape</label>
    <select name="tankShape">
        <option value="">Select Tank Shape</option>
        <option value="Round" <?= ($product['tankShape'] == 'Round') ? 'selected' : '' ?>>Round</option>
        <option value="Square" <?= ($product['tankShape'] == 'Square') ? 'selected' : '' ?>>Square</option>
        <option value="Others" <?= ($product['tankShape'] == 'Others') ? 'selected' : '' ?>>Others</option>
    </select>

    <label>Equipment</label>
    <input type="text" name="equipment" value="<?= htmlspecialchars($product['equipment']) ?>">


    <label>Current Image</label>
    <img src="<?= htmlspecialchars($product['image']) ?>" alt="Product" class="product-edit-img">

    <label>Change Image</label>
    <input type="file" name="image">

    <button type="submit" class="btn btn-primary">Save Changes</button>
    <a href="adminDashboard.php?page=manage_products" class="btn btn-secondary">Cancel</a>
</form>
