<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../index.php');
}

// Get current admin data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch();

$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($username)) {
        $errors[] = "Username is required";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // Password change validation
    $password_changed = false;
    if (!empty($new_password)) {
        if (!password_verify($current_password, $admin['password'])) {
            $errors[] = "Current password is incorrect";
        }

        if ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match";
        }

        if (strlen($new_password) < 8) {
            $errors[] = "Password must be at least 8 characters";
        }

        $password_changed = true;
    }

    if (empty($errors)) {
        try {
            $sql = "UPDATE users SET username = :username, email = :email";
            $params = [
                'username' => $username,
                'email' => $email,
                'id' => $_SESSION['user_id']
            ];

            if ($password_changed) {
                $sql .= ", password = :password";
                $params['password'] = password_hash($new_password, PASSWORD_DEFAULT);
            }

            $sql .= " WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $_SESSION['username'] = $username;
            $success = true;
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                $errors[] = "Username or email already exists";
            } else {
                $errors[] = "Database error: " . $e->getMessage();
            }
        }
    }
}

require_once 'header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4><i class="bi bi-person-gear"></i> Admin Profile</h4>
                </div>
                <div class="card-body">
                    <?php if ($success): ?>
                        <div class="alert alert-success">Profile updated successfully!</div>
                    <?php endif; ?>
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <div><?= htmlspecialchars($error) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" novalidate>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                       value="<?= htmlspecialchars($admin['username']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?= htmlspecialchars($admin['email']) ?>" required>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3">Change Password (Optional)</h5>

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password">
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                        </div>

                        <button type="submit" class="btn btn-success">Update Profile</button>
                        <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
