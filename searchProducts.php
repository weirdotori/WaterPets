<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php'; // your PDO connection

$query = trim($_GET['q'] ?? '');
$query = strtolower($query); // normalize case
if ($query === '') {
    echo json_encode([]);
    exit;
}

// Map category pages to category names in DB
$productPages = [
    'fish.php'        => 'Fish',
    'coralreefs.php'  => 'Coral Reefs',
    'supplies.php'    => 'Supplies',
    'equipment.php'   => 'Equipment'
];

$results = [];

foreach ($productPages as $page => $categoryName) {
    // Get category ID
    $stmtCat = $conn->prepare("SELECT categoryID FROM categories WHERE cName = ?");
    $stmtCat->execute([$categoryName]);
    $categoryID = $stmtCat->fetchColumn();

    if ($categoryID) {
        // Search products in that category
        $stmt = $conn->prepare("SELECT productID, pName FROM products 
                        WHERE categoryID = ? AND LOWER(pName) LIKE ?");
        $searchTerm = "%" . strtolower($query) . "%";
        $stmt->execute([$categoryID, $searchTerm]);


        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = [
                'page' => $page,
                'id'   => $row['productID'],
                'name' => $row['pName']
            ];
        }
    }
}

echo json_encode($results);
?>


