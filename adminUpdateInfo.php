<?php
session_start();
require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['admin']['userID'])) {
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, phone = ? WHERE userID = ?");
    $stmt->execute([$_POST['username'], $_POST['email'], $_POST['phone'], $_SESSION['admin']['userID']]);

    // Update session username so dashboard reflects change instantly
    $_SESSION['admin']['username'] = $_POST['username'];

    header("Location: adminProfile.php?success=1");
    exit();
}
?>
