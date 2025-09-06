<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../index.php');
}

// Handle category actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new category
    if (isset($_POST['add_category'])) {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        
        if (!empty($name)) {
            $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            $stmt->execute([$name, $description]);
            $_SESSION['success'] = "Category added successfully!";
        } else {
            $_SESSION['error'] = "Category name cannot be empty!";
        }
    }
    // Update category
    elseif (isset($_POST['update_category'])) {
        $id = intval($_POST['id']);
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        
        if (!empty($name)) {
            $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
            $stmt->execute([$name, $description, $id]);
            $_SESSION['success'] = "Category updated successfully!";
        } else {
            $_SESSION['error'] = "Category name cannot be empty!";
        }
    }
    
    redirect('categories.php');
}

// Delete category
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Check if category is empty (no products)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $stmt->execute([$id]);
    $product_count = $stmt->fetchColumn();
    
    if ($product_count == 0) {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "Category deleted successfully!";
    } else {
        $_SESSION['error'] = "Cannot delete category with products! Move or delete products first.";
    }
    
    redirect('categories.php');
}

// Get all categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

require_once 'header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Categories</h2>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="row">
        <!-- Add/Edit Category Form -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><?= isset($_GET['edit']) ? 'Edit' : 'Add' ?> Category</h5>
                </div>
                <div class="card-body">
                    <?php
                    $editing_category = null;
                    if (isset($_GET['edit'])) {
                        $edit_id = intval($_GET['edit']);
                        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
                        $stmt->execute([$edit_id]);
                        $editing_category = $stmt->fetch();
                    }
                    ?>
                    <form method="post">
                        <?php if ($editing_category): ?>
                            <input type="hidden" name="id" value="<?= $editing_category['id'] ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= $editing_category ? htmlspecialchars($editing_category['name']) : '' ?>" 
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="3"><?= $editing_category ? htmlspecialchars($editing_category['description']) : '' ?></textarea>
                        </div>
                        
                        <button type="submit" name="<?= $editing_category ? 'update_category' : 'add_category' ?>" 
                                class="btn btn-primary w-100">
                            <?= $editing_category ? 'Update' : 'Add' ?> Category
                        </button>
                        
                        <?php if ($editing_category): ?>
                            <a href="categories.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Categories List -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Existing Categories</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($categories)): ?>
                        <div class="alert alert-info">No categories found.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td><?= $category['id'] ?></td>
                                            <td><?= htmlspecialchars($category['name']) ?></td>
                                            <td><?= htmlspecialchars($category['description']) ?></td>
                                            <td>
                                                <a href="categories.php?edit=<?= $category['id'] ?>" 
                                                   class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="categories.php?delete=<?= $category['id'] ?>" 
                                                   class="btn btn-sm btn-danger" title="Delete"
                                                   onclick="return confirm('Are you sure? This action cannot be undone.')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>