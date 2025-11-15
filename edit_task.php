<?php
require_once 'db.php';
session_start();
if(!isset($_SESSION['user'])) { header('Location: index.php'); exit; }

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) die('CSRF error');
    $id = (int)$_POST['id'];
    $status = $_POST['status'] ?? 'pending';

    // nếu form submit chỉnh sửa chi tiết sẽ có title...
    if(isset($_POST['title'])) {
        $title = trim($_POST['title']);
        $desc  = trim($_POST['description'] ?? '');
        $due   = $_POST['due_date'] ?? null;
        $stmt = $pdo->prepare("UPDATE tasks SET title=:title, description=:desc, due_date=:due, status=:status WHERE id=:id AND user_id=:uid");
        $stmt->execute([':title'=>$title, ':desc'=>$desc, ':due'=>$due?:null, ':status'=>$status, ':id'=>$id, ':uid'=>$_SESSION['user']['id']]);
    } else {
        // chỉ đổi status
        $stmt = $pdo->prepare("UPDATE tasks SET status=:status WHERE id=:id AND user_id=:uid");
        $stmt->execute([':status'=>$status, ':id'=>$id, ':uid'=>$_SESSION['user']['id']]);
    }
    header('Location: index.php'); exit;
}

// Nếu GET: hiển thị form sửa
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id=:id AND user_id=:uid");
$stmt->execute([':id'=>$id, ':uid'=>$_SESSION['user']['id']]);
$task = $stmt->fetch();
if(!$task) {
    $_SESSION['flash'] = 'Không tìm thấy công việc';
    header('Location: index.php'); exit;
}
include 'header.php';
?>

<div class="card p-3">
  <h5>Sửa công việc</h5>
  <form method="post">
    <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>">
    <input type="hidden" name="id" value="<?=$task['id']?>">
    <div class="mb-2">
      <input name="title" class="form-control" value="<?=htmlspecialchars($task['title'])?>" required>
    </div>
    <div class="mb-2">
      <textarea name="description" class="form-control"><?=htmlspecialchars($task['description'])?></textarea>
    </div>
    <div class="mb-2">
      <input name="due_date" type="date" class="form-control" value="<?=$task['due_date']?>">
    </div>
    <div class="mb-2">
      <select name="status" class="form-select">
        <option value="pending" <?=$task['status']==='pending'?'selected':''?>>Pending</option>
        <option value="in_progress" <?=$task['status']==='in_progress'?'selected':''?>>In progress</option>
        <option value="completed" <?=$task['status']==='completed'?'selected':''?>>Completed</option>
      </select>
    </div>
    <button class="btn btn-warning">Lưu thay đổi</button>
    <a class="btn btn-secondary" href="index.php">Hủy</a>
  </form>
</div>

<?php include 'footer.php'; ?>
