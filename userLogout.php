<?php
session_start();

// Unset all user-related session data
unset($_SESSION['user']);
unset($_SESSION['cart']); // ensures cart is gone

// Optional: unset other session vars if you want a full logout
// unset($_SESSION['otherVar']);

// Destroy the session completely
session_destroy();

// Clear the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect
header("Location: home.php");
exit();
