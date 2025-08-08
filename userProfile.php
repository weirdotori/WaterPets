<?php
session_start();
require_once "db.php";

// Security: Only allow logged-in users
if (
    empty($_SESSION['userLogin']) ||
    $_SESSION['userLogin'] !== true ||
    empty($_SESSION['userID']) ||
    $_SESSION['role'] !== 'customer'
) {
    header("Location: userLogin.php");
    exit();
}

// Fetch user details
$stmt = $conn->prepare("SELECT username, email, phone, role, profile_pic, password FROM users WHERE userID = ?");
$stmt->execute([$_SESSION['userID']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/user_style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>

    <!-- Top Navbar -->
    <!-- <nav class="top-navbar d-flex align-items-center justify-content-between px-3 py-2 border-bottom bg-white">
        <div class="d-flex align-items-center">
            <img src="/images/oystergif.gif" alt="Logo" style="height:40px;">
            <a href="user_dashboard.php" class="logo-font ms-2">WaterPets</a>
        </div>
        <div> -->
            <!-- Arrow back button -->
            <!-- <a href="userDashboard.php" class="btn btn-light btn-sm" title="Back to Dashboard">
                <i class="bi bi-arrow-left"></i>
            </a>
        </div>
    </nav> -->

    <div class="profile-container">

        <!-- Profile Sidebar -->
        <div class="profile-sidebar">
            <ul>
                <li><a href="#" class="active" data-section="overview">Profile Overview</a></li>
                <li><a href="#" data-section="personal-info">Personal Information</a></li>
                <li><a href="#" data-section="security">Security Settings</a></li>
            </ul>
        </div>

        <!-- Profile Content -->
        <div class="profile-content">

            <!-- Profile Overview -->
            <div id="section-overview">
                <h3>Profile Overview</h3>
                <div class="card p-3 text-center" style="max-width:400px;">
                    <img src="<?= htmlspecialchars($user['profile_pic'] ?? '/images/user.png') ?>"
                        id="currentProfilePreview"
                        class="rounded-circle mx-auto d-block"
                        style="width:120px; height:120px; object-fit:cover;">
                    <form id="profileForm" enctype="multipart/form-data" class="mt-3">
                        <input type="file" name="profile_pic" class="form-control mb-2" required>
                        <div id="profileMsg" class="mb-2"></div>
                        <button type="submit" class="btn btn-primary w-100">Upload New</button>
                    </form>
                </div>
            </div>

            <!-- Personal Information -->
            <div id="section-personal-info" style="display:none;">
                <h3>Personal Information</h3>
                <form id="personalInfoForm" action="updateuserInfo.php" method="POST" style="max-width:500px;">

                    <!-- Username -->
                    <div class="mb-3 position-relative">
                        <label class="form-label">Username</label>
                        <input type="text" name="username"
                            class="form-control editable-field"
                            value="<?= htmlspecialchars($user['username']) ?>"
                            readonly required>
                        <i class="bi bi-pencil-square edit-icon" title="Edit Username"></i>
                    </div>

                    <!-- Email -->
                    <div class="mb-3 position-relative">
                        <label class="form-label">Email</label>
                        <input type="email" name="email"
                            class="form-control editable-field"
                            value="<?= htmlspecialchars($user['email']) ?>"
                            readonly required>
                        <i class="bi bi-pencil-square edit-icon" title="Edit Email"></i>
                    </div>

                    <!-- Phone -->
                    <div class="mb-3 position-relative">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone"
                            class="form-control editable-field"
                            value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                            readonly>
                        <i class="bi bi-pencil-square edit-icon" title="Edit Phone"></i>
                    </div>

                    <!-- Role (not editable) -->
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <input type="text"
                            class="form-control"
                            value="<?= htmlspecialchars(ucfirst($user['role'])) ?>"
                            readonly
                            style="pointer-events: none; cursor: default;">
                    </div>



                    <button type="submit" class="btn btn-success">Save Changes</button>
                </form>
            </div>


            <!-- Security Settings -->
            <div id="section-security" style="display:none;">
                <h3>Security Settings</h3>

                <!-- Masked Password Display -->
                <div class="mb-3 position-relative">
                    <label class="form-label">Password</label>
                    <input type="password" value="********" class="form-control" readonly style="pointer-events:none; cursor:default;">
                    <i class="bi bi-pencil-square edit-icon" id="editPasswordBtn" title="Change Password"></i>
                </div>

                <!-- Hidden Change Password Form -->
                <form id="changePasswordForm" action="updateuserPassword.php" method="POST" style="max-width:500px; display:none;">
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-warning">Save Changes</button>
                </form>
            </div>


        </div>
    </div>

    <!-- bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- dark mode js -->
    <script src="/js/darkmodeToggle.js"></script>

    <!-- sidebar page -->
    <script>
        // Sidebar tab switching
        document.querySelectorAll(".profile-sidebar a").forEach(link => {
            link.addEventListener("click", function(e) {
                e.preventDefault();
                document.querySelectorAll(".profile-sidebar a").forEach(a => a.classList.remove("active"));
                this.classList.add("active");

                let section = this.getAttribute("data-section");
                document.querySelectorAll(".profile-content > div").forEach(div => div.style.display = "none");
                document.getElementById("section-" + section).style.display = "block";
            });
        });

        // Profile picture upload
        document.getElementById("profileForm").addEventListener("submit", function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            fetch("uploadProfile.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    let msg = document.getElementById("profileMsg");
                    if (data.success) {
                        msg.innerHTML = "<span class='text-success'>" + data.message + "</span>";
                        document.getElementById("currentProfilePreview").src = data.newImage + "?t=" + new Date().getTime();
                    } else {
                        msg.innerHTML = "<span class='text-danger'>" + data.message + "</span>";
                    }
                });
        });

        // Enable editing when clicking pen icon in Personal Information sidebar
        document.querySelectorAll(".edit-icon").forEach(icon => {
            icon.addEventListener("click", function() {
                let input = this.previousElementSibling;
                input.removeAttribute("readonly");
                input.style.pointerEvents = "auto"; // Allow clicking and typing now
                input.focus();
            });
        });

        // Show password change form when pencil icon clicked
        document.getElementById("editPasswordBtn").addEventListener("click", function() {
            document.getElementById("changePasswordForm").style.display = "block";
            this.parentElement.style.display = "none"; // hide masked password row
        });
    </script>
</body>

</html>