<?php
// posts/upvote.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth.php';
auth_require_login();

$post_id = (int)($_POST['post_id'] ?? 0);
if ($post_id <= 0) { header('Location: /studentoverflow/public/index.php'); exit; }

$uid = auth_user_id();
try {
  // insert if not exists
  $ins = $pdo->prepare("INSERT INTO post_upvotes (post_id,user_id) VALUES (:p,:u)");
  $ins->execute([':p'=>$post_id, ':u'=>$uid]);
} catch (Throwable $e) {
  if (isset($_POST['toggle']) && $_POST['toggle'] === '1') {
    $del = $pdo->prepare("DELETE FROM post_upvotes WHERE post_id=:p AND user_id=:u");
    $del->execute([':p'=>$post_id, ':u'=>$uid]);
  }
}
// Redirect back
$back = $_POST['back'] ?? "/studentoverflow/posts/show.php?id={$post_id}";
header("Location: {$back}");
exit;

