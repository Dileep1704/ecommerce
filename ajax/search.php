<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$searchTerm = trim($_POST['search'] ?? '');

if ($searchTerm !== '') {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ?");
    $stmt->execute(["%$searchTerm%", "%$searchTerm%"]);
    $results = $stmt->fetchAll();

    if ($results) {
        foreach ($results as $product): ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <img src="../uploads/<?= htmlspecialchars($product['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                        <p class="card-text">$<?= number_format($product['price'], 2) ?></p>
                        <a href="../product_detail.php?id=<?= $product['id'] ?>" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach;
    } else {
        echo '<div class="col-12"><div class="alert alert-warning">No products found.</div></div>';
    }
}
?>
