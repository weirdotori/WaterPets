<?php
require_once "db.php";

if (!isset($_POST['token'], $_POST['password'], $_POST['password_confirmation'])) {
    die("Invalid request");
}

$token = $_POST["token"];
$token_hash = hash("sha256", $token);

// Fetch user with this reset token
$sql = "SELECT * FROM users WHERE reset_token_hash = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$token_hash]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Token not found");
}

// Check token expiry
if (strtotime($user["reset_token_expires_at"]) <= time()) {
    die("Token has expired");
}

// Validate new password
$password = $_POST["password"];
$confirm = $_POST["password_confirmation"];

if (strlen($password) < 8) {
    die("Password must be at least 8 characters");
}

if (!preg_match("/[a-z]/i", $password)) {
    die("Password must contain at least one letter");
}

if (!preg_match("/[0-9]/", $password)) {
    die("Password must contain at least one number");
}

if ($password !== $confirm) {
    die("Passwords must match");
}

// Hash password and update user
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "UPDATE users
        SET password = ?,
            reset_token_hash = NULL,
            reset_token_expires_at = NULL
        WHERE userID = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$password_hash, $user["userID"]]);

echo "Password updated successfully. You can now login.";
