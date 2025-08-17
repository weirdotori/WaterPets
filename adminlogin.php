<?php
session_name('admin_session'); // unique name for admin sessions
session_start();

require_once "db.php"; // PDO connection file


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['adminLogin'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        // Query with username + email + role
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
            // Save all necessary session data
            // $_SESSION['userID'] = $user['userID'];
            // $_SESSION['username'] = $user['username'];
            // $_SESSION['email'] = $user['email'];
            // $_SESSION['role'] = $user['role'];
            // $_SESSION['adminLogin'] = true;
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container my-5">
        <h2 class="mb-4">Admin Login</h2>

        <?php if (isset($message)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Username:</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" name="adminLogin" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>

</html>