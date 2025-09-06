<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../index.php');
}

require_once 'header.php';
?>

<div class="container mt-4">
    <h1>Admin Dashboard</h1>
    
    <div class="row mt-4">
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Products</h5>
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
                    $count = $stmt->fetchColumn();
                    ?>
                    <p class="card-text display-4"><?= $count ?></p>
                    <a href="add_product.php" class="text-white">Add New Product</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Total Orders</h5>
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM orders");
                    $count = $stmt->fetchColumn();
                    ?>
                    <p class="card-text display-4"><?= $count ?></p>
                    <a href="orders.php" class="text-white">View Orders</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
                    $count = $stmt->fetchColumn();
                    ?>
                    <p class="card-text display-4"><?= $count ?></p>
                    <a href="manage_users.php" class="text-white">Manage Users</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header">
            <h4>Recent Orders</h4>
        </div>
        <div class="card-body">
            <?php
            $stmt = $pdo->query("
                SELECT o.*, u.username 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC 
                LIMIT 5
            ");
            $orders = $stmt->fetchAll();
            ?>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['username']) ?></td>
                            <td>$<?= number_format($order['total_amount'], 2) ?></td>
                            <td><?= ucfirst($order['status']) ?></td>
                            <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <a href="orders.php" class="btn btn-primary">View All Orders</a>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php';?>