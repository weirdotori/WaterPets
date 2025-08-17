<?php
session_start();
require_once "db.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user']['userID'])) {
    $userID = $_SESSION['user']['userID'];

    try {
        // Fetch stored hashed password
        $stmt = $conn->prepare("SELECT password FROM users WHERE userID = ?");
        $stmt->execute([$userID]);
        $stored = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$stored || !password_verify($_POST['current_password'], $stored['password'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ]);
            exit();
        }

        if ($_POST['new_password'] !== $_POST['confirm_password']) {
            echo json_encode([
                'success' => false,
                'message' => 'New password and confirmation do not match.'
            ]);
            exit();
        }

        // Hash new password
        $hashedPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

        // Update in DB
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE userID = ?");
        $stmt->execute([$hashedPassword, $userID]);

        echo json_encode([
            'success' => true,
            'message' => 'Password changed successfully!'
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to change password. Please try again.'
        ]);
    }
}
?>
