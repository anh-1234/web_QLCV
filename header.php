<?php
if(session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';

if(empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];
$logged_in = isset($_SESSION['user']);
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Simple To-Do App</title>
  <!-- Bootstrap CSS (tải offline hoặc dùng CDN) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/custom.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">MyToDo</a>
    <div>
      <?php if($logged_in): ?>
        <span class="me-3">Xin chào, <?=htmlspecialchars($_SESSION['user']['username'])?></span>
        <a class="btn btn-outline-secondary btn-sm" href="logout.php">Đăng xuất</a>
      <?php else: ?>
        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#authModal">Đăng nhập / Đăng ký</button>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- Modal: Đăng nhập / Đăng ký (tabs) -->
<div class="modal fade" id="authModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <ul class="nav nav-tabs card-header-tabs" id="authTab" role="tablist">
          <li class="nav-item">
            <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button">Đăng nhập</button>
          </li>
          <li class="nav-item">
            <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button">Đăng ký</button>
          </li>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="tab-content">
          <!-- LOGIN -->
          <div class="tab-pane fade show active" id="login">
            <form method="post" action="login.php" class="p-2">
              <input type="hidden" name="csrf_token" value="<?=$csrf?>">
              <div class="mb-3">
                <input name="username" class="form-control" placeholder="Nhập tên đăng nhập" required>
              </div>
              <div class="mb-3">
                <input name="password" type="password" class="form-control" placeholder="Mật khẩu" required>
              </div>
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div><input type="checkbox" id="remember"> <label for="remember">Nhớ mật khẩu</label></div>
                <a href="#" class="small">Quên mật khẩu</a>
              </div>
              <button class="btn btn-warning w-100">ĐĂNG NHẬP</button>
            </form>
          </div>
          <!-- REGISTER -->
          <div class="tab-pane fade" id="register">
            <form method="post" action="register.php" class="p-2">
              <input type="hidden" name="csrf_token" value="<?=$csrf?>">
              <div class="mb-3"><input name="username" class="form-control" placeholder="Nhập họ và tên (username)" required></div>
              <div class="mb-3"><input name="phone" class="form-control" placeholder="Nhập số điện thoại (tùy chọn)"></div>
              <div class="mb-3"><input name="email" type="email" class="form-control" placeholder="Nhập địa chỉ email"></div>
              <div class="mb-3"><input name="password" type="password" class="form-control" placeholder="Nhập mật khẩu" required></div>
              <div class="mb-3"><input name="confirm_password" type="password" class="form-control" placeholder="Nhập lại mật khẩu" required></div>
              <button class="btn btn-warning w-100">ĐĂNG KÝ</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<main class="container my-4">
