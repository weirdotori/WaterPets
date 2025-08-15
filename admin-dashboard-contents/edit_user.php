<?php
require_once 'db.php'; // DB connection

// 1. Get user ID
$userID = $_GET['id'] ?? null;
if (!$userID) {
    echo "<div class='alert-danger'>No user selected.</div>";
    exit;
}

// 2. Fetch user info
$stmt = $conn->prepare("SELECT userID, username, email, phone, role, profile_pic FROM users WHERE userID = ?");
$stmt->execute([$userID]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<div class='alert-danger'>User not found.</div>";
    exit;
}

// 3. Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $role = $_POST['role'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    // Optional: handle profile picture upload
    // Optional: handle profile picture upload (overwrite old file)
    if (!empty($_FILES['profile_pic']['name'])) {
        $targetDir = "uploads/profile_pics/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        // Use the same filename as current to overwrite
        $oldFile = $user['profile_pic']; // existing file path in DB
        $targetFile = $oldFile ? ltrim($oldFile, '/') : $targetDir . $userID . "_" . basename($_FILES['profile_pic']['name']);

        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFile)) {
            $profilePicPath = '/' . $targetFile; // save updated path
        } else {
            $profilePicPath = $user['profile_pic']; // fallback keep old
        }
    } else {
        $profilePicPath = $user['profile_pic']; // keep old
    }


    // Update query
    if (!empty($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, phone=?, role=?, profile_pic=?, password=? WHERE userID=?");
        $stmt->execute([$username, $email, $phone, $role, $profilePicPath, $hashedPassword, $userID]);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, phone=?, role=?, profile_pic=? WHERE userID=?");
        $stmt->execute([$username, $email, $phone, $role, $profilePicPath, $userID]);
    }

    echo "<script>alert('User updated successfully'); window.location.href='?page=manage_users';</script>";
    exit;
}
?>

<div class="edit-user-container">
    <h2>Edit User</h2>
    <form method="POST" enctype="multipart/form-data" class="edit-user-form">
        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label>Phone</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">

        <label>Role</label>
        <select name="role" required>
            <option value="customer" <?= $user['role'] == 'customer' ? 'selected' : '' ?>>Customer</option>
            <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>


        <label>Profile Picture</label>
        <img src="<?= !empty($user['profile_pic']) ? $user['profile_pic'] : '/uploads/default.png' ?>" alt="Profile" class="edit-user-pic">
        <input type="file" name="profile_pic">

        <label>Reset Password</label>
        <input type="password" name="new_password" placeholder="Enter new password (optional)">

        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="?page=manage_users" class="btn btn-secondary">Cancel</a>
    </form>
</div>