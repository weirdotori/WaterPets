<?php
session_start();
require_once "db.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user']['userID'])) {
    try {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, phone = ? WHERE userID = ?");
        $stmt->execute([
            $_POST['username'], 
            $_POST['email'], 
            $_POST['phone'], 
            $_SESSION['user']['userID']
        ]);

        // Update session username
        $_SESSION['user']['username'] = $_POST['username'];

        echo json_encode([
            'success' => true,
            'message' => 'Personal information updated successfully!'
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update personal information. Please try again.'
        ]);
    }
}
