<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "db.php";

// Find category ID for "Coral Reefs"
$categoryStmt = $conn->prepare("SELECT categoryID FROM categories WHERE cName = 'Coral Reefs'");
$categoryStmt->execute();
$categoryID = $categoryStmt->fetchColumn();

if (!$categoryID) {
    echo "<div class='container py-4'><p>No coral category found.</p></div>";
    exit;
}

// Get fish products
$query = "SELECT * FROM products WHERE categoryID = ?";
$params = [$categoryID];

// Coral Type
if (!empty($_GET['coralType'])) {
    $placeholders = implode(',', array_fill(0, count($_GET['coralType']), '?'));
    $query .= " AND coralType IN ($placeholders)";
    $params = array_merge($params, $_GET['coralType']);
}

// Difficulty
if (!empty($_GET['difficulty'])) {
    $placeholders = implode(',', array_fill(0, count($_GET['difficulty']), '?'));
    $query .= " AND difficulty IN ($placeholders)";
    $params = array_merge($params, $_GET['difficulty']);
}

// Lighting Level
if (!empty($_GET['lighting'])) {
    $placeholders = implode(',', array_fill(0, count($_GET['lighting']), '?'));
    $query .= " AND lighting IN ($placeholders)";
    $params = array_merge($params, $_GET['lighting']);
}

// Water Flow
if (!empty($_GET['waterFlow'])) {
    $placeholders = implode(',', array_fill(0, count($_GET['waterFlow']), '?'));
    $query .= " AND waterFlow IN ($placeholders)";
    $params = array_merge($params, $_GET['waterFlow']);
}

// Color filter (single value)
if (!empty($_GET['color'])) {
    $query .= " AND color = ?";
    $params[] = $_GET['color'];
}

// Search by product name if ?search= is present
if (!empty($_GET['search'])) {
    $query .= " AND pName LIKE ?";
    $params[] = "%" . $_GET['search'] . "%";
}

// Sorting
if (!empty($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price_asc':
            $query .= " ORDER BY price ASC";
            break;
        case 'price_desc':
            $query .= " ORDER BY price DESC";
            break;
        case 'date_new':
            $query .= " ORDER BY created_at DESC"; // assumes you have a created_at column
            break;
        case 'date_old':
            $query .= " ORDER BY created_at ASC";
            break;
        case 'popular':
            // Join with order_details to count orders
            $query = "
        SELECT p.*, COALESCE(SUM(od.orderQty), 0) AS total_sold
        FROM products p
        LEFT JOIN order_details od ON p.productID = od.productID
        WHERE p.categoryID = ?
    ";

            $params = [$categoryID]; // reset params


            // Apply other filters manually if needed
            if (!empty($_GET['coralType'])) {
                $placeholders = implode(',', array_fill(0, count($_GET['coralType']), '?'));
                $query .= " AND p.coralType IN ($placeholders)";
                $params = array_merge($params, $_GET['coralType']);
            }
            if (!empty($_GET['difficulty'])) {
                $placeholders = implode(',', array_fill(0, count($_GET['difficulty']), '?'));
                $query .= " AND p.difficulty IN ($placeholders)";
                $params = array_merge($params, $_GET['difficulty']);
            }
            if (!empty($_GET['lighting'])) {
                $placeholders = implode(',', array_fill(0, count($_GET['lighting']), '?'));
                $query .= " AND p.lighting IN ($placeholders)";
                $params = array_merge($params, $_GET['lighting']);
            }
            if (!empty($_GET['waterFlow'])) {
                $placeholders = implode(',', array_fill(0, count($_GET['waterFlow']), '?'));
                $query .= " AND p.waterFlow IN ($placeholders)";
                $params = array_merge($params, $_GET['waterFlow']);
            }
            if (!empty($_GET['color'])) {
                $query .= " AND p.color = ?";
                $params[] = $_GET['color'];
            }
            if (!empty($_GET['search'])) {
                $query .= " AND p.pName LIKE ?";
                $params[] = "%" . $_GET['search'] . "%";
            }

            $query .= " GROUP BY p.productID ORDER BY total_sold DESC";
            break;
    }
}

