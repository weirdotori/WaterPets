<?php
session_start();
require_once "db.php";

// Security: Only allow logged-in admins
if (empty($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'admin') {
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
    <link rel="stylesheet" href="/css/admin_style.css">

    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"> -->
</head>

<body>
    <div class="admin-container">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <div class="navbar-left">
                <img src="/images/oystergif.gif" alt="WaterPets Logo" class="logo-img">
                <a href="home.php" class="logo-font">WaterPets</a>
            </div>

            <div class="navbar-center">
                <form class="search-form" role="search">
                    <input type="search" placeholder="Search..." class="search-input">
                    <button type="submit" class="search-btn">Search</button>
                </form>
            </div>

            <div class="navbar-right">
                <button id="themeToggle" class="icon-btn">
                    <img id="themeIcon" src="/images/darkmode.png" alt="Toggle Theme" class="icon-img">
                </button>

                <div class="dropdown">
                    <button class="dropdown-btn profile-btn">
                        <?php
                        $stmt = $conn->prepare("SELECT profile_pic FROM users WHERE userID = ?");
                        $stmt->execute([$_SESSION['admin']['userID']]);
                        $latestProfilePic = $stmt->fetchColumn();
                        ?>
                        <img src="<?= htmlspecialchars($latestProfilePic ?: '/images/user.png') ?>" alt="Profile" class="profile-img">
                        <span class="username"><?= htmlspecialchars($_SESSION['admin']['username']) ?></span>
                    </button>
                    <div class="dropdown-content">
                        <a href="adminProfile.php">My Profile</a>
                        <a href="#">Settings</a>
                        <hr>
                        <a href="adminLogout.php" class="logout-link">Sign out</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Sidebar -->
        <?php
        $page = $_GET['page'] ?? 'dashboard_home';
        function isActive($linkPage, $currentPage)
        {
            return $linkPage === $currentPage ? 'active' : '';
        }
        ?>

        <div class="sidebar">
            <ul>
                <li><a href="?page=dashboard_home" class="<?= isActive('dashboard_home', $page) ?>">Dashboard</a></li>
                <li><a href="?page=analytics" class="<?= isActive('analytics', $page) ?>">Analytics</a></li>
                <li><a href="?page=manage_users" class="<?= isActive('manage_users', $page) ?>">Manage Users</a></li>
                <li><a href="?page=manage_orders" class="<?= isActive('manage_orders', $page) ?>">Manage Orders</a></li>
                <li><a href="?page=manage_products" class="<?= isActive('manage_products', $page) ?>">Manage Products</a></li>
                <li><a href="?page=manage_inquiries" class="<?= isActive('manage_inquiries', $page) ?>">Manage Inquiries</a></li>
                <li><a href="?page=manage_faqs" class="<?= isActive('manage_faqs', $page) ?>">Manage FAQs</a></li>
                <li><a href="?page=settings" class="<?= isActive('settings', $page) ?>">Settings</a></li>
                <li><a href="adminLogout.php" class="logout">Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <?php
            $allowed_pages = ['dashboard_home', 'analytics', 'manage_users', 'edit_user', 'manage_orders', 'view_order', 'manage_products', 'edit_product', 'view_product', 'add_product', 'manage_inquiries', 'manage_faqs', 'settings'];
            if (in_array($page, $allowed_pages)) {
                include __DIR__ . "/admin-dashboard-contents/$page.php";
            } else {
                include __DIR__ . "/admin-dashboard-contents/dashboard_home.php";
            }
            ?>
        </div>
    </div>

    <script src="/js/darkmodeToggle.js"></script>
    <script src="/js/admin_uploadProfile.js"></script>

    <script>
        // Profile dropdown toggle
        document.addEventListener('DOMContentLoaded', function() {
            const profileBtn = document.querySelector('.profile-btn');
            const dropdown = document.querySelector('.dropdown-content');

            profileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('show');
            });

            document.addEventListener('click', function() {
                dropdown.classList.remove('show');
            });
        });
    </script>

    <!-- Bootstrap js -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script> -->

</body>

</html>