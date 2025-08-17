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


            // Update last_login
            $updateLogin = $conn->prepare("UPDATE users SET last_login = NOW() WHERE userID = :userID");
            $updateLogin->bindParam(':userID', $user['userID'], PDO::PARAM_INT);
            $updateLogin->execute();

            // Redirect to homepage or user dashboard
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <div class="container my-5">
        <h2>User Login</h2>

        <?php if ($message): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" required />
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" required />
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required />
            </div>

            <button type="submit" name="userLogin" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>

</html>