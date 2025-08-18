<?php
session_start();
require_once "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['userLogin'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $sql = "SELECT userID, username, email, password, role, profile_pic 
                FROM users 
                WHERE username = :username 
                AND email = :email 
                AND role = 'customer'";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            $_SESSION['user'] = [
                'userID' => $user['userID'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'],
                'profile_pic' => $user['profile_pic']
            ];

            $updateLogin = $conn->prepare("UPDATE users SET last_login = NOW() WHERE userID = :userID");
            $updateLogin->bindParam(':userID', $user['userID'], PDO::PARAM_INT);
            $updateLogin->execute();

            header("Location: home.php");
            exit();
        } else {
            $message = "Invalid login credentials.";
        }
    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>User Login</title>
    <link rel="stylesheet" href="/css/userLogin_style.css">
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <div class="login-container">
        <div class="login-content">

            <!-- Heading above the form -->
            <div class="login-heading">
                <h1>Welcome to WaterPets</h1>
                <p>Please login to continue</p>
            </div>

            <!-- Login Form -->
            <div class="login-right">
                <h2>User Login</h2>
                <?php if ($message): ?>
                    <div class="login-message"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" placeholder="Enter your username" required />
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" placeholder="Enter your email" required />
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" placeholder="Enter your password" required />
                    </div>

                    <button type="submit" name="userLogin">Login</button>

                    <!-- Forgot password link -->
                    <p style="margin-top: 10px; text-align: right;">
                        <a href="forgot-password.php" style="color: #0011ffff; text-decoration: underline;">Forgot Password?</a>
                    </p>
                </form>
            </div>

        </div>
    </div>



</body>

</html>