<?php
session_name('admin_session'); // unique name for admin sessions
session_start();
require_once "db.php";

header("Content-Type: application/json");

// Check if admin is logged in
if (empty($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$userID = $_SESSION['admin']['userID'];

// Validate uploaded file
if (!isset($_FILES['profile_pic']) || $_FILES['profile_pic']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["success" => false, "message" => "Please select a valid image file"]);
    exit;
}

$allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
$ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowedExts)) {
    echo json_encode(["success" => false, "message" => "Invalid file type"]);
    exit;
}

$uploadDir = __DIR__ . "/uploads/profile_pics/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$fileName = "admin_" . $userID . "_" . time() . "." . $ext;
$filePath = $uploadDir . $fileName;
$dbPath = "/uploads/profile_pics/" . $fileName;

if (!move_uploaded_file($_FILES['profile_pic']['tmp_name'], $filePath)) {
    echo json_encode(["success" => false, "message" => "Failed to upload file"]);
    exit;
}

// Delete old profile picture
$stmt = $conn->prepare("SELECT profile_pic FROM users WHERE userID = ?");
$stmt->execute([$userID]);
$oldPic = $stmt->fetchColumn();

if ($oldPic && $oldPic !== $dbPath && $oldPic !== '/images/default-profile.jpg') {
    $oldFile = __DIR__ . $oldPic;
    if (file_exists($oldFile)) {
        unlink($oldFile);
    }
}

// Update DB and session
$stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE userID = ?");
$stmt->execute([$dbPath, $userID]);

$_SESSION['admin']['profile_pic'] = $dbPath;

echo json_encode([
    "success" => true,
    "message" => "Admin profile picture updated!",
    "newImage" => $dbPath
]);
