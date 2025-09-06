<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isset($_GET['id'])) {
    redirect('index.php');
}

$product_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    redirect('index.php');
}

require_once 'includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - E-Commerce Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <img src="uploads/<?= htmlspecialchars($product['image'] ?? 'placeholder.jpg') ?>" class="img-fluid" alt="<?= htmlspecialchars($product['name']) ?>">
            </div>
            <div class="col-md-6">
                <h1><?= htmlspecialchars($product['name']) ?></h1>
                <p class="text-muted">Product ID: <?= $product['id'] ?></p>
                <h3 class="text-primary">$<?= number_format($product['price'], 2) ?></h3>
                <p><?= htmlspecialchars($product['description']) ?></p>
                <p>Stock: <?= $product['stock_quantity'] > 0 ? 'In Stock' : 'Out of Stock' ?></p>
                
                <form action="cart.php" method="post" class="mt-4">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity:</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="<?= $product['stock_quantity'] ?>" style="width: 80px;">
                    </div>
                    <button type="submit" class="btn btn-primary" name="add_to_cart">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>

    <?php require_once 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>