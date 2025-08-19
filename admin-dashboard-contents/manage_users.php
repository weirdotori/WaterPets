<?php
require_once 'db.php';

//  Handle Delete Request
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE userID = ?");
    if ($stmt->execute([$delete_id])) {
        $_SESSION['msg'] = "User deleted successfully.";
    } else {
        $_SESSION['msg'] = "Error deleting user.";
    }
    header("Location: ?page=manage_users");
    exit;
}

//Fetch all users
$stmt = $conn->query("SELECT * FROM users ORDER BY userID ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="users-container">
    <h2 class="users-title">Manage Users</h2>
    <p>View, Edit, and Delete User accounts.</p>

    <?php if (isset($_SESSION['msg'])): ?>
        <div class="users-alert"><?php echo $_SESSION['msg'];
                                    unset($_SESSION['msg']); ?></div>
    <?php endif; ?>
    <table class="users-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Profile</th>
                <th>Username</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $row): ?>
                <tr>
                    <td><?= $row['userID'] ?></td>
                    <td>
                        <img src="<?= !empty($row['profile_pic']) ? $row['profile_pic'] : '/uploads/default.png' ?>"
                            alt="Profile" class="users-pic">
                    </td>



                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['role']) ?></td>
                    <td>
                        <a href="?page=edit_user&id=<?= $row['userID'] ?>" class="users-btn edit">Edit</a>
                        <a href="?page=manage_users&delete_id=<?= $row['userID'] ?>"
                            onclick="return confirm('Are you sure you want to delete this user?')"
                            class="users-btn delete">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>