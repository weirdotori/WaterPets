<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "db.php";

// Find category ID for "Equipment"
$categoryStmt = $conn->prepare("SELECT categoryID FROM categories WHERE cName = 'Equipment'");
$categoryStmt->execute();
$categoryID = $categoryStmt->fetchColumn();

if (!$categoryID) {
    echo "<div class='container py-4'><p>No Eqipment category found.</p></div>";
    exit;
}

// Get equipment products
$query = "SELECT * FROM products WHERE categoryID = ?";
$params = [$categoryID];

// Filter Type
if (!empty($_GET['filterType'])) {
    $placeholders = implode(',', array_fill(0, count($_GET['filterType']), '?'));
    $query .= " AND filterType IN ($placeholders)";
    $params = array_merge($params, $_GET['filterType']);
}

// Heater Watt
if (!empty($_GET['heaterWatt'])) {
    $placeholders = implode(',', array_fill(0, count($_GET['heaterWatt']), '?'));
    $query .= " AND heaterWatt IN ($placeholders)";
    $params = array_merge($params, $_GET['heaterWatt']);
}

// Pump Size
if (!empty($_GET['pumpSize'])) {
    $placeholders = implode(',', array_fill(0, count($_GET['pumpSize']), '?'));
    $query .= " AND pumpSize IN ($placeholders)";
    $params = array_merge($params, $_GET['pumpSize']);
}

// Tank Size
if (!empty($_GET['tankSize'])) {
    $placeholders = implode(',', array_fill(0, count($_GET['tankSize']), '?'));
    $query .= " AND tankSize IN ($placeholders)";
    $params = array_merge($params, $_GET['tankSize']);
}

