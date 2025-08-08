<?php
session_start();
require_once "db.php";

header("Content-Type: application/json");

// Check log in verification for admin and customer
if (
    empty($_SESSION['userID']) ||
    empty($_SESSION['role']) ||
    !in_array($_SESSION['role'], ['admin', 'customer'])
) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}
// Check file
if (!isset($_FILES['profile_pic']) || $_FILES['profile_pic']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["success" => false, "message" => "Please select a valid image file"]);
    exit;
}

// Allowed extensions
$allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
$ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowedExts)) {
    echo json_encode(["success" => false, "message" => "Invalid file type"]);
    exit;
}

// Upload folder
$uploadDir = __DIR__ . "/uploads/profile_pics/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Create unique filename
$fileName = "user_" . $_SESSION['userID'] . "_" . time() . "." . $ext;
$filePath = $uploadDir . $fileName;

// Move file
if (!move_uploaded_file($_FILES['profile_pic']['tmp_name'], $filePath)) {
    echo json_encode([
        "success" => false,
        "message" => "Failed to upload file. PHP error code: " . $_FILES['profile_pic']['error']
    ]);
    exit;
}

// File path for database (relative URL)
$dbPath = "/uploads/profile_pics/" . $fileName;

// --- DELETE OLD PROFILE PICTURE FILE ---
try {
    // Get old profile picture path from DB
    $stmt = $conn->prepare("SELECT profile_pic FROM users WHERE userID = ?");
    $stmt->execute([$_SESSION['userID']]);
    $oldPic = $stmt->fetchColumn();

    if ($oldPic && $oldPic !== $dbPath && $oldPic !== '/images/default-profile.jpg') {
        $oldFile = __DIR__ . $oldPic;
        if (file_exists($oldFile)) {
            unlink($oldFile); // Delete old file
        }
    }
} catch (Exception $e) {
    // Log error or ignore
}

// Update DB
$stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE userID = ?");
$stmt->execute([$dbPath, $_SESSION['userID']]);

// Update session
$_SESSION['profile_pic'] = $dbPath;

echo json_encode([
    "success" => true,
    "message" => "Profile picture updated successfully!",
    "newImage" => $dbPath
]);
