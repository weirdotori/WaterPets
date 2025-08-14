<?php
session_start();
require_once "db.php";

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    echo "<p>Please <a href='login.php'>login</a> to submit an inquiry.</p>";
    exit;
}
$userID = $_SESSION['user']['userID'];

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subjectType = $_POST['subjectType'] ?? '';
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($subjectType) || empty($message)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO inquiry (userID, name, email, subjectType, message, status, created_at) VALUES (?, ?, ?, ?, ?, 'Pending', NOW())");
        if ($stmt->execute([$userID, $name, $email, $subjectType, $message])) {
            $success = "Your inquiry has been submitted successfully.";


            $name = $_SESSION['user']['username'];
            $email = $_SESSION['user']['email'];
            $subjectType = '';
            $message = '';
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Contact Us - WaterPets</title>
    <link rel="stylesheet" href="/css/contactus_style.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <section class="contact-section">
        <div class="contact-container">
            <h2>Contact Us</h2>

            <?php if ($success): ?>
                <p class="success-msg"><?= htmlspecialchars($success) ?></p>
            <?php elseif ($error): ?>
                <p class="error-msg"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="POST" action="contactus.php" class="contact-form">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" required value="<?= htmlspecialchars($name ?? $_SESSION['user']['username']) ?>">

                <label for="email">Email</label>
                <input type="email" name="email" id="email" required value="<?= htmlspecialchars($email ?? $_SESSION['user']['email']) ?>">
                <label for="subjectType">Subject Type</label>
                <select name="subjectType" id="subjectType" required>
                    <option value="">-- Select Subject Type --</option>
                    <option value="Question" <?= ($subjectType ?? '') === 'Question' ? 'selected' : '' ?>>Question</option>
                    <option value="Complaint" <?= ($subjectType ?? '') === 'Complaint' ? 'selected' : '' ?>>Complaint</option>
                    <option value="Feedback" <?= ($subjectType ?? '') === 'Feedback' ? 'selected' : '' ?>>Feedback</option>
                </select>

                <label for="message">Message</label>
                <textarea name="message" id="message" rows="6" required><?= htmlspecialchars($message ?? '') ?></textarea>

                <button type="submit">Submit Inquiry</button>
            </form>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>

</html>