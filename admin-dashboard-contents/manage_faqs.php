<?php
require_once "db.php";

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_faq'])) {
    $id = $_POST['faqID'];
    $question = $_POST['question'];
    $answer = $_POST['answer'];

    $stmt = $conn->prepare("UPDATE faqs SET question = ?, answer = ?, updated_at = NOW() WHERE faqID = ?");
    $stmt->execute([$question, $answer, $id]);

     $_SESSION['msg'] = "FAQ #$id updated successfully.";
    header("Location: ?page=manage_faqs&success=updated");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM faqs WHERE faqID = ?");
    $stmt->execute([$id]);

    $_SESSION['msg'] = "Inquiry deleted.";
    header("Location: ?page=manage_faqs&success=deleted");
    exit;
}

// Fetch FAQs
$faqsStmt = $conn->query("SELECT * FROM faqs ORDER BY created_at DESC");
$faqs = $faqsStmt->fetchAll(PDO::FETCH_ASSOC);
?>


<div class="admin-page-header">
    <h2>Manage FAQs</h2>
    <a href="?page=add_faq" class="btn-add">+ Add New FAQ</a>
</div>

<?php if (count($faqs) === 0): ?>
    <p>No FAQs found. Click "Add New FAQ" to create one.</p>
<?php else: ?>
    <?php if (isset($_SESSION['msg'])): ?>
        <div class="inquiries-alert"><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
    <?php endif; ?>
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
            <?php foreach ($faqs as $faq): ?>
                <tr>
                    <form method="POST">
                        <td><?= htmlspecialchars($faq['faqID']) ?></td>
                        <td>
                            <input type="text" name="question" value="<?= htmlspecialchars($faq['question']) ?>" required>
                        </td>
                        <td>
                            <textarea name="answer" required><?= htmlspecialchars($faq['answer']) ?></textarea>
                        </td>
                        <td><?= htmlspecialchars($faq['created_at']) ?></td>
                        <td><?= htmlspecialchars($faq['updated_at']) ?></td>
                        <td>
                            <input type="hidden" name="faqID" value="<?= $faq['faqID'] ?>">
                            <button type="submit" name="update_faq" class="btn-update">Update</button>
                            <a href="?page=manage_faqs&delete=<?= $faq['faqID'] ?>" 
                               onclick="return confirm('Are you sure you want to delete this FAQ?')" 
                               class="btn-delete">Delete</a>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
