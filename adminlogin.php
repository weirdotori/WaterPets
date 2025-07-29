<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="/css/admin-style.css">
</head>

<body>
    <h2>Admin Login</h2>
    <form action="admin_auth.php" method="POST">
        <label>Username: </label><br>
        <input type="text" name="username" required><br><br>

        <label>Email: </label><br>
        <input type="email" name="email" required><br><br>

        <label>Password: </label><br>
        <input type="password" name="password" required><br><br>

        <label>Confirm Password: </label><br>
        <input type="password" name="confirm_password" required><br><br>
        
        <button type="submit">Login</button>
    </form>
</body>

</html>