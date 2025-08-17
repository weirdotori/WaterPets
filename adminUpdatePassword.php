<?php
session_name('admin_session');
session_start();
require_once "db.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['admin']['userID'])) {
    $userID = $_SESSION['admin']['userID'];

    $current_password = trim($_POST['current_password']);
    $new_password     = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    $stmt = $conn->prepare("SELECT password FROM users WHERE userID = ?");
    $stmt->execute([$userID]);
    $stored = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$stored || !password_verify($current_password, $stored['password'])) {
        echo json_encode(["success" => false, "message" => "Current password is incorrect."]);
        exit;
    }

    if ($new_password !== $confirm_password) {
        echo json_encode(["success" => false, "message" => "New password and confirmation do not match."]);
        exit;
    }

    $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE userID = ?");
    $stmt->execute([$hashedPassword, $userID]);

    echo json_encode(["success" => true, "message" => "Password updated successfully!"]);
    exit;
} else {
    echo json_encode(["success" => false, "message" => "Invalid request or session expired."]);
}
