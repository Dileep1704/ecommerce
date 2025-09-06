<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

// Update online status
$pdo->prepare("
    INSERT INTO chat_status (user_id, is_online, last_seen)
    VALUES (?, 1, NOW())
    ON DUPLICATE KEY UPDATE is_online = 1, last_seen = NOW()
")->execute([$_SESSION['user_id']]);

// Handle new message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($_POST['message']))) {
    $stmt = $pdo->prepare("INSERT INTO chat_messages (user_id, message, is_admin) VALUES (?, ?, 0)");
    $stmt->execute([$_SESSION['user_id'], trim($_POST['message'])]);
    redirect('chat.php');
}

// Fetch messages
$stmt = $pdo->prepare("
    SELECT cm.*, u.username 
    FROM chat_messages cm
    JOIN users u ON u.id = IF(cm.is_admin = 1, cm.admin_id, cm.user_id)
    WHERE cm.user_id = ?
    ORDER BY cm.created_at ASC
");
$stmt->execute([$_SESSION['user_id']]);
$messages = $stmt->fetchAll();

// Mark unread admin messages as read
$pdo->prepare("UPDATE chat_messages SET is_read = 1 WHERE user_id = ? AND is_admin = 1 AND is_read = 0")
    ->execute([$_SESSION['user_id']]);
?>

<?php require_once 'includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Support Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Customer Support Chat</h5>
                <div class="d-flex align-items-center">
                    <div class="online-status me-2"></div>
                    <small id="admin-status">Checking admin status...</small>
                </div>
            </div>

            <div class="card-body chat-messages" style="height: 400px; overflow-y: auto;">
                <?php if (empty($messages)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-chat-square-text fs-1"></i>
                        <p>Start a conversation with our support team</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($messages as $message): ?>
                        <div class="mb-3 <?= $message['is_admin'] ? 'text-start' : 'text-end' ?>">
                            <div class="d-flex <?= $message['is_admin'] ? 'justify-content-start' : 'justify-content-end' ?>">
                                <div class="p-3 rounded <?= $message['is_admin'] ? 'bg-light' : 'bg-primary text-white' ?>" style="max-width: 70%;">
                                    <div class="fw-bold"><?= $message['is_admin'] ? 'Admin' : 'You' ?></div>
                                    <div><?= htmlspecialchars($message['message']) ?></div>
                                    <small class="<?= $message['is_admin'] ? 'text-muted' : 'text-white-50' ?>">
                                        <?= date('M j, g:i a', strtotime($message['created_at'])) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="card-footer">
                <form method="post" class="d-flex">
                    <input type="text" name="message" class="form-control" placeholder="Type your message..." required>
                    <button type="submit" class="btn btn-primary ms-2">
                        <i class="bi bi-send"></i> Send
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Scroll to latest message
    const chatContainer = document.querySelector('.chat-messages');
    if (chatContainer) chatContainer.scrollTop = chatContainer.scrollHeight;

    // Admin status check
    function checkAdminStatus() {
        fetch('check_admin_online.php')
            .then(res => res.json())
            .then(data => {
                const statusText = document.getElementById('admin-status');
                const statusDot = document.querySelector('.online-status');
                if (data.isOnline) {
                    statusText.textContent = 'Admin is online';
                    statusDot.innerHTML = '<span class="badge bg-success rounded-pill p-1"></span>';
                } else {
                    statusText.textContent = 'Admin is offline';
                    statusDot.innerHTML = '<span class="badge bg-secondary rounded-pill p-1"></span>';
                }
            });
    }

    checkAdminStatus();
    setInterval(checkAdminStatus, 5000);
    </script>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>
