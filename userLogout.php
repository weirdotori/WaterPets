<?php
session_start();

// Unset only the user session data
if (isset($_SESSION['user'])) {
    unset($_SESSION['user']);
}

// Optionally, if you set a separate userLogin flag or similar, unset that too
// unset($_SESSION['userLogin']);

// If after unsetting user, the session is empty, you can destroy the session fully
if (empty($_SESSION)) {
    session_destroy();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
}

// Redirect to user homepage or login page (your choice)
header("Location: home.php");
exit();
