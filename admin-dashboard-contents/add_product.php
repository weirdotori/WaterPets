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
        (pName, description, price, stock, categoryID, waterType, difficulty, species, aggressionLevel, image, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $pName, $description, $price, $stock, $categoryID,
        $waterType, $difficulty, $species, $aggressionLevel, $imagePath
    ]);

    header("Location: ?page=manage_products&success=1");
    exit;
}
?>

<h2 class="section-title">Add New Product</h2>

<form method="POST" enctype="multipart/form-data" class="edit-product-form">
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
    <select name="waterType" required>
        <option value="">Select Water Type</option>
        <option value="Freshwater">Freshwater</option>
        <option value="Saltwater">Saltwater</option>
    </select>

    <label>Difficulty</label>
    <select name="difficulty" required>
        <option value="">Select Difficulty</option>
        <option value="Beginner">Beginner</option>
        <option value="Intermediate">Intermediate</option>
        <option value="Expert">Expert</option>
    </select>

    <label>Species</label>
    <input type="text" name="species" required>

    <label>Aggression Level</label>
    <select name="aggressionLevel" required>
        <option value="">Select Aggression Level</option>
        <option value="Peaceful">Peaceful</option>
        <option value="Semi-Aggressive">Semi-Aggressive</option>
        <option value="Aggressive">Aggressive</option>
    </select>

    <label>Product Image</label>
    <input type="file" name="image" accept="image/*">

    <button type="submit" class="btn btn-primary">Add Product</button>
    <a href="?page=manage_products" class="btn btn-secondary">Cancel</a>
</form>
