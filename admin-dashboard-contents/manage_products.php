<?php
require_once "db.php"; // Adjust path if needed

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



<h2 class="mb-4">Product Catalog</h2>

<!-- Search + Filters -->
<form method="GET" class="d-flex flex-wrap gap-2 mb-3">
    <input type="hidden" name="page" value="manage_products"> <!-- so dashboard stays on same page -->

    <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
        class="form-control" style="max-width:250px;" placeholder="Search products...">

    <select name="category" class="form-select" style="max-width:200px;">
        <option value="">All Categories</option>
        <?php
        $cats = $conn->query("SELECT categoryID, cName FROM categories")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cats as $cat) {
            $selected = ($_GET['category'] ?? '') == $cat['categoryID'] ? 'selected' : '';
            echo "<option value='{$cat['categoryID']}' $selected>{$cat['cName']}</option>";
        }
        ?>
    </select>

    <select name="stock" class="form-select" style="max-width:200px;">
        <option value="">All Stock</option>
        <option value="high" <?= ($_GET['stock'] ?? '') === 'high' ? 'selected' : '' ?>>High Stock</option>
        <option value="low" <?= ($_GET['stock'] ?? '') === 'low' ? 'selected' : '' ?>>Low Stock</option>
        <option value="out" <?= ($_GET['stock'] ?? '') === 'out' ? 'selected' : '' ?>>Out of Stock</option>
    </select>



    <button type="submit" class="btn btn-primary">Filter</button>

    <!-- Add product button -->
    <a href="?page=add_product" class="btn btn-success">
        <i class="bi bi-plus-lg"></i> Add New Product
    </a>

</form>


<div class="table-responsive">
    <table class="table align-middle table-hover">
        <thead class="table-light">
            <tr>
                <th scope="col"><input type="checkbox"></th>
                <th scope="col">Product</th>
                <th scope="col">Category</th>
                <th scope="col">Price</th>
                <th scope="col">Stock</th>
                <th scope="col">Created</th>
                <th scope="col" class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><input type="checkbox"></td>
                        <!-- Product + SKU -->
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="<?= htmlspecialchars($product['image']) ?>"
                                    alt="<?= htmlspecialchars($product['pName']) ?>"
                                    class="rounded me-2"
                                    style="width:40px; height:40px; object-fit:cover;">
                                <div>
                                    <div class="fw-semibold"><?= htmlspecialchars($product['pName']) ?></div>
                                </div>
                            </div>
                        </td>

                        <!-- Category -->
                        <td>
                            <span class="badge bg-light text-dark"><?= htmlspecialchars($product['cName']) ?></span>
                        </td>

                        <!-- Price -->
                        <td>$<?= number_format($product['price'], 2) ?></td>

                        <!-- Stock -->
                        <td>
                            <?php if ($product['stock'] > 5): ?>
                                <span class="badge bg-warning text-dark"><?= $product['stock'] ?> Units</span> <!-- Yellow -->
                            <?php elseif ($product['stock'] > 0 && $product['stock'] <= 5): ?>
                                <span class="badge bg-danger"><?= $product['stock'] ?> Units</span> <!-- Red -->
                            <?php elseif ($product['stock'] == 0): ?>
                                <span class="badge bg-secondary">Out of Stock</span> <!-- Gray -->
                            <?php endif; ?>
                        </td>




                        <!-- Created Date -->
                        <td><?= date('Y-m-d', strtotime($product['created_at'])) ?></td>

                        <!-- Actions Dropdown -->
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="?page=view_product&id=<?= $product['productID'] ?>">View Details</a></li>
                                    <li><a class="dropdown-item" href="?page=edit_product&id=<?= $product['productID'] ?>">Edit</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger"
                                            href="delete_product.php?id=<?= $product['productID'] ?>"
                                            onclick="return confirm('Are you sure you want to delete this product?');">
                                            Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center text-muted">No products found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap Icons for actions -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">