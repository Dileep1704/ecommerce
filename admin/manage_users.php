<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../index.php');
}

// Handle user actions (delete or toggle admin)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

    if ($stmt->fetch()) {
        if ($user_id !== $_SESSION['user_id']) {
            switch ($_GET['action']) {
                case 'delete':
                    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$user_id]);
                    $_SESSION['success'] = "User deleted successfully.";
                    break;

                case 'toggle_admin':
                    $pdo->prepare("UPDATE users SET is_admin = NOT is_admin WHERE id = ?")->execute([$user_id]);
                    $_SESSION['success'] = "User role updated.";
                    break;
            }
        } else {
            $_SESSION['error'] = "You cannot modify your own privileges.";
        }
    }

    redirect('manage_users.php');
}

// Fetch all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

require_once 'header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Manage Users</h2>
        <a href="add_user.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Add User</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Name</th>
                    <th>Registered</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars(trim($user['first_name'] . ' ' . $user['last_name'])) ?></td>
                        <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <span class="badge <?= $user['is_admin'] ? 'bg-danger' : 'bg-primary' ?>">
                                <?= $user['is_admin'] ? 'Admin' : 'User' ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
							<a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-primary" title="Edit User">
							<i class="bi bi-pencil-square"></i>
								</a>
                                <a href="?action=toggle_admin&id=<?= $user['id'] ?>" 
                                   class="btn btn-sm <?= $user['is_admin'] ? 'btn-warning' : 'btn-success' ?>" 
                                   title="<?= $user['is_admin'] ? 'Revoke Admin' : 'Make Admin' ?>">
                                    <i class="bi bi-<?= $user['is_admin'] ? 'person-x' : 'person-check' ?>"></i>
                                </a>
                                <a href="?action=delete&id=<?= $user['id'] ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this user?')"
                                   title="Delete User">
                                    <i class="bi bi-trash"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">You</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

