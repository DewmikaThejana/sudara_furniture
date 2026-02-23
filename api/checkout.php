<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_items = $_SESSION['cart'];
$total_amount = 0;

try {
    $pdo->beginTransaction();

    // Calculate total amount and verify products
    foreach ($cart_items as $product_id => $quantity) {
        $stmt = $pdo->prepare("SELECT price, stock FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if (!$product) {
            throw new Exception("Product ID $product_id not found.");
        }

        if ($product['stock'] < $quantity) {
            throw new Exception("Insufficient stock for product ID $product_id.");
        }

        $total_amount += ($product['price'] * $quantity);
    }

    // Insert order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
    $stmt->execute([$user_id, $total_amount]);
    $order_id = $pdo->lastInsertId();

    // Insert order items and update stock
    foreach ($cart_items as $product_id => $quantity) {
        // Get price again
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $price = $stmt->fetchColumn();

        // Insert into order_items
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $product_id, $quantity, $price]);

        // Update product stock
        $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt->execute([$quantity, $product_id]);
    }

    $pdo->commit();
    $_SESSION['cart'] = []; // Clear cart after successful checkout

    echo json_encode(['success' => true, 'message' => 'Order placed successfully', 'order_id' => $order_id]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Checkout failed: ' . $e->getMessage()]);
}
?>