$stmt = $conn->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Coral Reefs - WaterPets</title>

    <!-- Css Style -->
    <link rel="stylesheet" href="/css/shop_style.css">

    <!-- Tailwind css -->
    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body>

    <section class="fish-section">
        <?php include 'header.php'; ?>
        <hr class="header-divider">

        <div class="page-container">

            <!-- Sidebar Filters -->
            <?php if (empty($_GET['id'])): ?>
                <aside class="filter-sidebar">
                    <form method="GET" action="coralreefs.php">
                        <h5>Filter Options</h5>

                        <!-- By Coral Type -->
                        <div class="filter-group">
                            <h6>By Coral Type</h6>
                            <label><input type="checkbox" name="coralType[]" value="Soft Coral" <?= in_array('Soft Coral', $_GET['coralType'] ?? []) ? 'checked' : '' ?>> Soft Coral</label> <br>
                            <label><input type="checkbox" name="coralType[]" value="LPS" <?= in_array('LPS', $_GET['coralType'] ?? []) ? 'checked' : '' ?>> LPS</label> <br>
                            <label><input type="checkbox" name="coralType[]" value="SPS" <?= in_array('SPS', $_GET['coralType'] ?? []) ? 'checked' : '' ?>> SPS</label> <br>
                            <label><input type="checkbox" name="coralType[]" value="Stone" <?= in_array('Stone', $_GET['coralType'] ?? []) ? 'checked' : '' ?>> Stone</label>
                        </div>

                        <!-- By Difficulty -->
                        <div class="filter-group">
                            <h6>By Difficulty</h6>
                            <label><input type="checkbox" name="difficulty[]" value="Beginner" <?= in_array('Beginner', $_GET['difficulty'] ?? []) ? 'checked' : '' ?>> Beginner</label> <br>
                            <label><input type="checkbox" name="difficulty[]" value="Intermediate" <?= in_array('Intermediate', $_GET['difficulty'] ?? []) ? 'checked' : '' ?>> Intermediate</label> <br>
                            <label><input type="checkbox" name="difficulty[]" value="Expert" <?= in_array('Expert', $_GET['difficulty'] ?? []) ? 'checked' : '' ?>> Expert</label>
                        </div>

                        <!-- By Lighting Level -->
                        <div class="filter-group">
                            <h6>By Lighting Level</h6>
                            <label><input type="checkbox" name="lighting[]" value="Low" <?= in_array('Low', $_GET['lighting'] ?? []) ? 'checked' : '' ?>> Low</label> <br>
                            <label><input type="checkbox" name="lighting[]" value="Moderate" <?= in_array('Moderate', $_GET['lighting'] ?? []) ? 'checked' : '' ?>> Moderate</label> <br>
                            <label><input type="checkbox" name="lighting[]" value="High" <?= in_array('High', $_GET['lighting'] ?? []) ? 'checked' : '' ?>> High</label>
                        </div>

                        <!-- By Water Flow -->
                        <div class="filter-group">
                            <h6>By Water FLow</h6>
                            <label><input type="checkbox" name="waterFlow[]" value="Low" <?= in_array('Low', $_GET['waterFlow'] ?? []) ? 'checked' : '' ?>> Low</label> <br>
                            <label><input type="checkbox" name="waterFlow[]" value="Moderate" <?= in_array('Moderate', $_GET['waterFlow'] ?? []) ? 'checked' : '' ?>> Moderate</label> <br>
                            <label><input type="checkbox" name="waterFlow[]" value="High" <?= in_array('High', $_GET['waterFlow'] ?? []) ? 'checked' : '' ?>> High</label>
                        </div>

                        <button type="submit" class="filter-btn">Apply Filters</button>
                    </form>
                </aside>
            <?php endif; ?>


            <!-- Main Shop Area -->
            <section class="shop-main <?= !empty($_GET['id']) ? 'w-full' : '' ?>">

                <?php if (empty($_GET['id'])): ?>
                    <form method="GET" action="coralreefs.php" class="search-sort">
                        <!-- Keep all existing filter params in hidden fields -->
                        <?php foreach ($_GET as $key => $value): ?>
                            <?php if ($key !== 'sort'): ?>
                                <?php if (is_array($value)): ?>
                                    <?php foreach ($value as $v): ?>
                                        <input type="hidden" name="<?= htmlspecialchars($key) ?>[]" value="<?= htmlspecialchars($v) ?>">
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <select name="sort" onchange="this.form.submit()" class="sort-dropdown">
                            <option value="">Default Sorting</option>
                            <option value="price_asc" <?= ($_GET['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="price_desc" <?= ($_GET['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                            <option value="date_new" <?= ($_GET['sort'] ?? '') === 'date_new' ? 'selected' : '' ?>>Newest First</option>
                            <option value="date_old" <?= ($_GET['sort'] ?? '') === 'date_old' ? 'selected' : '' ?>>Oldest First</option>
                            <option value="popular" <?= ($_GET['sort'] ?? '') === 'popular' ? 'selected' : '' ?>>Most Popular</option>
                        </select>
                    </form>

                <?php endif; ?>

                <?php $selectedcolor = $_GET['color'] ?? ''; ?>

                <?php if (empty($_GET['id'])): ?>
                    <div class="color-buttons">
                        <a href="coralreefs.php" class="<?= $selectedcolor === '' ? 'active' : '' ?>">All</a>
                        <a href="coralreefs.php?color=Blue" class="<?= $selectedcolor === 'Blue' ? 'active' : '' ?>">Blue</a>
                        <a href="coralreefs.php?color=Red" class="<?= $selectedcolor === 'Red' ? 'active' : '' ?>">Red</a>
                        <a href="coralreefs.php?color=Yellow" class="<?= $selectedcolor === 'Yellow' ? 'active' : '' ?>">Yellow</a>
                        <a href="coralreefs.php?color=Green" class="<?= $selectedcolor === 'Green' ? 'active' : '' ?>">Green</a>
                        <a href="coralreefs.php?color=Brown" class="<?= $selectedcolor === 'Brown' ? 'active' : '' ?>">Brown</a>
                    </div>
                <?php endif; ?>

                <!-- applied filter tags -->
                <?php
                // Build an array of applied filters
                $appliedFilters = [];

                // Water type
                if (!empty($_GET['coralType'])) {
                    foreach ($_GET['coralType'] as $wt) {
                        $appliedFilters[] = [
                            'label' => $wt,
                            'param' => 'coralType',
                            'value' => $wt
                        ];
                    }
                }

                // Difficulty
                if (!empty($_GET['difficulty'])) {
                    foreach ($_GET['difficulty'] as $diff) {
                        $appliedFilters[] = [
                            'label' => $diff,
                            'param' => 'difficulty',
                            'value' => $diff
                        ];
                    }
                }

                // Lighting Level
                if (!empty($_GET['lighting'])) {
                    foreach ($_GET['lighting'] as $light) {
                        $appliedFilters[] = [
                            'label' => $light,
                            'param' => 'lighting',
                            'value' => $light
                        ];
                    }
                }

                // Water Flow
                if (!empty($_GET['waterFlow'])) {
                    foreach ($_GET['waterFlow'] as $wf) {
                        $appliedFilters[] = [
                            'label' => $wf,
                            'param' => 'waterFlow',
                            'value' => $wf
                        ];
                    }
                }
                ?>

                <?php if (!empty($appliedFilters)): ?>
                    <div class="applied-filters">
                        <strong>Applied Filters:</strong>
                        <?php foreach ($appliedFilters as $filter): ?>
                            <?php
                            // Copy all query parameters
                            $newParams = $_GET;

                            // If param exists and is an array, remove the specific value
                            if (isset($newParams[$filter['param']]) && is_array($newParams[$filter['param']])) {
                                $newParams[$filter['param']] = array_values(
                                    array_diff($newParams[$filter['param']], [$filter['value']])
                                );

                                // If array becomes empty, remove it entirely
                                if (empty($newParams[$filter['param']])) {
                                    unset($newParams[$filter['param']]);
                                }
                            }

                            // Build URL without this filter
                            $removeUrl = "coralreefs.php?" . http_build_query($newParams);
                            ?>
                            <a href="<?= htmlspecialchars($removeUrl) ?>" class="filter-tag">
                                <?= htmlspecialchars($filter['label']) ?> ✕
                            </a>
                        <?php endforeach; ?>

                        <!-- Clear All -->
                        <a href="coralreefs.php" class="filter-tag clear-all">Clear All</a>
                    </div>
                <?php endif; ?>


                <!-- Product Display -->
                <?php
                if (!empty($_GET['id'])) {
                    // Show single product detail page
                    include 'product_detail.php';
                } else {
                    // Show normal product grid
                ?>
                    <div class="product-grid">
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <a href="coralreefs.php?id=<?= $product['productID'] ?>">
                                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['pName']) ?>" class="product-image">
                                </a>
                                <div class="card-body">
                                    <h6><?= htmlspecialchars($product['pName']) ?></h6>
                                    <p><?= htmlspecialchars($product['description']) ?></p>
                                    <div class="price">$<?= number_format($product['price'], 2) ?></div>

                                    <div class="card-actions" style="display: flex; gap: 8px; align-items: center;">
                                        <!-- Add to Cart -->
                                        <form method="post" action="add_to_cart.php" style="margin: 0;">
                                            <input type="hidden" name="productID" value="<?= $product['productID'] ?>">
                                            <?php if ($product['stock'] > 0): ?>
                                                <button type="submit">Add to Cart</button>
                                            <?php else: ?>
                                                <button type="submit" disabled class="btn-disabled" title="Out of stock">Out of Stock</button>
                                            <?php endif; ?>
                                        </form>

                                        <!-- Wishlist -->
                                        <a href="add_to_wishlist.php?productID=<?= $product['productID'] ?>"
                                            title="Add to Wishlist"
                                            style="display: inline-block;">
                                            <img src="/images/wishlist.png" alt="Wishlist" style="width: 32px; height: 32px;">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php
                }
                ?>


            </section>

        </div>
    </section>

    <?php include 'footer.php'; ?>

    <?php include 'chatbot.php'; ?>
    <?php include 'backToTop.php'; ?>
</body>

</html>