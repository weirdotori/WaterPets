<?php
require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pName = $_POST['pName'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $categoryID = $_POST['categoryID'];

    // Handle file upload
    $imagePath = null;
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/products/"; // folder to save uploads
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true); // create folder if it doesn't exist
        }

        $fileName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;

        // Move uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $targetFile; // Save relative path to DB
        }
    }

    // Insert into DB
    $stmt = $conn->prepare("
        INSERT INTO products (pName, description, price, stock, categoryID, image, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$pName, $description, $price, $stock, $categoryID, $imagePath]);

    header("Location: ?page=manage_products&success=1");
    exit;
}
?>

<h2>Add New Product</h2>
<form method="POST" enctype="multipart/form-data" class="mb-3">
    <div class="mb-3">
        <label>Product Name</label>
        <input type="text" name="pName" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control" required></textarea>
    </div>

    <div class="mb-3">
        <label>Price</label>
        <input type="number" name="price" step="0.01" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Stock</label>
        <input type="number" name="stock" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Category</label>
        <select name="categoryID" class="form-select" required>
            <?php
            $cats = $conn->query("SELECT categoryID, cName FROM categories")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($cats as $cat) {
                echo "<option value='{$cat['categoryID']}'>{$cat['cName']}</option>";
            }
            ?>
        </select>
    </div>

    <div class="mb-3">
        <label>Product Image</label>
        <input type="file" name="image" class="form-control" accept="image/*">
    </div>

    <button type="submit" class="btn btn-primary">Add Product</button>
    <a href="?page=manage_products" class="btn btn-secondary">Cancel</a>
</form>
