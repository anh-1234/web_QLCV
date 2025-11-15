<?php
require_once 'db.php';
session_start();
if(!isset($_SESSION['user'])) { header('Location: index.php'); exit; }
if($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
if(!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) die('CSRF error');

$title = trim($_POST['title'] ?? '');
$desc = trim($_POST['description'] ?? null);
$due = $_POST['due_date'] ?? null;

if($title === '') {
    $_SESSION['flash'] = 'Tiêu đề không được để trống.';
    header('Location: index.php'); exit;
}

$stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description, due_date) VALUES (:uid, :title, :desc, :due)");
$stmt->execute([
    ':uid' => $_SESSION['user']['id'],
    ':title' => $title,
    ':desc' => $desc,
    ':due' => $due ?: null
]);
header('Location: index.php');
exit;
?>
