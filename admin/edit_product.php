<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../index.php');
}

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch product data
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    $_SESSION['error'] = "Product not found";
    redirect('products.php');
}

// Fetch categories for dropdown
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $stock_quantity = intval($_POST['stock_quantity']);
    
    // Validate inputs
    $errors = [];
    if (empty($name)) $errors[] = "Product name is required";
    if ($price <= 0) $errors[] = "Price must be greater than 0";
    if ($stock_quantity < 0) $errors[] = "Stock quantity cannot be negative";
    
    // Handle file upload
    $image = $product['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $upload_dir = '../uploads/products/';
            $image = 'product_' . $product_id . '_' . time() . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            
            // Delete old image if it's not the default placeholder
            if ($product['image'] && $product['image'] !== 'placeholder.jpg') {
                @unlink($upload_dir . $product['image']);
            }
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image)) {
                $errors[] = "Failed to upload image";
            }
        } else {
            $errors[] = "Only JPG, PNG, and GIF images are allowed";
        }
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("
            UPDATE products 
            SET name = ?, description = ?, price = ?, category_id = ?, stock_quantity = ?, image = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $description, $price, $category_id, $stock_quantity, $image, $product_id]);
        
        $_SESSION['success'] = "Product updated successfully!";
        redirect('products.php');
    }
}

require_once 'header.php';
?>

<div class="container mt-4">
    <h2>Edit Product</h2>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p class="mb-0"><?= $error ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="name" class="form-label">Product Name</label>
            <input type="text" class="form-control" id="name" name="name" 
                   value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" 
                      rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="price" class="form-label">Price</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" 
                           value="<?= htmlspecialchars($product['price']) ?>" required>
                </div>
            </div>
            
            <div class="col-md-4">
                <label for="stock_quantity" class="form-label">Stock Quantity</label>
                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" 
                       value="<?= htmlspecialchars($product['stock_quantity']) ?>" required>
            </div>
            
            <div class="col-md-4">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" 
                            <?= $category['id'] == $product['category_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="current_image" class="form-label">Current Image</label>
            <div>
                <img src="../uploads/products/<?= htmlspecialchars($product['image'] ?? 'placeholder.jpg') ?>" 
                     alt="Current Product Image" class="img-thumbnail" style="max-height: 200px;">
            </div>
        </div>
        
        <div class="mb-3">
            <label for="image" class="form-label">Change Image</label>
            <input type="file" class="form-control" id="image" name="image">
            <div class="form-text">Leave blank to keep current image</div>
        </div>
        
        <button type="submit" class="btn btn-primary">Update Product</button>
        <a href="products.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>