<?php
require_once 'includes/db.php';

// Fetch the latest admin online status
$stmt = $pdo->prepare("
    SELECT is_online 
    FROM chat_status 
    WHERE is_admin = 1 
    ORDER BY last_seen DESC 
    LIMIT 1
");
$stmt->execute();
$adminStatus = $stmt->fetch();

header('Content-Type: application/json');
echo json_encode([
    'isOnline' => $adminStatus && $adminStatus['is_online'] == 1
]);
