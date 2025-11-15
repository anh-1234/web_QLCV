<?php
require_once 'db.php';
session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        die('CSRF token mismatch');
    }
    $username = trim($_POST['username'] ?? '');
    $email = filter_var($_POST['email'] ?? null, FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if($password !== $confirm) {
        $_SESSION['flash'] = 'Mật khẩu không khớp.';
        header('Location: index.php#register'); exit;
    }
    if(strlen($password) < 6) {
        $_SESSION['flash'] = 'Mật khẩu quá ngắn (>=6).';
        header('Location: index.php#register'); exit;
    }
    // Kiểm tra trùng username/email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username=:username OR email=:email LIMIT 1");
    $stmt->execute([':username'=>$username, ':email'=>$email]);
    if($stmt->fetch()) {
        $_SESSION['flash'] = 'Username hoặc email đã tồn tại.';
        header('Location: index.php#register'); exit;
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $ins = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (:username,:password,:email)");
    $ins->execute([':username'=>$username, ':password'=>$hash, ':email'=>$email]);
    $_SESSION['flash_success'] = 'Đăng ký thành công. Bạn có thể đăng nhập.';
    header('Location: index.php'); exit;
} else {
    header('Location: index.php'); exit;
}
?>