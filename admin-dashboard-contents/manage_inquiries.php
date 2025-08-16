<?php
require_once "db.php";

// Handle update (only status editable)
if (isset($_POST['update_id'])) {
    $id = intval($_POST['update_id']);
    $status = $_POST['status'] === 'Resolved' ? 'Resolved' : 'Pending'; // sanitize enum

    $stmt = $conn->prepare("UPDATE inquiry SET status = ? WHERE inquiryID = ?");
    $stmt->execute([$status, $id]);

    $_SESSION['msg'] = "Inquiry #$id updated successfully.";
    header("Location: ?page=manage_inquiries");
    exit;
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM inquiry WHERE inquiryID = ?");
    $stmt->execute([$id]);
    $_SESSION['msg'] = "Inquiry deleted.";
    header("Location: ?page=manage_inquiries");
    exit;
}

// Fetch all inquiries
$stmt = $conn->query("SELECT * FROM inquiry ORDER BY created_at DESC");
$inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="inquiries-container">
    <h2 class="inquiries-title">Manage Inquiries</h2>

    <?php if (isset($_SESSION['msg'])): ?>
        <div class="inquiries-alert"><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
    <?php endif; ?>

    <table class="inquiries-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($inquiries as $inq): ?>
                <tr>
                    <td><?= $inq['inquiryID'] ?></td>
                    <td><?= $inq['userID'] ?: '-' ?></td>
                    <td><?= htmlspecialchars($inq['name']) ?></td>
                    <td><?= htmlspecialchars($inq['email']) ?></td>
                    <td><?= htmlspecialchars($inq['subjectType']) ?></td>
                    <td class="inquiries-message"><?= nl2br(htmlspecialchars($inq['message'])) ?></td>
                    <td>
                        <form method="post" class="inquiries-form">
                            <input type="hidden" name="update_id" value="<?= $inq['inquiryID'] ?>">
                            <select name="status" class="status-select">
                                <option value="Pending" <?= $inq['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Resolved" <?= $inq['status'] === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                            </select>
                    </td>
                    <td><?= $inq['created_at'] ?></td>
                    <td>
                            <button type="submit" class="inquiries-btn update">Update</button>
                        </form>
                        <a href="?page=manage_inquiries&delete_id=<?= $inq['inquiryID'] ?>"
                           onclick="return confirm('Are you sure you want to delete this inquiry?')"
                           class="inquiries-btn delete">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
