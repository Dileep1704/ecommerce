<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../index.php');
}

// Get user ID
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = "User not found.";
    redirect('manage_users.php');
}

$errors = [];
$success = false;

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $address = trim($_POST['address']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($username) || empty($email)) {
        $errors[] = "Username and email are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Check password match if filled
    if (!empty($new_password)) {
        if (strlen($new_password) < 8) {
            $errors[] = "Password must be at least 8 characters.";
        }
        if ($new_password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        }
    }

    if (empty($errors)) {
        try {
            // Build update query
            $sql = "UPDATE users SET username = :username, email = :email, first_name = :first_name, last_name = :last_name, address = :address";
            $params = [
                ':username' => $username,
                ':email' => $email,
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':address' => $address,
                ':id' => $user_id
            ];

            if (!empty($new_password)) {
                $sql .= ", password = :password";
                $params[':password'] = password_hash($new_password, PASSWORD_DEFAULT);
            }

            $sql .= " WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $success = true;
            $user = array_merge($user, $_POST); // reflect updates in form
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                $errors[] = "Username or email already exists.";
            } else {
                $errors[] = "Error updating user.";
            }
        }
    }
}

require_once 'header.php';
?>

<div class="container mt-4">
    <h2>Edit User</h2>

    <?php if ($success): ?>
        <div class="alert alert-success">User updated successfully!</div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e): ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control"><?= htmlspecialchars($user['address']) ?></textarea>
        </div>

        <hr>
        <h5>Change Password (optional)</h5>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">New Password</label>
                <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep unchanged">
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Repeat new password">
            </div>
        </div>

        <div class="text-end">
            <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Update User</button>
        </div>
    </form>
</div>