// Equipment filter (single value)
if (!empty($_GET['equipment'])) {
    $query .= " AND equipment = ?";
    $params[] = $_GET['equipment'];
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
            if (!empty($_GET['filterType'])) {
                $placeholders = implode(',', array_fill(0, count($_GET['filterType']), '?'));
                $query .= " AND p.filterType IN ($placeholders)";
                $params = array_merge($params, $_GET['filterType']);
            }
            if (!empty($_GET['heaterWatt'])) {
                $placeholders = implode(',', array_fill(0, count($_GET['heaterWatt']), '?'));
                $query .= " AND p.heaterWatt IN ($placeholders)";
                $params = array_merge($params, $_GET['heaterWatt']);
            }
            if (!empty($_GET['pumpSize'])) {
                $placeholders = implode(',', array_fill(0, count($_GET['pumpSize']), '?'));
                $query .= " AND p.pumpSize IN ($placeholders)";
                $params = array_merge($params, $_GET['pumpSize']);
            }
            if (!empty($_GET['tankShape'])) {
                $placeholders = implode(',', array_fill(0, count($_GET['tankShape']), '?'));
                $query .= " AND p.tankShape IN ($placeholders)";
                $params = array_merge($params, $_GET['tankShape']);
            }
            if (!empty($_GET['equipment'])) {
                $query .= " AND p.equipment = ?";
                $params[] = $_GET['equipment'];
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
    <title>Shop Equipment - WaterPets</title>

    <!-- Css Style -->
    <link rel="stylesheet" href="/css/shop_style.css">

    <!-- Tailwind css -->
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

    <section class="fish-section">
        <?php include 'header.php'; ?>
        <hr class="header-divider">

        <div class="page-container">

            <!-- Sidebar Filters -->
            <?php if (empty($_GET['id'])): ?>
                <aside class="filter-sidebar">
                    <form method="GET" action="equipment.php">
                        <h5>Filter Options</h5>

                        <!-- By Filter Type -->
                        <div class="filter-group">
                            <h6>By Filter Type</h6>
                            <label><input type="checkbox" name="filterType[]" value="Internal" <?= in_array('Internal', $_GET['filterType'] ?? []) ? 'checked' : '' ?>> Internal</label> <br>
                            <label><input type="checkbox" name="filterType[]" value="External" <?= in_array('External', $_GET['filterType'] ?? []) ? 'checked' : '' ?>> External</label> <br>
                            <label><input type="checkbox" name="filterType[]" value="Hang-on" <?= in_array('Hang-on', $_GET['filterType'] ?? []) ? 'checked' : '' ?>> Hang-on</label>
                        </div>

                        <!-- By Heater Watt -->
                        <div class="filter-group">
                            <h6>By Heater Watt</h6>
                            <label><input type="checkbox" name="heaterWatt[]" value="Small (50-100W)" <?= in_array('Small (50-100W)', $_GET['heaterWatt'] ?? []) ? 'checked' : '' ?>> Small (50-100W)</label> <br>
                            <label><input type="checkbox" name="heaterWatt[]" value="Square (100–200W)" <?= in_array('Medium (100–200W)', $_GET['heaterWatt'] ?? []) ? 'checked' : '' ?>> Medium (100–200W)</label> <br>
                            <label><input type="checkbox" name="heaterWatt[]" value="Others (200+W)" <?= in_array('Large (200+W)', $_GET['heaterWatt'] ?? []) ? 'checked' : '' ?>> Large (200+W)</label>
                        </div>

                        <!-- By Pump Size -->
                        <div class="filter-group">
                            <h6>By Pump Size</h6>
                            <label><input type="checkbox" name="pumpSize[]" value="Small" <?= in_array('Small', $_GET['pumpSize'] ?? []) ? 'checked' : '' ?>> Small</label> <br>
                            <label><input type="checkbox" name="pumpSize[]" value="Medium" <?= in_array('Medium', $_GET['pumpSize'] ?? []) ? 'checked' : '' ?>> Medium</label> <br>
                            <label><input type="checkbox" name="pumpSize[]" value="Large" <?= in_array('Large', $_GET['pumpSize'] ?? []) ? 'checked' : '' ?>> Large</label>
                        </div>

                        <!-- By Tank Shape -->
                        <div class="filter-group">
                            <h6>By Tank Shape</h6>
                            <label><input type="checkbox" name="tankShape[]" value="Round" <?= in_array('Round', $_GET['tankShape'] ?? []) ? 'checked' : '' ?>> Round</label> <br>
                            <label><input type="checkbox" name="tankShape[]" value="Medium" <?= in_array('Square', $_GET['tankShape'] ?? []) ? 'checked' : '' ?>> Square</label> <br>
                            <label><input type="checkbox" name="tankShape[]" value="Others" <?= in_array('Others', $_GET['tankShape'] ?? []) ? 'checked' : '' ?>> Others</label>
                        </div>

                        <button type="submit" class="filter-btn">Apply Filters</button>
                    </form>
                </aside>
            <?php endif; ?>


            <!-- Main Shop Area -->
            <section class="shop-main <?= !empty($_GET['id']) ? 'w-full' : '' ?>">

                <?php if (empty($_GET['id'])): ?>
                    <form method="GET" action="equipment.php" class="search-sort">
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

                <?php $selectedEquipment = $_GET['equipment'] ?? ''; ?>

                <?php if (empty($_GET['id'])): ?>
                    <div class="species-buttons">
                        <a href="equipment.php" class="<?= $selectedEquipment === '' ? 'active' : '' ?>">All</a>
                        <a href="equipment.php?species=Betta" class="<?= $selectedEquipment === 'Filter' ? 'active' : '' ?>">Filter</a>
                        <a href="equipment.php?species=Goldfish" class="<?= $selectedEquipment === 'Heater' ? 'active' : '' ?>">Heater</a>
                        <a href="equipment.php?species=Guppy" class="<?= $selectedEquipment === 'Pump' ? 'active' : '' ?>">Pump</a>
                        <a href="equipment.php?species=Tetra" class="<?= $selectedEquipment === 'Tank' ? 'active' : '' ?>">Tank</a>
                    </div>
                <?php endif; ?>

                <!-- applied filter tags -->
                <?php
                // Build an array of applied filters
                $appliedFilters = [];

                // Filter type
                if (!empty($_GET['filterType'])) {
                    foreach ($_GET['filterType'] as $wt) {
                        $appliedFilters[] = [
                            'label' => $wt,
                            'param' => 'filterType',
                            'value' => $wt
                        ];
                    }
                }

                // Heater Watt
                if (!empty($_GET['heaterWatt'])) {
                    foreach ($_GET['heaterWatt'] as $diff) {
                        $appliedFilters[] = [
                            'label' => $diff,
                            'param' => 'heaterWatt',
                            'value' => $diff
                        ];
                    }
                }

                // Pump Size
                if (!empty($_GET['pumpSize'])) {
                    foreach ($_GET['pumpSize'] as $agg) {
                        $appliedFilters[] = [
                            'label' => $agg,
                            'param' => 'pumpSize',
                            'value' => $agg
                        ];
                    }
                }

                // Tank Shape
                if (!empty($_GET['tankShape'])) {
                    foreach ($_GET['tankShape'] as $agg) {
                        $appliedFilters[] = [
                            'label' => $agg,
                            'param' => 'tankShape',
                            'value' => $agg
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
                            $removeUrl = "equipment.php?" . http_build_query($newParams);
                            ?>
                            <a href="<?= htmlspecialchars($removeUrl) ?>" class="filter-tag">
                                <?= htmlspecialchars($filter['label']) ?> ✕
                            </a>
                        <?php endforeach; ?>

                        <!-- Clear All -->
                        <a href="equipment.php" class="filter-tag clear-all">Clear All</a>
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
                                <a href="equipment.php?id=<?= $product['productID'] ?>">
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