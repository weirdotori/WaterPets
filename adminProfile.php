<?php
session_name('admin_session');
session_start();
require_once "db.php";

// Security: Only allow logged-in admins
if (empty($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'admin') {
    header("Location: adminLogin.php");
    exit();
}

// Fetch admin details
$stmt = $conn->prepare("SELECT username, email, phone, role, profile_pic, password FROM users WHERE userID = ?");
$stmt->execute([$_SESSION['admin']['userID']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="/css/adminProfile_style.css">
</head>

<body>

    <div class="profile-container">

        <!-- Profile Sidebar -->
        <div class="profile-sidebar">
            <ul>
                <li><a href="#" class="active" data-section="overview">Profile Overview</a></li>
                <li><a href="#" data-section="personal-info">Personal Information</a></li>
                <li><a href="#" data-section="admin-security">Security Settings</a></li>
                <li>
                    <button type="button" class="sidebar-back-btn" onclick="window.location.href='adminDashboard.php'">← Back</button>
                </li>
            </ul>
        </div>

        <!-- Profile Content -->
        <div class="profile-content">

            <!-- Profile Overview -->
            <div id="section-overview" class="section-overview">
                <h3>Profile Overview</h3>
                <div class="profile-card text-center">
                    <img src="<?= htmlspecialchars($admin['profile_pic'] ?? '/images/admin-profile.jpg') ?>" id="currentProfilePreview" class="profile-pic">
                    <form id="adminProfileForm" enctype="multipart/form-data" class="profile-upload-form">
                        <input type="file" name="profile_pic" class="file-input" required>
                        <div id="profileMsg" class="profile-msg"></div>
                        <button type="submit" class="btn-primary w-100">Upload New</button>
                    </form>
                </div>
            </div>

            <!-- Personal Information -->
            <div id="section-personal-info" style="display:none;">
                <h3>Personal Information</h3>
                <div id="personalInfoMsg" class="noti-msg"></div>
                <form id="personalInfoForm" action="adminUpdateInfo.php" method="POST" class="info-form">

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="editable-field" value="<?= htmlspecialchars($admin['username']) ?>" readonly required>
                        <img src="/images/edit.png" class="edit-icon" title="Edit Username">
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="editable-field" value="<?= htmlspecialchars($admin['email']) ?>" readonly required>
                        <img src="/images/edit.png" class="edit-icon" title="Edit Email">
                    </div>

                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="editable-field" value="<?= htmlspecialchars($admin['phone'] ?? '') ?>" readonly>
                        <img src="/images/edit.png" class="edit-icon" title="Edit Phone">
                    </div>

                    <div class="form-group">
                        <label>Role</label>
                        <input type="text" value="<?= htmlspecialchars(ucfirst($admin['role'])) ?>" readonly class="readonly-field">
                    </div>

                    <button type="submit" class="btn-success">Save Changes</button>
                </form>
            </div>

            <!-- Admin Security Settings -->
            <div id="section-admin-security" style="display:none;">
                <h3>Admin Security Settings</h3>

                <!-- Normal Security Content -->
                <div id="adminSecurityContent">
                    <!-- Password Row -->
                    <div class="form-group password-row">
                        <label>Password</label>
                        <input type="password" value="********" readonly class="readonly-field">
                        <img src="/images/edit.png" class="edit-icon" id="editAdminPasswordBtn" title="Change Password">
                    </div>

                    <!-- Forgot Password -->
                    <div class="forgot-password">
                        <a href="forgot-password.php">Forgot Password?</a>
                    </div>

                    <!-- Recent Login Activity -->
                    <div class="form-group">
                        <label>Recent Logins</label>
                        <ul class="login-activity">
                            <li>2025-08-16 11:05 - IP: 192.168.1.10</li>
                            <li>2025-08-14 20:47 - IP: 192.168.1.8</li>
                        </ul>
                    </div>

                    <!-- Two-Factor Authentication -->
                    <div class="form-group">
                        <label>Two-Factor Authentication</label>
                        <p>Extra security for your admin account.</p>
                        <button type="button" class="btn-enable">Enable 2FA</button>
                    </div>
                </div>

                <!-- Password Change Form -->
                <form id="changeAdminPasswordForm" action="adminUpdatePassword.php" method="POST" class="info-form" style="display:none;">
                    <button type="button" id="backToAdminSecurity" class="btn-back">← Back</button>

                    <!-- Message Display -->
                    <div id="adminSecurityMsg" class="noti-msg"></div>

                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn-save">Save Changes</button>
                </form>
            </div>


        </div>
    </div>

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

        // Profile picture upload (Admin)
        document.getElementById("adminProfileForm").addEventListener("submit", function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            fetch("adminUploadProfile.php", {
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

        // Enable editing for personal info fields
        document.querySelectorAll(".edit-icon").forEach(icon => {
            icon.addEventListener("click", function() {
                let input = this.previousElementSibling;
                input.removeAttribute("readonly");
                input.style.pointerEvents = "auto";
                input.focus();
            });
        });



        // Show Change Password form
        document.getElementById("editAdminPasswordBtn").addEventListener("click", function() {
            document.getElementById("adminSecurityContent").style.display = "none";
            document.getElementById("changeAdminPasswordForm").style.display = "block";

            // Make inputs editable
            document.querySelectorAll('#changeAdminPasswordForm input').forEach(input => {
                input.removeAttribute('readonly');
                input.style.pointerEvents = "auto";
            });
        });

        // Back button in Change Password form
        document.getElementById("backToAdminSecurity").addEventListener("click", function() {
            document.getElementById("changeAdminPasswordForm").style.display = "none";
            document.getElementById("adminSecurityContent").style.display = "block";
        });

        // Handle Change Password form submission (AJAX)
        document.getElementById("changeAdminPasswordForm").addEventListener("submit", function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);
            const msgDiv = document.getElementById("adminSecurityMsg");

            fetch("adminUpdatePassword.php", {
                    method: "POST",
                    body: formData,
                    credentials: "same-origin" // ✅ important: sends session cookie
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        msgDiv.innerHTML = `<span class="text-success">${data.message}</span>`;
                        form.reset();
                        setTimeout(() => {
                            document.getElementById("changeAdminPasswordForm").style.display = "none";
                            document.getElementById("adminSecurityContent").style.display = "block";
                        }, 1500);
                    } else {
                        msgDiv.innerHTML = `<span class="text-danger">${data.message}</span>`;
                    }
                })
                .catch(err => {
                    console.error(err);
                    msgDiv.innerHTML = `<span class="text-danger">Something went wrong. Please try again.</span>`;
                });

        });
    </script>


</body>

</html>