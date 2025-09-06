<?php
session_start();
require_once 'includes/db.php';

$error = '';

// If form submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    echo "<pre>Entered Username: $username</pre>";
    echo "<pre>Entered Password: $password</pre>";

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    echo "<pre>User fetched from DB:\n";
    print_r($user);
    echo "</pre>";

    if ($user) {
        $match = password_verify($password, $user['password']);
        echo "<pre>password_verify result: " . ($match ? '✅ MATCH' : '❌ NO MATCH') . "</pre>";

        if ($match) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];

            echo "<pre>✅ Login successful. Redirecting...</pre>";

            if ($user['is_admin']) {
                header("Location: admin/index.php");
                exit();
            } else {
                header("Location: index.php");
                exit();
            }
        } else {
            $error = 'Invalid username or password';
        }
    } else {
        $error = 'Invalid username or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Login </h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="mx-auto" style="max-width: 400px;">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        <button class="btn btn-primary w-100" type="submit">Login</button>
    </form>
</div>
</body>
</html>
