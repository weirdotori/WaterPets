<?php
session_start();
require_once "db.php";

// Security: Only allow logged-in admins
if (
    empty($_SESSION['adminLogin']) ||
    $_SESSION['adminLogin'] !== true ||
    empty($_SESSION['userid']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: adminLogin.php");
    exit();
}


// Query statistics
$totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalProducts = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalReviews = $conn->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/admin_style.css">
</head>

<body>

    <div class="admin-container">
        <!-- Top Navbar -->
        <nav class="top-navbar d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
            <!-- Left: Logo -->
            <div class="d-flex align-items-center">
                <img src="/images/oystergif.gif" alt="WaterPets Logo" style="height:40px;">
                <a href="#" class="logo-font">WaterPets</a>
            </div>

            <!-- Center: Search -->
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search..." aria-label="Search">
                <button class="btn btn-outline-primary" type="submit">Search</button>
            </form>

            <!-- Right: Theme + Profile -->
            <div class="d-flex align-items-center gap-3">
                <!-- Theme Toggle -->
                <button id="themeToggle" class="btn btn-sm p-1">
                    <img id="themeIcon" src="/images/darkmode.png" alt="Toggle Theme" style="width:20px; height:20px;">
                </button>

                <!-- Profile Dropdown -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php
                        $stmt = $conn->prepare("SELECT profile_pic FROM users WHERE userID = ?");
                        $stmt->execute([$_SESSION['userid']]);
                        $latestProfilePic = $stmt->fetchColumn();
                        ?>
                        <img src="<?= htmlspecialchars($latestProfilePic ?? '/images/admin-profile.jpg') ?>"
                            alt="Profile"
                            class="rounded-circle"
                            style="width:35px; height:35px; object-fit:cover;">

                        <span class="ms-2"><?= htmlspecialchars($_SESSION['username']) ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end text-small" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="adminProfile.php">My Profile</a></li>
                        <li><a class="dropdown-item" href="#">Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="logout.php">Sign out</a></li>
                    </ul>

                </div>

            </div>
        </nav>

        <!-- Sidebar -->
        <div class="sidebar">
            <ul>
                <li><a href="?page=dashboard_home" class="active">Dashboard</a></li>
                <li><a href="?page=analytics">Analytics</a></li>
                <li><a href="?page=manage_users">Manage Users</a></li>
                <li><a href="?page=manage_orders">Manage Orders</a></li>
                <li><a href="?page=manage_products">Manage Products</a></li>
                <li><a href="?page=manage_reviews">Reviews and Inquires</a></li>
                <li><a href="?page=settings">Settiings</a></li>
                <li><a href="logout.php" class="logout">Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <?php
            $page = $_GET['page'] ?? 'dashboard_home';
            $allowed_pages = ['dashboard_home', 'analytics', 'manage_users', 'manage_orders', 'manage_products', 'edit_product', 'view_product', 'add_product', 'manage_reviews', 'settings'];

            if (in_array($page, $allowed_pages)) {
                include __DIR__ . "/admin-dashboard-contents/$page.php";
            } else {
                include __DIR__ . "/admin-dashboard-contents/dashboard_home.php";
            }
            ?>
        </div>


        <!-- Toggle light or dark mode -->
        <script src="/js/darkmodeToggle.js"></script>

        <!-- Bootstrap js -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Toggle light or dark mode -->
        <script src="/js/admin_uploadProfile.js"></script>

</body>

</html>