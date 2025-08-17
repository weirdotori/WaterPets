<?php
require_once "db.php";

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_chatbot'])) {
    $question = trim($_POST['question'] ?? '');
    $answer = trim($_POST['answer'] ?? '');

    if (empty($question) || empty($answer)) {
        $error = "Please fill in both Question and Answer fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO chatbot (question, answer, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
        if ($stmt->execute([$question, $answer])) {
            $success = "Chatbot entry added successfully!";
            $question = '';
            $answer = '';
        } else {
            $error = "Failed to add entry. Please try again.";
        }
    }
}
?>

<div class="admin-page-header">
    <h2>Add New Chatbot Entry</h2>
    <a href="?page=manage_chatbot" class="btn-back">← Back to Chatbot</a>
</div>

<?php if ($success): ?>
    <div class="success-msg"><?= htmlspecialchars($success) ?></div>
<?php elseif ($error): ?>
    <div class="error-msg"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" class="faq-form">
    <label for="question">Question</label>
    <input type="text" name="question" id="question" value="<?= htmlspecialchars($question ?? '') ?>" required>

    <label for="answer">Answer</label>
    <textarea name="answer" id="answer" rows="6" required><?= htmlspecialchars($answer ?? '') ?></textarea>

    <button type="submit" name="add_chatbot" class="btn-submit">Add Entry</button>
</form>
