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

    <link rel="stylesheet" href="/css/userProfile_style.css">

</head>

<body>

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
                <li>
                    <button type="button" class="sidebar-back-btn" onclick="window.location.href='home.php'">← Back</button>
                </li>


            </ul>
        </div>

        <!-- Profile Content -->
        <div class="profile-content">

            <!-- Profile Overview -->
            <div id="section-overview">
                <h3>Profile Overview</h3>
                <div class="profile-card text-center">
                    <img src="<?= htmlspecialchars($user['profile_pic'] ?? '/images/user.png') ?>"
                        id="currentProfilePreview"
                        class="profile-pic">
                    <form id="userProfileForm" enctype="multipart/form-data" class="profile-upload-form">
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
                <form id="personalInfoForm" action="userUpdateInfo.php" method="POST" class="info-form">

                    <!-- Username -->
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="editable-field" value="<?= htmlspecialchars($user['username']) ?>" readonly required>
                        <img src="/images/edit.png" class="edit-icon" title="Edit Username">
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="editable-field" value="<?= htmlspecialchars($user['email']) ?>" readonly required>
                        <img src="/images/edit.png" class="edit-icon" title="Edit Email">
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="editable-field" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" readonly>
                        <img src="/images/edit.png" class="edit-icon" title="Edit Phone">
                    </div>

                    <!-- Role (not editable) -->
                    <div class="form-group">
                        <label>Role</label>
                        <input type="text" value="<?= htmlspecialchars(ucfirst($user['role'])) ?>" readonly class="readonly-field">
                    </div>

                    <button type="submit" class="btn-success">Save Changes</button>
                </form>

            </div>

            <!-- Orders Section -->
            <div id="section-orders" style="display:none;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3>My Orders</h3>
                    <button id="view-processing-orders-btn" style="padding:6px 12px; font-size:14px; cursor:pointer;">View Processing Orders</button>
                </div>
                <div class="orders-list-container"></div>
                <div class="order-summary-container" style="display:none;"></div>
            </div>


            <!-- Security Settings -->
            <div id="section-security" style="display:none;">
                <h3>Security Settings</h3>

                <!-- Normal Security Content -->
                <div id="securityContent">
                    <!-- Password Row -->
                    <div class="form-group password-row">
                        <label>Password</label>
                        <input type="password" value="********" readonly class="readonly-field">
                        <img src="/images/edit.png" class="edit-icon" id="editPasswordBtn" title="Change Password">
                    </div>

                    <!-- Forgot Password -->
                    <div class="forgot-password">
                        <a href="forgot-password.php">Forgot Password?</a>
                    </div>

                    <!-- Recent Login Activity -->
                    <div class="form-group">
                        <label>Recent Logins</label>
                        <ul class="login-activity">
                            <li>2025-08-15 14:32 - IP: 192.168.1.2</li>
                            <li>2025-08-12 09:17 - IP: 192.168.1.5</li>
                        </ul>
                    </div>


                </div>

                <!-- Password Change Form -->
                <form id="changePasswordForm" action="userUpdatePassword.php" method="POST" class="info-form" style="display:none;">
                    <button type="button" id="backToSecurity" class="btn-back">← Back</button>

                    <!-- Message Display -->
                    <div id="securityMsg" class="noti-msg"></div>

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



            <!-- Loyalty Program -->
            <div id="section-loyalty" style="display:none;">
                <h3>Loyalty Program & Coupons</h3>
                <?php if (count($coupons) === 0): ?>
                    <p style="color: white;">You don’t have any coupons yet. Place 3 orders to unlock your first coupon!</p>
                <?php else: ?>
                    <div class="coupons-list">
                        <?php foreach ($coupons as $coupon): ?>
                            <div class="coupon-card <?= $coupon['status'] === 'used' ? 'used-coupon' : '' ?>">
                                <div class="coupon-row">
                                    <span class="coupon-code"><?= htmlspecialchars($coupon['code']) ?></span>
                                    <span class="coupon-desc"><?= $coupon['discount'] ?>% OFF</span>
                                    <span class="coupon-status"><?= ucfirst($coupon['status']) ?></span>
                                </div>
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

        // Show Change Password form
        document.getElementById("editPasswordBtn").addEventListener("click", function() {
            document.getElementById("securityContent").style.display = "none"; // hide normal security content
            document.getElementById("changePasswordForm").style.display = "block"; // show form

            // Make inputs editable
            document.querySelectorAll('#changePasswordForm input').forEach(input => {
                input.removeAttribute('readonly');
                input.style.pointerEvents = "auto";
            });
        });

        // Back button in Change Password form
        document.getElementById("backToSecurity").addEventListener("click", function() {
            document.getElementById("changePasswordForm").style.display = "none"; // hide form
            document.getElementById("securityContent").style.display = "block"; // show normal content
        });

        // Handle Change Password form submission via AJAX
        document.getElementById("changePasswordForm").addEventListener("submit", function(e) {
            e.preventDefault(); // prevent page reload

            const form = e.target;
            const formData = new FormData(form);
            const msgDiv = document.getElementById("securityMsg");

            fetch("userUpdatePassword.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        msgDiv.innerHTML = `<span class="text-success">${data.message}</span>`;
                        form.reset(); // clear inputs
                    } else {
                        msgDiv.innerHTML = `<span class="text-danger">${data.message}</span>`;
                    }
                })
                .catch(err => {
                    console.error(err);
                    msgDiv.innerHTML = `<span class="text-danger">Something went wrong. Please try again.</span>`;
                });
        });




        // Delete account confirmation
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

        // No reload when saving changes in personal info
        document.getElementById("personalInfoForm").addEventListener("submit", function(e) {
            e.preventDefault(); // prevent page reload

            const form = e.target;
            const formData = new FormData(form);

            fetch("userUpdateInfo.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json()) // userUpdateInfo.php will return JSON
                .then(data => {
                    const msgDiv = document.getElementById("personalInfoMsg");
                    if (data.success) {
                        msgDiv.innerHTML = `<span class="text-success">${data.message}</span>`;
                        // Update username in session display if needed
                        // e.g., document.querySelector('#usernameDisplay').textContent = formData.get('username');
                    } else {
                        msgDiv.innerHTML = `<span class="text-danger">${data.message}</span>`;
                    }
                })
                .catch(err => console.error(err));
        });
    </script>

    <script>
        const ordersContainer = document.querySelector('.orders-list-container');
        const viewProcessingBtn = document.getElementById('view-processing-orders-btn');
        const summaryContainer = document.querySelector('.order-summary-container');

        // Load My Orders
        async function loadMyOrders() {
            const response = await fetch('userFetchOrdersList.php');
            const html = await response.text();
            ordersContainer.innerHTML = html;
            ordersContainer.style.display = 'block';
            summaryContainer.style.display = 'none';

            // Attach "View Summary" buttons
            ordersContainer.querySelectorAll('.view-order-btn').forEach(btn => {
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
                            ordersContainer.style.display = 'none';
                        });
                });
            });
        }

        // Load Processing Orders
        async function loadProcessingOrders() {
            const response = await fetch('userFetchProcessingOrders.php');
            const html = await response.text();
            ordersContainer.innerHTML = html;

            // Back button
            const backBtn = document.createElement('button');
            backBtn.textContent = '← Back to My Orders';
            backBtn.style = 'padding:6px 12px; font-size:14px; cursor:pointer; margin-bottom:10px;';
            ordersContainer.prepend(backBtn);

            backBtn.addEventListener('click', () => {
                loadMyOrders(); // important: reattach handlers
            });
        }

        // Cancel order delegation
        ordersContainer.addEventListener('click', async (e) => {
            if (e.target.classList.contains('cancel-order-btn')) {
                const orderID = e.target.dataset.orderId;
                if (!confirm('Are you sure you want to cancel this order?')) return;

                const formData = new FormData();
                formData.append('orderID', orderID);

                const response = await fetch('userCancelOrder.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    const orderCard = document.getElementById('order-' + orderID);
                    if (orderCard) {
                        const statusSpan = orderCard.querySelector('.order-status');
                        if (statusSpan) statusSpan.textContent = 'Cancelled';
                        e.target.remove();
                    }
                    alert(data.message);
                } else {
                    alert(data.message || 'Unable to cancel order.');
                }
            }
        });

        // Initial load
        loadMyOrders();

        // Click listener to switch to Processing Orders
        viewProcessingBtn.addEventListener('click', loadProcessingOrders);
    </script>



</body>

</html>