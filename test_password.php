<?php
require_once 'includes/db.php'; // adjust path if needed

$username = 'admin';
$password_attempt = 'admin123'; // change this to the password you want to test

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user) {
    echo "❌ No user found with username '$username'.";
    exit;
}

echo "<h3>User Found:</h3><pre>";
print_r($user);
echo "</pre>";

$stored_hash = $user['password'];

echo "<h3>Password Hash in DB:</h3><pre>$stored_hash</pre>";

if (password_verify($password_attempt, $stored_hash)) {
    echo "<h2 style='color: green;'>✅ Password is correct!</h2>";
} else {
    echo "<h2 style='color: red;'>❌ Password is incorrect!</h2>";
}
