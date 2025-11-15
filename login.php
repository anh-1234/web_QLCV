<?php
require_once 'db.php';
session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        die('CSRF token mismatch');
    }
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->execute([':username'=>$username]);
    $user = $stmt->fetch();
    if($user && password_verify($password, $user['password'])) {
        // Đăng nhập thành công
        unset($user['password']);
        $_SESSION['user'] = $user;
        header('Location: index.php'); exit;
    } else {
        $_SESSION['flash'] = 'Đăng nhập thất bại: username hoặc mật khẩu không đúng.';
        header('Location: index.php#login'); exit;
    }
} else {
    header('Location: index.php'); exit;
}
?>