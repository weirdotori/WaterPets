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

    <!-- Tailwind css -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>


    <section class="contact-section">
        <?php include 'header.php'; ?>
        <hr class="header-divider">

        <div class="contact-wrapper">
            <!-- Left: Info -->
            <div class="contact-info">
                <h3>Our Information</h3>
                <p><strong>Address:</strong> 123 Coral Reef Street, Ocean City, Myanmar</p>
                <p><strong>Phone:</strong> +95 9 123 456 789</p>
                <p><strong>Email:</strong> support@waterpets.com</p>

                <div class="social-media">
                    <a href="https://facebook.com" target="_blank">Facebook</a>
                    <a href="https://instagram.com" target="_blank">Instagram</a>
                    <a href="https://twitter.com" target="_blank">Twitter</a>
                    <a href="https://youtube.com" target="_blank">YouTube</a>
                    <a href="https://youtube.com" target="_blank">Discord</a>
                    <a href="https://youtube.com" target="_blank">Telegram</a>
                </div>
                <!-- Small image below social media -->
                <div class="contact-image">
                    <img src="/images/oystergif.gif" alt="WaterPets Logo">
                </div>
            </div>

            <!-- Right: Form -->
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
                    <textarea name="message" id="message" rows="6" placeholder="Reach out to us with detailed information."><?= htmlspecialchars($message ?? '') ?></textarea>

                    <button type="submit">Submit Inquiry</button>
                </form>
            </div>
        </div>

    </section>

    <?php include 'footer.php'; ?>

    <?php include 'chatbot.php'; ?>
    <?php include 'backToTop.php'; ?>
</body>

</html>