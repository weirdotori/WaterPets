<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

// Access session correctly based on your structure
$userID = $_SESSION['user']['userID'] ?? null;
$userRole = $_SESSION['user']['role'] ?? null;

$productID = $_POST['productID'] ?? null;
$rating = $_POST['rating'] ?? null;
$comment = trim($_POST['reviewComment'] ?? '');

if ($userID && $userRole === 'customer' && $productID && $rating && $comment) {
    $stmt = $conn->prepare("INSERT INTO reviews (reviewComment, rating, userID, productID, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$comment, $rating, $userID, $productID]);

    echo json_encode(['success' => true, 'message' => 'Review submitted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input or unauthorized. Make sure you are logged in as a user and filled all fields.']);
}
exit;
?>
