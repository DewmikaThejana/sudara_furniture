<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'register') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid method']);
        exit;
    }

    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Username or email already exists']);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");

    if ($stmt->execute([$username, $email, $hashedPassword])) {
        // Auto-login after registration
        $userId = $pdo->lastInsertId();
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        echo json_encode(['success' => true, 'message' => 'Registration successful']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration failed']);
    }
} elseif ($action === 'login') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid method']);
        exit;
    }

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        echo json_encode(['success' => true, 'message' => 'Login successful', 'username' => $user['username']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    }
} elseif ($action === 'logout') {
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
} elseif ($action === 'status') {
    if (isset($_SESSION['user_id'])) {
        echo json_encode(['logged_in' => true, 'username' => $_SESSION['username']]);
    } else {
        echo json_encode(['logged_in' => false]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>