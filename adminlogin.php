<?php
session_name('admin_session'); 
session_start();

require_once "db.php"; 

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['adminLogin'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $sql = "SELECT userID, username, email, password, role, profile_pic
                FROM users 
                WHERE username = :username 
                AND email = :email 
                AND role = 'admin'";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin'] = [
                'userID' => $user['userID'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => 'admin',
                'profile_pic' => $user['profile_pic']
            ];

            header("Location: adminDashboard.php");
            exit();
        } else {
            $message = "Admission Access Denied. Please try again.";
        }
    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="/css/adminLogin_style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <div class="login-container">
        <div class="login-content">
            <!-- Heading -->
            <div class="login-heading">
                <h1>Admin Portal</h1>
                <p>Secure access for administrators only</p>
            </div>

            <!-- Glass form -->
            <div class="login-right">
                <h2>Admin Login</h2>

                <?php if (isset($message)): ?>
                    <div class="alert-message">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" placeholder="Enter username" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="Enter email" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Enter password" required>
                    </div>

                    <button type="submit" name="adminLogin">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
