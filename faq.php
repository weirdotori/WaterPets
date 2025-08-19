<?php
session_start();
require_once "db.php";

// Fetch all FAQs
try {
    $stmt = $conn->prepare("SELECT faqID, question, answer FROM faqs ORDER BY created_at DESC");
    $stmt->execute();
    $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>FAQs - WaterPets</title>
    <!-- Css Style -->
    <link rel="stylesheet" href="/css/faqs_style.css">

    <!-- Tailwind css -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

    <section class="faq-section">
        <?php include 'header.php'; ?>
        <hr class="header-divider">

        <div class="faq-container">
            <h2>Frequently Asked Questions</h2>

            <?php if (empty($faqs)): ?>
                <p>No FAQs available at the moment.</p>
            <?php else: ?>
                <?php foreach ($faqs as $faq): ?>
                    <div class="faq-item">
                        <div class="faq-question"><?= htmlspecialchars($faq['question']) ?></div>
                        <div class="faq-answer"><?= nl2br(htmlspecialchars($faq['answer'])) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

   
    <?php include 'footer.php'; ?>
    <?php include 'chatbot.php'; ?>
    <?php include 'backToTop.php'; ?>

    <script>
        // Simple toggle for answers
        const questions = document.querySelectorAll('.faq-question');
        questions.forEach(q => {
            q.addEventListener('click', () => {
                q.classList.toggle('active');
                const answer = q.nextElementSibling;
                answer.style.display = answer.style.display === 'block' ? 'none' : 'block';
            });
        });
    </script>
</body>

</html>