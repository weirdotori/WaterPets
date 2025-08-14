<?php
session_start();
require_once "db.php";

// Security: Only allow logged-in users
if (
    empty($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'customer'
) {
    header("Location: userLogin.php");
    exit();
}


// Fetch user details
$stmt = $conn->prepare("SELECT username, email, phone, role, profile_pic, password FROM users WHERE userID = ?");
$stmt->execute([$_SESSION['user']['userID']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch coupons for the logged-in user
$stmt = $conn->prepare("SELECT * FROM coupons WHERE userID = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user']['userID']]);
$coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/userProfile_style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>
    <!-- back button -->
    <button class="btn btn-outline-dark" onclick="history.back()" style="font-size: 16px; padding: 8px 16px;">
        ←
    </button>

    <div class="profile-container">

        <!-- Profile Sidebar -->
        <div class="profile-sidebar">
            <ul>
                <li><a href="#" class="active" data-section="overview">Profile Overview</a></li>
                <li><a href="#" data-section="personal-info">Personal Information</a></li>
                <li><a href="#" data-section="orders">My Orders</a></li>
                <li><a href="#" data-section="security">Security Settings</a></li>
                <li><a href="#" data-section="loyalty">Loyalty Program</a></li>
                <li>
                    <form id="deleteAccountForm" method="POST" action="userDeleteAccount.php" onsubmit="return confirmDelete()">
                        <button type="submit" class="delete-account-btn">Delete Account</button>
                    </form>
                </li>
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
                    <form id="userProfileForm" enctype="multipart/form-data" class="mt-3">
                        <input type="file" name="profile_pic" class="form-control mb-2" required>
                        <div id="profileMsg" class="mb-2"></div>
                        <button type="submit" class="btn btn-primary w-100">Upload New</button>
                    </form>
                </div>
            </div>

            <!-- Personal Information -->
            <div id="section-personal-info" style="display:none;">
                <h3>Personal Information</h3>
                <form id="personalInfoForm" action="userUpdateInfo.php" method="POST" style="max-width:500px;">

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

            <!-- Orders Section -->
            <div id="section-orders" style="display:none;">
                <h3>My Orders</h3>
                <div class="orders-list-container"></div> <!-- for orders list -->
                <div class="order-summary-container" style="display:none;"></div> <!-- for summary -->
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
                <form id="changePasswordForm" action="userUpdatePassword.php" method="POST" style="max-width:500px; display:none;">
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

            <!-- Loyalty Program -->
            <div id="section-loyalty" style="display:none;">
                <h3>Loyalty Program & Coupons</h3>

                <?php if (count($coupons) === 0): ?>
                    <p>You don’t have any coupons yet. Place 3 orders to unlock your first coupon!</p>
                <?php else: ?>
                    <div class="coupons-list">
                        <?php foreach ($coupons as $coupon): ?>
                            <div class="coupon-card <?= $coupon['status'] === 'used' ? 'used-coupon' : '' ?>">
                                <span class="coupon-code"><?= htmlspecialchars($coupon['code']) ?></span>
                                <span class="coupon-desc"><?= $coupon['discount'] ?>% OFF</span>
                                <span class="coupon-status"><?= ucfirst($coupon['status']) ?></span>
                                <?php if ($coupon['status'] === 'unused'): ?>
                                    <button class="copy-btn" data-code="<?= htmlspecialchars($coupon['code']) ?>">Copy</button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php endif; ?>
            </div>


        </div>
    </div>


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
        document.getElementById("userProfileForm").addEventListener("submit", function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            fetch("userUploadProfile.php", {
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

        function confirmDelete() {
            return confirm("Are you sure you want to delete your account? This action cannot be undone.");
        }


        // Copy coupon code to clipboard
        document.querySelectorAll('.copy-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const code = btn.dataset.code;
                navigator.clipboard.writeText(code).then(() => {
                    btn.textContent = 'Copied!';
                    setTimeout(() => btn.textContent = 'Copy', 1500);
                });
            });
        });

        // Load orders list and attach handlers
        function loadOrdersList() {
            const listContainer = document.querySelector('.orders-list-container');
            const summaryContainer = document.querySelector('.order-summary-container');

            // Hide summary when showing list
            summaryContainer.style.display = 'none';
            listContainer.style.display = 'block';

            fetch('userFetchOrdersList.php')
                .then(res => res.text())
                .then(html => {
                    listContainer.innerHTML = html;

                    // Attach click handlers for "View Summary" buttons
                    document.querySelectorAll('.view-order-btn').forEach(btn => {
                        btn.addEventListener('click', () => {
                            const orderID = btn.dataset.orderId;

                            fetch('userFetchOrderSummary.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: 'orderID=' + encodeURIComponent(orderID)
                                })
                                .then(res => res.text())
                                .then(html => {
                                    summaryContainer.innerHTML = html;
                                    summaryContainer.style.display = 'block';
                                    listContainer.style.display = 'none';
                                });
                        });
                    });
                });
        }

        // Back button in order summary
        function goBackToOrders() {
            loadOrdersList(); // reload list and reattach handlers
        }

        // When "My Orders" sidebar clicked
        document.querySelector('a[data-section="orders"]').addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll(".profile-sidebar a").forEach(a => a.classList.remove("active"));
            this.classList.add("active");

            document.querySelectorAll(".profile-content > div").forEach(div => div.style.display = "none");
            document.getElementById("section-orders").style.display = "block";

            loadOrdersList();
        });
    </script>
</body>

</html>