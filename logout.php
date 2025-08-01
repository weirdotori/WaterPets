<?php
session_start();

// Remove all session variables
session_unset();

// Destroy the session
session_destroy();

// Remove the session cookie (important for browsers keeping old sessions)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to admin login page
header("Location: adminLogin.php");
exit();
