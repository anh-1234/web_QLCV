<?php
require_once 'db.php';
session_start();
include 'header.php';

if(!isset($_SESSION['user'])) {
    // nếu chưa login, show trang chủ nhẹ với modal
    if(!empty($_SESSION['flash'])) {
        echo '<div class="alert alert-danger">'.htmlspecialchars($_SESSION['flash']).'</div>';
        unset($_SESSION['flash']);
    }
    if(!empty($_SESSION['flash_success'])) {
        echo '<div class="alert alert-success">'.htmlspecialchars($_SESSION['flash_success']).'</div>';
        unset($_SESSION['flash_success']);
    }
    echo '<div class="text-center py-5">
            <h3>Ứng dụng To-Do đơn giản</h3>
            <p>Vui lòng đăng nhập hoặc đăng ký để quản lý công việc của bạn.</p>
          </div>';
    include 'footer.php';
    exit;
}

// Đã login: lấy các tham số lọc/sắp xếp
$userId = $_SESSION['user']['id'];
$statusFilter = $_GET['status'] ?? '';
$sort = ($_GET['sort'] ?? 'due_date_asc');

$where = " WHERE user_id = :user_id ";
$params = [':user_id' => $userId];

if(in_array($statusFilter, ['pending','in_progress','completed'])) {
    $where .= " AND status = :status ";
    $params[':status'] = $statusFilter;
}

$order = " ORDER BY due_date ASC, created_at DESC ";
if($sort === 'due_date_desc') $order = " ORDER BY due_date DESC, created_at DESC ";
if($sort === 'created_desc') $order = " ORDER BY created_at DESC ";
if($sort === 'created_asc') $order = " ORDER BY created_at ASC ";

$stmt = $pdo->prepare("SELECT * FROM tasks $where $order");
$stmt->execute($params);
$tasks = $stmt->fetchAll();
?>

<div class="row">
  <div class="col-md-4">
    <div class="card p-3 task-card">
      <h5>Thêm công việc mới</h5>
      <form action="create_task.php" method="post">
        <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>">
        <div class="mb-2">
          <input name="title" class="form-control" placeholder="Tiêu đề" required>
        </div>
        <div class="mb-2">
          <textarea name="description" class="form-control" placeholder="Mô tả (tùy chọn)"></textarea>
        </div>
        <div class="mb-2">
          <input name="due_date" type="date" class="form-control">
        </div>
        <button class="btn btn-warning w-100">Thêm công việc</button>
      </form>
    </div>

    <div class="card mt-3 p-3">
      <h6>Bộ lọc</h6>
      <form method="get">
        <select name="status" class="form-select mb-2">
          <option value="">Tất cả trạng thái</option>
          <option value="pending" <?= $statusFilter==='pending' ? 'selected':'' ?>>Pending</option>
          <option value="in_progress" <?= $statusFilter==='in_progress' ? 'selected':'' ?>>In progress</option>
          <option value="completed" <?= $statusFilter==='completed' ? 'selected':'' ?>>Completed</option>
        </select>
        <select name="sort" class="form-select mb-2">
          <option value="due_date_asc" <?= $sort==='due_date_asc'?'selected':'' ?>>Hạn chót ↑</option>
          <option value="due_date_desc" <?= $sort==='due_date_desc'?'selected':'' ?>>Hạn chót ↓</option>
          <option value="created_desc" <?= $sort==='created_desc'?'selected':'' ?>>Mới nhất</option>
          <option value="created_asc" <?= $sort==='created_asc'?'selected':'' ?>>Cũ nhất</option>
        </select>
        <button class="btn btn-outline-primary w-100">Áp dụng</button>
      </form>
    </div>
  </div>

  <div class="col-md-8">
    <h5>Công việc của bạn (<?=count($tasks)?>)</h5>
    <?php foreach($tasks as $t): ?>
      <div class="card mb-2 p-3 task-card">
        <div class="d-flex justify-content-between">
          <div>
            <h6 class="<?= $t['status']==='completed' ? 'text-decoration-line-through':'' ?>"><?=htmlspecialchars($t['title'])?></h6>
            <small class="text-muted"><?=htmlspecialchars($t['description'])?></small><br>
            <small>Hạn: <?= $t['due_date'] ? htmlspecialchars($t['due_date']) : 'Không' ?></small>
          </div>
          <div class="text-end">
            <span class="badge status-badge <?=htmlspecialchars($t['status'])?>"><?=htmlspecialchars($t['status'])?></span>
            <div class="mt-2">
              <a href="edit_task.php?id=<?=$t['id']?>" class="btn btn-sm btn-outline-secondary">Sửa</a>
              <a href="delete_task.php?id=<?=$t['id']?>&csrf_token=<?=$_SESSION['csrf_token']?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa công việc này?')">Xóa</a>
            </div>
            <form action="edit_task.php" method="post" class="mt-2">
              <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>">
              <input type="hidden" name="id" value="<?=$t['id']?>">
              <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="pending" <?=$t['status']==='pending'?'selected':''?>>Pending</option>
                <option value="in_progress" <?=$t['status']==='in_progress'?'selected':''?>>In progress</option>
                <option value="completed" <?=$t['status']==='completed'?'selected':''?>>Completed</option>
              </select>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php include 'footer.php'; ?>
