<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!is_admin()) redirect('../index.php');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    if (!$username || !$email || !$password || !$confirm) {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email.";
    } elseif ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $hash, $is_admin]);
        $_SESSION['success'] = "User added successfully!";
        redirect('manage_users.php');
    }
}

require_once 'header.php';
?>

<div class="container mt-4">
    <h3>Add User</h3>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger"><?= implode('<br>', $errors) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Username</label>
            <input name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input name="email" type="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input name="password" type="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Confirm Password</label>
            <input name="confirm" type="password" class="form-control" required>
        </div>
        <div class="form-check mb-3">
            <input type="checkbox" name="is_admin" class="form-check-input" id="is_admin">
            <label for="is_admin" class="form-check-label">Is Admin</label>
        </div>
        <button class="btn btn-primary">Add User</button>
    </form>
</div>


