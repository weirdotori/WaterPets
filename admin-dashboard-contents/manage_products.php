<?php
require_once "db.php"; // Adjust path if needed

// 1. Handle Delete Request
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM products WHERE productID = ?");
    if ($stmt->execute([$delete_id])) {
        $_SESSION['msg'] = "Product deleted successfully.";
    } else {
        $_SESSION['msg'] = "Error deleting product.";
    }
    // no header() redirect here, will reload page naturally
}

$where = [];
$params = [];

// Search filter
if (!empty($_GET['search'])) {
    $where[] = "p.pName LIKE ?";
    $params[] = "%" . $_GET['search'] . "%";
}

// Category filter
if (!empty($_GET['category'])) {
    $where[] = "p.categoryID = ?";
    $params[] = $_GET['category'];
}

// Stock filter
if (!empty($_GET['stock'])) {
    if ($_GET['stock'] === 'high') {
        $where[] = "p.stock > 5";
    } elseif ($_GET['stock'] === 'low') {
        $where[] = "p.stock > 0 AND p.stock <= 5";
    } elseif ($_GET['stock'] === 'out') {
        $where[] = "p.stock = 0";
    }
}



// Build SQL
$sql = "
    SELECT p.productID, p.pName, p.description, p.price, p.stock, p.created_at, p.image, c.cName
    FROM products p
    JOIN categories c ON p.categoryID = c.categoryID
";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 class="section-title">Product Catalog</h2>
<?php if (isset($_SESSION['msg'])): ?>
    <div class="alert-msg"><?= $_SESSION['msg'];
                            unset($_SESSION['msg']); ?></div>
<?php endif; ?>

<!-- Search + Filters -->
<form method="GET" class="filter-form">
    <input type="hidden" name="page" value="manage_products">

    <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
        class="input-text" placeholder="Search products...">

    <select name="category" class="input-select">
        <option value="">All Categories</option>
        <?php
        $cats = $conn->query("SELECT categoryID, cName FROM categories")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cats as $cat) {
            $selected = ($_GET['category'] ?? '') == $cat['categoryID'] ? 'selected' : '';
            echo "<option value='{$cat['categoryID']}' $selected>{$cat['cName']}</option>";
        }
        ?>
    </select>

    <select name="stock" class="input-select">
        <option value="">All Stock</option>
        <option value="high" <?= ($_GET['stock'] ?? '') === 'high' ? 'selected' : '' ?>>High Stock</option>
        <option value="low" <?= ($_GET['stock'] ?? '') === 'low' ? 'selected' : '' ?>>Low Stock</option>
        <option value="out" <?= ($_GET['stock'] ?? '') === 'out' ? 'selected' : '' ?>>Out of Stock</option>
    </select>

    <button type="submit" class="btn btn-primary">Filter</button>

    <a href="?page=add_product" class="btn btn-success">
        ➕ Add New Product
    </a>
</form>

<!-- Table -->
<table class="custom-table">
    <thead>
        <tr>
            <th><input type="checkbox"></th>
            <th>Product</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Created</th>
            <th class="text-right">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><input type="checkbox"></td>
                    <td class="product-info">
                        <img src="<?= htmlspecialchars($product['image']) ?>"
                            alt="<?= htmlspecialchars($product['pName']) ?>"
                            class="product-img">
                        <span><?= htmlspecialchars($product['pName']) ?></span>
                    </td>
                    <td><span class="badge"><?= htmlspecialchars($product['cName']) ?></span></td>
                    <td>$<?= number_format($product['price'], 2) ?></td>
                    <td>
                        <?php if ($product['stock'] > 5): ?>
                            <span class="badge stock-high"><?= $product['stock'] ?> Units</span>
                        <?php elseif ($product['stock'] > 0 && $product['stock'] <= 5): ?>
                            <span class="badge stock-low"><?= $product['stock'] ?> Units</span>
                        <?php elseif ($product['stock'] == 0): ?>
                            <span class="badge stock-out">Out of Stock</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('Y-m-d', strtotime($product['created_at'])) ?></td>
                    <td class="text-right">
                        <div class="action-menu">
                            <a href="?page=view_product&id=<?= $product['productID'] ?>" class="action-link">View</a>
                            <a href="?page=edit_product&id=<?= $product['productID'] ?>" class="action-link">Edit</a>
                            <a href="?page=manage_products&delete_id=<?= $product['productID'] ?>"
                                onclick="return confirm('Are you sure you want to delete this product?');"
                                class="action-link delete">
                                Delete
                            </a>


                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="no-data">No products found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>