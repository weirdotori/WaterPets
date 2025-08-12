<?php
session_start();
require_once "db.php";

if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header("Location: userLogin.php");
    exit();
}

$userId = $_SESSION['user']['userID'];

// Delete user from DB
$stmt = $conn->prepare("DELETE FROM users WHERE userID = ?");
if ($stmt->execute([$userId])) {
    session_destroy();
    header("Location: goodbye.php"); // a goodbye or homepage
    exit();
} else {
    echo "Error deleting account. Please try again.";
}
?>
