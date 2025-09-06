<?php
function display_error($error) {
    return '<div class="alert alert-danger">'.$error.'</div>';
}

function display_success($message) {
    return '<div class="alert alert-success">'.$message.'</div>';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

function get_products($pdo, $limit = null, $search = '')
{
    if (!empty($search)) {
        $sql = "SELECT * FROM products WHERE name LIKE :search OR description LIKE :search ORDER BY created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['search' => '%' . $search . '%']);
    } else {
        $sql = "SELECT * FROM products ORDER BY created_at DESC";
        if ($limit) {
            $sql .= " LIMIT ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$limit]);
        } else {
            $stmt = $pdo->query($sql);
        }
    }

    return $stmt->fetchAll();
}

?>