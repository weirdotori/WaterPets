<?php
session_start();
require_once "db.php";

$name = '';
$email = '';
$subjectType = '';
$message = '';

if (isset($_SESSION['user'])) {
    $name = $_SESSION['user']['username'];
    $email = $_SESSION['user']['email'];
}
$userID = isset($_SESSION['user']) ? $_SESSION['user']['userID'] : null;




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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body>


    <section class="contact-section">
        <?php include 'header.php'; ?>
        <hr class="header-divider">

        <div class="contact-wrapper">
            <!-- Left: Info -->
            <div class="contact-info">
                <h3>Our Information</h3>
                <p><strong>Address:</strong> No.230, Rose Road, Yangon, MM </p>
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
                <!-- Google Map embed -->
                <div class="contact-map">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3818.434519407103!2d96.17932019999998!3d16.8543865!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30c1937fd20ba547%3A0xdf6077ff69d92f12!2sAquarium%20World!5e0!3m2!1sen!2smm!4v1755515397808!5m2!1sen!2smm" width="100%" height="250" style="border:0; border-radius:10px;" allowfullscreen="" loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>

            </div>

            <!-- Right: Form -->
            <div class="contact-container">
                <h2>Contact Us</h2>



                <form method="POST" action="contactus.php" class="contact-form">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" required value="<?= htmlspecialchars($name) ?>">

                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required value="<?= htmlspecialchars($email) ?>">

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

<script>
    const form = document.querySelector('.contact-form');
    const isLoggedIn = <?= isset($_SESSION['user']) ? 'true' : 'false' ?>;

    form.addEventListener('submit', function(e) {
        if (!isLoggedIn) {
            e.preventDefault(); // prevent form submission

            // Create popup
            const popup = document.createElement('div');
            popup.style.position = 'fixed';
            popup.style.top = '0';
            popup.style.left = '0';
            popup.style.width = '100%';
            popup.style.height = '100%';
            popup.style.backgroundColor = 'rgba(0,0,0,0.7)';
            popup.style.display = 'flex';
            popup.style.flexDirection = 'column';
            popup.style.justifyContent = 'center';
            popup.style.alignItems = 'center';
            popup.style.zIndex = '10000';
            popup.style.color = '#fff';
            popup.style.textAlign = 'center';
            popup.style.padding = '20px';
            popup.innerHTML = `
                <h2>Please Login or Register</h2>
                <p>You need to be logged in to submit an inquiry.</p>
                <div style="margin-top: 20px;">
                    <a href="userLogin.php" style="padding: 10px 20px; margin: 5px; background: #fcd34d; color: #000; border-radius: 5px; text-decoration: none;">Login</a>
                    <a href="register.php" style="padding: 10px 20px; margin: 5px; background: #34d1fc; color: #000; border-radius: 5px; text-decoration: none;">Register</a>
                    <button id="close-popup" style="padding: 10px 20px; margin: 5px; border: none; border-radius: 5px; background: #ff5c5c; color: #fff; cursor: pointer;">Close</button>
                </div>
            `;
            document.body.appendChild(popup);

            document.getElementById('close-popup').addEventListener('click', () => {
                popup.remove();
            });
        }
    });
</script>