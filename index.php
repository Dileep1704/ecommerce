<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/navbar.php';

// Get featured products initially
$products = get_products($pdo, 4); // You can change the number as needed
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopEase - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Welcome to Our Store</h1>

        <!-- Featured Products -->
        <h2 class="mb-3">Featured Products</h2>
        <div class="row" id="product-list">
            <?php foreach ($products as $product): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <img src="uploads/<?= htmlspecialchars($product['image'] ?? 'placeholder.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="card-text">$<?= number_format($product['price'], 2) ?></p>
                            <a href="product_detail.php?id=<?= $product['id'] ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php require_once 'includes/footer.php'; ?>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AJAX Search Handler -->
    <script>
        $('#navbarSearchForm').on('submit', function(e) {
            e.preventDefault();
            const query = $('#navbarSearchInput').val().trim();
            if (query.length === 0) return;

            $.ajax({
                url: 'ajax/search.php',
                type: 'POST',
                data: { search: query },
                success: function(response) {
                    $('#product-list').html(response);
                },
                error: function() {
                    $('#product-list').html('<div class="alert alert-danger">Search failed. Please try again.</div>');
                }
            });
        });
    </script>
</body>
</html>
