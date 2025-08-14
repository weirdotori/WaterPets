<?php
require_once "db.php";

// Fetch all FAQs
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
                    <td><?= htmlspecialchars($faq['faqID']) ?></td>
                    <td><?= htmlspecialchars($faq['question']) ?></td>
                    <td><?= htmlspecialchars($faq['answer']) ?></td>
                    <td><?= htmlspecialchars($faq['created_at']) ?></td>
                    <td><?= htmlspecialchars($faq['updated_at']) ?></td>

                    <td class="action-buttons">
                        <div class="dropdown">
                            <button class="dropdown-btn">⋮</button>
                            <div class="dropdown-content">
                                <a href="?page=edit_faq&id=<?= $faq['faqID'] ?>">Edit</a>
                                <a href="?page=delete_faq&id=<?= $faq['faqID'] ?>" onclick="return confirm('Are you sure you want to delete this FAQ?')">Delete</a>
                            </div>
                        </div>
                    </td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const dropdownButtons = document.querySelectorAll('.dropdown-btn');

    dropdownButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation(); // prevent body click

            // Close other open dropdowns
            document.querySelectorAll('.dropdown-content').forEach(dc => {
                if (dc !== btn.nextElementSibling) {
                    dc.classList.remove('show');
                }
            });

            // Toggle current dropdown
            btn.nextElementSibling.classList.toggle('show');
        });
    });

    // Close dropdown if clicked outside
    document.addEventListener('click', function () {
        document.querySelectorAll('.dropdown-content').forEach(dc => dc.classList.remove('show'));
    });
});
</script>


