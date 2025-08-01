<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['adminLogin']) || $_SESSION['role'] !== 'admin') {
    header("Location: adminLogin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $file = $_FILES['profile_pic'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allowed)) {
            $newName = 'admin_' . $_SESSION['userid'] . '.' . $ext;
            $uploadPath = __DIR__ . '/uploads/profile_pics/' . $newName;

            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                // Save in database
                $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE userid = ?");
                $stmt->execute(['/uploads/profile_pics/' . $newName, $_SESSION['userid']]);

                $_SESSION['profile_pic'] = '/uploads/profile_pics/' . $newName;
                $message = "Profile picture updated successfully!";
            }
        } else {
            $message = "Invalid file type. Only JPG, PNG, GIF allowed.";
        }
    } else {
        $message = "Error uploading file.";
    }
}

if (isset($_POST['remove_pic'])) {
    $stmt = $conn->prepare("UPDATE users SET profile_pic = NULL WHERE userid = ?");
    $stmt->execute([$_SESSION['userid']]);
    $_SESSION['profile_pic'] = null;
    $message = "Profile picture removed.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
    <h2>Manage Profile Picture</h2>
    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <div class="mb-3">
        <img src="<?= htmlspecialchars($_SESSION['profile_pic'] ?? '/images/admin-profile.jpg') ?>" 
             alt="Profile" 
             class="rounded-circle" 
             style="width:100px; height:100px; object-fit:cover;">
    </div>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <input type="file" name="profile_pic" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Upload New Picture</button>
        <button type="submit" name="remove_pic" class="btn btn-danger">Remove Picture</button>
    </form>
</body>
</html>
