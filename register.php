<?php
session_start();
require 'db.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $phone = trim($_POST['phone']);
    $remember = isset($_POST['remember']);

    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword) || empty($phone)) {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format. Must include '@'.";
    } elseif ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    } elseif (!preg_match('/[0-9]/', $password) || !preg_match('/[^a-zA-Z0-9]/', $password)) {
        $errors[] = "Password must include at least one number and one special character.";
    } elseif (!preg_match('/^\d{1,11}$/', $phone)) {
        $errors[] = "Phone number must contain only digits and be at most 11 characters.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $errors[] = "Email already registered.";
        }
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $role = 'customer';
        $profile_pic = NULL;

        $insert = $conn->prepare("INSERT INTO users (username, email, password, phone, role, profile_pic) VALUES (?, ?, ?, ?, ?, ?)");
        $successInsert = $insert->execute([$username, $email, $hashedPassword, $phone, $role, $profile_pic]);

        if ($successInsert) {
            $userID = $conn->lastInsertId();

            $_SESSION['user'] = [
                'userID'      => $userID,
                'username'    => $username,
                'email'       => $email,
                'role'        => $role,
                'profile_pic' => $profile_pic
            ];

            if ($remember) {
                setcookie('userID', $userID, time() + (86400 * 30), "/");
                setcookie('username', $username, time() + (86400 * 30), "/");
                setcookie('email', $email, time() + (86400 * 30), "/");
                setcookie('role', $role, time() + (86400 * 30), "/");
            }

            header("Location: home.php");
            exit();
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>User Registration</title>
    <link rel="stylesheet" href="/css/userLogin_style.css"> <!-- reuse login styles -->
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <div class="login-container">
        <div class="login-content">
            <!-- Heading above the form -->
            <div class="login-heading">
                <h1>Welcome to WaterPets</h1>
                <p>Please enter information to register an account.</p>
            </div>

            <div class="login-right">
                <h2>Create an Account</h2>

                <?php if (!empty($errors)): ?>
                    <div class="login-message" style="color: #ff6b6b; margin-bottom:15px; text-align:left;">
                        <ul>
                            <?php foreach ($errors as $e): ?>
                                <li><?= htmlspecialchars($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" required 
                               value="<?= htmlspecialchars($username ?? '') ?>" />
                    </div>

                    <div class="form-group">
                        <label for="email">Email address</label>
                        <input type="email" name="email" id="email" required 
                               value="<?= htmlspecialchars($email ?? '') ?>" />
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" required />
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" required />
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" name="phone" id="phone" maxlength="11" pattern="\d{1,11}" required 
                               value="<?= htmlspecialchars($phone ?? '') ?>" />
                    </div>

                    <div class="form-group" style="flex-direction:row; align-items:center;">
                        <input type="checkbox" name="remember" id="remember" style="width:auto; margin-right:8px;"
                               <?= isset($remember) ? 'checked' : '' ?> />
                        <label for="remember" style="margin:0; font-weight:normal;">Remember Me</label>
                    </div>

                    <button type="submit">Register</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
