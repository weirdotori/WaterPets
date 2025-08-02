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
    <link rel="stylesheet" href="/css/admin-style.css">
</head>

<body>

    <div class="admin-container">
        <!-- Top Navbar -->
        <nav class="top-navbar d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
            <!-- Left: Logo -->
            <div class="d-flex align-items-center">
                <img src="/images/oystergif.gif" alt="WaterPets Logo" style="height:40px;">
                <span class="h-8 border-l-2 border-blue-500 mx-3"></span>
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
                        <img src="<?= htmlspecialchars($_SESSION['profile_pic'] ?? '/images/admin-profile.jpg') ?>"
                            alt="Profile"
                            class="rounded-circle"
                            style="width:35px; height:35px; object-fit:cover;">
                        <span class="ms-2"><?= htmlspecialchars($_SESSION['username']) ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end text-small" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="#" id="openProfileModal">My Profile</a></li>

                        <li><a class="dropdown-item" href="#" id="">Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="logout.php">Sign out</a></li>
                    </ul>
                </div>

            </div>
        </nav>

        <!-- Profile Modal -->
        <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="profileModalLabel">Update Profile Picture</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="profileForm" enctype="multipart/form-data">
                            <div class="text-center mb-3">
                                <img src="<?= htmlspecialchars($_SESSION['profile_pic'] ?? '/images/admin-profile.jpg') ?>"
                                    id="currentProfilePreview"
                                    class="rounded-circle"
                                    style="width:100px; height:100px; object-fit:cover;">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Choose New Profile Picture</label>
                                <input type="file" name="profile_pic" class="form-control" required>
                            </div>

                            <div id="profileMsg" class="mb-2"></div>

                            <button type="submit" class="btn btn-primary w-100">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <ul>
                <li><a href="admin_dashboard.php" class="active">Dashboard</a></li>
                <li><a href="admin_account.php">Account</a></li>
                <li><a href="admin_reports.php">Reports</a></li>
                <li><a href="manage_orders.php">Orders</a></li>
                <li><a href="manage_users.php">Users</a></li>
                <li><a href="manage_products.php">Products</a></li>
                <li><a href="view_messages.php">Inquires</a></li>
                <li><a href="admin_reviews.php">Reviews</a></li>
                <li><a href="logout.php" class="logout">Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="dashboard-header">
                <h2>
                    Welcome back,
                    <span class="username-highlight"><?= htmlspecialchars($_SESSION['username']) ?></span>
                </h2>
                <p class="text-muted">Admin Dashboard Overview</p>
            </div>


            <!-- Stats Cards -->
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card stat-card bg-primary text-white p-3">
                        <h5>Total Users</h5>
                        <h3><?= $totalUsers ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-success text-white p-3">
                        <h5>Total Products</h5>
                        <h3><?= $totalProducts ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-warning text-dark p-3">
                        <h5>Total Orders</h5>
                        <h3><?= $totalOrders ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-danger text-white p-3">
                        <h5>Total Reviews</h5>
                        <h3><?= $totalReviews ?></h3>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="mt-5">
                <h4>Quick Links</h4>
                <div class="d-flex flex-wrap gap-3">
                    <a href="manage_users.php" class="btn btn-outline-primary">Manage Users</a>
                    <a href="manage_products.php" class="btn btn-outline-success">Manage Products</a>
                    <a href="manage_orders.php" class="btn btn-outline-warning">Manage Orders</a>
                    <a href="view_messages.php" class="btn btn-outline-danger">View Messages</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Toggle light or dark mode -->
    <script src="/js/darkmodeToggle.js"></script>

    <!-- Bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Toggle light or dark mode -->
    <script src="/js/uploadProfile.js"></script>

</body>

</html>