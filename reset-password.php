<?php
require_once "db.php";

if (!isset($_GET['token'])) {
    die("No token provided");
}

$token = $_GET['token'];
$token_hash = hash("sha256", $token);

// Fetch user with this reset token
$sql = "SELECT * FROM users WHERE reset_hash_token = ?";
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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>

    <h1>Reset Password</h1>

    <form method="post" action="process-reset-password.php">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <label for="password">New password</label>
        <input type="password" id="password" name="password" required>

        <label for="password_confirmation">Repeat password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required>

        <button type="submit">Change Password</button>
    </form>

</body>
</html>
