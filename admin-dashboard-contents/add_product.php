<?php
require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pName       = $_POST['pName'];
    $description = $_POST['description'];
    $price       = $_POST['price'];
    $stock       = $_POST['stock'];
    $categoryID  = $_POST['categoryID'];
    $waterType   = $_POST['waterType'];
    $difficulty  = $_POST['difficulty'];
    $species     = $_POST['species'];
    $aggressionLevel = $_POST['aggressionLevel'];
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

    // Handle file upload
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

    // Insert into DB
    $stmt = $conn->prepare("
        INSERT INTO products 
        (pName, description, price, stock, categoryID, waterType, difficulty, species, aggressionLevel, coralType, lighting, waterFlow, color, foodType, substrateType, supplies, filterType, heaterWatt, pumpSize, tankShape, equipment, image, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $pName, $description, $price, $stock, $categoryID,
        $waterType, $difficulty, $species, $aggressionLevel, $coralType, $lighting, $waterFlow, $color, $foodType, $substrateType, $supplies, $filterType, $heaterWatt, $pumpSize, $tankShape, $equipment, $imagePath
    ]);

    header("Location: ?page=manage_products&success=1");
    exit;
}
?>

<h2 class="section-title">Add New Product</h2>

<form method="POST" enctype="multipart/form-data" class="add-product-form">
    <label>Product Name</label>
    <input type="text" name="pName" required>

    <label>Description</label>
    <textarea name="description" required></textarea>

    <label>Price</label>
    <input type="number" name="price" step="0.01" required>

    <label>Stock</label>
    <input type="number" name="stock" required>

    <label>Category</label>
    <select name="categoryID" required>
        <?php
        $cats = $conn->query("SELECT categoryID, cName FROM categories")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cats as $cat) {
            echo "<option value='{$cat['categoryID']}'>{$cat['cName']}</option>";
        }
        ?>
    </select>

    <label>Water Type</label>
    <select name="waterType">
        <option value="">Select Water Type</option>
        <option value="Freshwater">Freshwater</option>
        <option value="Saltwater">Saltwater</option>
    </select>

    <label>Difficulty</label>
    <select name="difficulty">
        <option value="">Select Difficulty</option>
        <option value="Beginner">Beginner</option>
        <option value="Intermediate">Intermediate</option>
        <option value="Expert">Expert</option>
    </select>

    <label>Species</label>
    <input type="text" name="species">

    <label>Aggression Level</label>
    <select name="aggressionLevel">
        <option value="">Select Aggression Level</option>
        <option value="Peaceful">Peaceful</option>
        <option value="Semi-Aggressive">Semi-Aggressive</option>
        <option value="Aggressive">Aggressive</option>
    </select>

    <label>Coral Type</label>
    <select name="coralType">
        <option value="">Select Coral Type</option>
        <option value="Soft Coral">Soft Coral</option>
        <option value="LPS">LPS (Large Polyp Stony)</option>
        <option value="SPS">SPS (Small Polyp Stony)</option>
        <option value="Stone">Stone</option>
    </select>

    <label>Lighting</label>
    <select name="lighting">
        <option value="">Select Lighting</option>
        <option value="Low">Low</option>
        <option value="Moderate">Moderate</option>
        <option value="High">High</option>
    </select>

    <label>Water Flow</label>
    <select name="waterFlow">
        <option value="">Select Water Flow</option>
        <option value="Low">Low</option>
        <option value="Moderate">Moderate</option>
        <option value="High">High</option>
    </select>

    <label>Color</label>
    <input type="text" name="color">

    <label>Food Type</label>
    <select name="foodType">
        <option value="">Select Food Type</option>
        <option value="Flakes">Flakes</option>
        <option value="Pellets">Pellets</option>
        <option value="Freeze-dried">Freeze-dried</option>
        <option value="Frozen">Frozen</option>
    </select>
    
    <label>Substrate Type</label>
    <select name="substrateType">
        <option value="">Select Substrate Type</option>
        <option value="Sand">Sand</option>
        <option value="Gravel">Gravel</option>
        <option value="Soil">Soil</option>
    </select>

    <label>Supplies</label>
    <input type="text" name="supplies">

    <label>Filter Type</label>
    <select name="filterType">
        <option value="">Select Filter Type</option>
        <option value="Internal">Internal</option>
        <option value="External">External</option>
        <option value="Hang-on">Hang-on</option>
    </select>

    <label>Heater Watt</label>
    <select name="heaterWatt">
        <option value="">Select Heater Watt</option>
        <option value="Small (50–100W)">Small (50–100W)</option>
        <option value="Medium (100–200W)">Medium (100–200W)</option>
        <option value="Large (200+W)">Large (200+W)</option>
    </select>

    <label>Pump Size</label>
    <select name="pumpSize">
        <option value="">Select Pump Size</option>
        <option value="Small">Small</option>
        <option value="Medium">Medium</option>
        <option value="Large">Large</option>
    </select>

    <label>Tank Shape</label>
    <select name="tankShape">
        <option value="">Select Tank Shape</option>
        <option value="Round">Round</option>
        <option value="Square">Square</option>
        <option value="Others">Others</option>
    </select>

    <label>Equipment</label>
    <input type="text" name="equipment">

    <label>Product Image</label>
    <input type="file" name="image" accept="image/*">

    <button type="submit" class="btn btn-primary">Add Product</button>
    <a href="?page=manage_products" class="btn btn-secondary">Cancel</a>
</form>
