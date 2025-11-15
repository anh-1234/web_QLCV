<?php
require_once 'db.php';
session_start();
if(!isset($_SESSION['user'])) { header('Location: index.php'); exit; }

$id = (int)($_GET['id'] ?? 0);
$csrf = $_GET['csrf_token'] ?? '';
if(!hash_equals($_SESSION['csrf_token'] ?? '', $csrf)) die('CSRF error');

$stmt = $pdo->prepare("DELETE FROM tasks WHERE id=:id AND user_id=:uid");
$stmt->execute([':id'=>$id, ':uid'=>$_SESSION['user']['id']]);
header('Location: index.php');
exit;
?>