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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
     <link rel="stylesheet" href="css/reset-password.css">

</head>
<body>
    <div class="login-container">
        <div class="login-content">
            <!-- Heading -->
            <div class="login-heading">
                <h1>Reset Password</h1>
                <p>Enter your new password below</p>
            </div>

            <!-- Glass form -->
            <div class="login-right">
                <h2>Create New Password</h2>
                <form method="post" action="process-reset-password.php">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" required placeholder="Enter new password">
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Repeat Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Repeat new password">
                    </div>

                    <button type="submit">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
