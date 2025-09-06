<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../index.php');
}

$current_user = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

// Handle new message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && $current_user) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO chat_messages (user_id, admin_id, message, is_admin) VALUES (?, ?, ?, 1)");
        $stmt->execute([$current_user, $_SESSION['user_id'], $message]);

        $pdo->prepare("INSERT INTO chat_status (user_id, is_online, last_seen)
                        VALUES (?, 0, NOW())
                        ON DUPLICATE KEY UPDATE last_seen = NOW()")->execute([$current_user]);

        redirect("chat.php?user_id=$current_user");
    }
}

// Mark messages as read
if ($current_user) {
    $stmt = $pdo->prepare("UPDATE chat_messages SET is_read = 1 WHERE user_id = ? AND is_admin = 0");
    $stmt->execute([$current_user]);
}

$users = $pdo->query("SELECT u.id, u.username,
    (SELECT COUNT(*) FROM chat_messages WHERE user_id = u.id AND is_read = 0 AND is_admin = 0) AS unread,
    cs.is_online
FROM users u
LEFT JOIN chat_status cs ON u.id = cs.user_id
WHERE EXISTS (SELECT 1 FROM chat_messages WHERE user_id = u.id)
ORDER BY cs.is_online DESC, (SELECT MAX(created_at) FROM chat_messages WHERE user_id = u.id) DESC
")->fetchAll();

$messages = [];
if ($current_user) {
    $stmt = $pdo->prepare("SELECT cm.*, u.username FROM chat_messages cm
        JOIN users u ON u.id = IF(cm.is_admin = 1, cm.admin_id, cm.user_id)
        WHERE cm.user_id = ?
        ORDER BY cm.created_at ASC");
    $stmt->execute([$current_user]);
    $messages = $stmt->fetchAll();
}

require_once 'header.php';
?>



<div class="container-fluid mt-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5>Customer Chats</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php foreach ($users as $user): ?>
                        <a href="chat.php?user_id=<?= $user['id'] ?>"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= $current_user == $user['id'] ? 'active' : '' ?>">
                            <div>
                                <span class="fw-bold"><?= htmlspecialchars($user['username']) ?></span>
                                <?php if ($user['is_online']): ?>
                                    <span class="badge bg-success ms-2">Online</span>
                                <?php endif; ?>
                            </div>
                            <?php if ($user['unread'] > 0): ?>
                                <span class="badge bg-danger rounded-pill"><?= $user['unread'] ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Chat Window -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5>
                        <?php if ($current_user): ?>
                            Chat with <?= htmlspecialchars($users[array_search($current_user, array_column($users, 'id'))]['username']) ?>
                        <?php else: ?>
                            Select a user to chat
                        <?php endif; ?>
                    </h5>
                </div>

                <div class="card-body chat-container">
                    <?php if ($current_user): ?>
                        <?php foreach ($messages as $message): ?>
                            <div class="mb-3 <?= $message['is_admin'] ? 'text-end' : 'text-start' ?>">
                                <div class="d-flex <?= $message['is_admin'] ? 'justify-content-end' : 'justify-content-start' ?>">
                                    <div class="p-3 rounded <?= $message['is_admin'] ? 'bg-primary text-white' : 'bg-light' ?>" style="max-width: 70%;">
                                        <div class="fw-bold"><?= $message['is_admin'] ? 'You (Admin)' : htmlspecialchars($message['username']) ?></div>
                                        <div><?= htmlspecialchars($message['message']) ?></div>
                                        <small class="text-muted">
                                            <?= date('M j, g:i a', strtotime($message['created_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted mt-5">
                            <i class="bi bi-chat-square-text fs-1"></i>
                            <p>Select a customer to view chat history</p>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($current_user): ?>
                    <div class="card-footer">
                        <form method="post" class="d-flex">
                            <input type="text" name="message" class="form-control" placeholder="Type your message..." required>
                            <button type="submit" class="btn btn-primary ms-2">
                                <i class="bi bi-send"></i> Send
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const chatContainer = document.querySelector('.chat-container');
    if (chatContainer) chatContainer.scrollTop = chatContainer.scrollHeight;
});
</script>

<?php require_once '../includes/footer.php'; ?>