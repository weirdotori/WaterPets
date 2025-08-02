<?php
session_start();
require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['userid'])) {
    $userID = $_SESSION['userid'];

    // Fetch stored hashed password
    $stmt = $conn->prepare("SELECT password FROM users WHERE userID = ?");
    $stmt->execute([$userID]);
    $stored = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$stored || !password_verify($_POST['current_password'], $stored['password'])) {
        die("Current password is incorrect.");
    }

    if ($_POST['new_password'] !== $_POST['confirm_password']) {
        die("New password and confirmation do not match.");
    }

    // Hash new password
    $hashedPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // Update in DB
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE userID = ?");
    $stmt->execute([$hashedPassword, $userID]);

    header("Location: adminProfile.php?password_updated=1");
    exit();
}
?>
