<?php
session_start();
header('Content-Type: application/json');

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_GET['action'] ?? 'view';

if ($action === 'view') {
    echo json_encode($_SESSION['cart']);
} elseif ($action === 'add') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['error' => 'Invalid method']);
        exit;
    }

    $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

    if ($product_id <= 0 || $quantity <= 0) {
        echo json_encode(['error' => 'Invalid product or quantity']);
        exit;
    }

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }

    echo json_encode(['success' => true, 'message' => 'Product added to cart', 'cart' => $_SESSION['cart']]);

} elseif ($action === 'update') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['error' => 'Invalid method']);
        exit;
    }

    $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 0;

    if ($product_id <= 0 || $quantity < 0) {
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }

    if ($quantity === 0) {
        unset($_SESSION['cart'][$product_id]);
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }

    echo json_encode(['success' => true, 'message' => 'Cart updated', 'cart' => $_SESSION['cart']]);
} elseif ($action === 'remove') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['error' => 'Invalid method']);
        exit;
    }

    $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;

    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }

    echo json_encode(['success' => true, 'message' => 'Product removed from cart', 'cart' => $_SESSION['cart']]);
} elseif ($action === 'clear') {
    $_SESSION['cart'] = [];
    echo json_encode(['success' => true, 'message' => 'Cart cleared', 'cart' => $_SESSION['cart']]);
} else {
    echo json_encode(['error' => 'Invalid action']);
}
?>