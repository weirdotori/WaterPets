<?php
session_start();

// Determine user role before destroying session
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Remove all session variables
session_unset();

// Destroy the session
session_destroy();

// Remove the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect based on previous role
if ($isAdmin) {
    header("Location: adminLogin.php");
} else {
    header("Location: home.php");
}
exit();
