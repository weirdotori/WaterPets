<?php
session_name('admin_session'); // unique name for admin sessions
session_start();

// Unset only the admin session data
if (isset($_SESSION['admin'])) {
    unset($_SESSION['admin']);
}

// Optionally, if you set a separate adminLogin flag or similar, unset that too
// unset($_SESSION['adminLogin']);

// If after unsetting admin, the session is empty, you can destroy the session fully
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

// Redirect to admin login page
header("Location: adminLogin.php");
exit();
