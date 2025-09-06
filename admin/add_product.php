<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../index.php');
}

$errors = [];
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $category_id = $_POST['category_id'];
    $stock_quantity = $_POST['stock_quantity'];
    
    // Validate inputs
    if (empty($name)) $errors[] = 'Product name is required';
    if (empty($price) || !is_numeric($price) || $price <= 0) $errors[] = 'Valid price is required';
    if (empty($stock_quantity) || !is_numeric($stock_quantity) || $stock_quantity < 0) $errors[] = 'Valid stock quantity is required';
    
    // Handle file upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $upload_dir = '../uploads/';
            $image = uniqid() . '_' . basename($_FILES['image']['name']);
            $target_path = $upload_dir . $image;
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $errors[] = 'Failed to upload image';
            }
        } else {
            $errors[] = 'Only JPG, PNG, and GIF images are allowed';
        }
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO products (name, description, price, image, category_id, stock_quantity)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$name, $description, $price, $image, $category_id, $stock_quantity]);
        
        $_SESSION['success'] = 'Product added successfully!';
        redirect('index.php');
    }
}

require_once 'header.php';
?>

<div class="container mt-4">
    <h1>Add New Product</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <div><?= $error ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="name" class="form-label">Product Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
        </div>
        <div class="mb-3">
            <label for="category_id" class="form-label">Category</label>
            <select class="form-select" id="category_id" name="category_id" required>
                <option value="">Select a category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="stock_quantity" class="form-label">Stock Quantity</label>
            <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" required>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Product Image</label>
            <input type="file" class="form-control" id="image" name="image">
        </div>
        <button type="submit" class="btn btn-primary">Add Product</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>