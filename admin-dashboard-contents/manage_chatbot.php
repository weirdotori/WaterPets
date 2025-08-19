<?php
require_once "db.php";

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_chatbot'])) {
    $id = $_POST['chatbotID'];
    $question = $_POST['question'];
    $answer = $_POST['answer'];

    $stmt = $conn->prepare("UPDATE chatbot SET question = ?, answer = ?, updated_at = NOW() WHERE chatbotID = ?");
    if ($stmt->execute([$question, $answer, $id])) {
        $_SESSION['msg'] = "Chatbot entry #$id updated successfully.";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['msg'] = "Failed to update chatbot entry #$id.";
        $_SESSION['msg_type'] = "error";
    }

    echo "<script>window.location.href='?page=manage_chatbot';</script>";
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM chatbot WHERE chatbotID = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['msg'] = "Chatbot entry deleted successfully.";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['msg'] = "Failed to delete chatbot entry.";
        $_SESSION['msg_type'] = "error";
    }

    echo "<script>window.location.href='?page=manage_chatbot';</script>";
    exit;
}

// Fetch chatbot entries
$chatbotStmt = $conn->query("SELECT * FROM chatbot ORDER BY created_at DESC");
$chatbots = $chatbotStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-page-header">
    <h2>Manage Chatbot</h2>
    <a href="?page=add_chatbot" class="btn-add">+ Add New Entry</a>
</div>

<?php if (isset($_SESSION['msg'])): ?>
    <div class="inquiries-alert <?= $_SESSION['msg_type'] ?>">
        <?= $_SESSION['msg'] ?>
    </div>
    <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
<?php endif; ?>

<?php if (count($chatbots) === 0): ?>
    <p>No chatbot entries found. Click "Add New Entry" to create one.</p>
<?php else: ?>
    <table class="faq-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Question</th>
                <th>Answer</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($chatbots as $entry): ?>
                <tr>
                    <form method="POST">
                        <td><?= htmlspecialchars($entry['chatbotID']) ?></td>
                        <td>
                            <input type="text" name="question" value="<?= htmlspecialchars($entry['question']) ?>" required>
                        </td>
                        <td>
                            <textarea name="answer" required><?= htmlspecialchars($entry['answer']) ?></textarea>
                        </td>
                        <td><?= htmlspecialchars($entry['created_at']) ?></td>
                        <td><?= htmlspecialchars($entry['updated_at']) ?></td>
                        <td>
                            <input type="hidden" name="chatbotID" value="<?= $entry['chatbotID'] ?>">
                            <button type="submit" name="update_chatbot" class="btn-update">Update</button>
                            <a href="?page=manage_chatbot&delete=<?= $entry['chatbotID'] ?>" 
                               onclick="return confirm('Are you sure you want to delete this entry?')" 
                               class="btn-delete">Delete</a>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
