<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? 'list';

if ($action === 'list') {
    $category_id = isset($_GET['category_id']) ? (int) $$_GET['category_id'] : null;
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 50;

    $query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id";
    $params = [];

    if ($category_id) {
        $query .= " WHERE p.category_id = ?";
        $params[] = $category_id;
    }

    $query .= " ORDER BY p.id DESC LIMIT $limit";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    echo json_encode($products);

} elseif ($action === 'single') {
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    if ($id <= 0) {
        echo json_encode(['error' => 'Invalid product ID']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product) {
        echo json_encode($product);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
    }

} elseif ($action === 'categories') {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
    $categories = $stmt->fetchAll();
    echo json_encode($categories);
} else {
    echo json_encode(['error' => 'Invalid action']);
}
?>