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
        $errors[] = "Invalid email format.";
    } elseif ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    } else {
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <div class="container my-5" style="max-width: 500px;">
        <h2 class="mb-4">Create an Account</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" required value="<?= htmlspecialchars($username ?? '') ?>" />
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" name="email" id="email" class="form-control" required value="<?= htmlspecialchars($email ?? '') ?>" />
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required />
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required />
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" name="phone" id="phone" class="form-control" required value="<?= htmlspecialchars($phone ?? '') ?>" />
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" id="remember" class="form-check-input" <?= isset($remember) ? 'checked' : '' ?> />
                <label class="form-check-label" for="remember">Remember Me</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
    </div>
</body>

</html>