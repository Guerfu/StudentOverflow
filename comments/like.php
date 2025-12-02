<?php
// comments/like.php (controller only)
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../lib/media.php'; // for base_url()
auth_require_login();

$comment_id = (int)($_POST['comment_id'] ?? 0);
$post_id    = (int)($_POST['post_id'] ?? 0);

if ($comment_id <= 0 || $post_id <= 0) {
  header('Location: ' . base_url() . '/public/index.php');
  exit;
}

try {
  $ins = $pdo->prepare(
    "INSERT INTO comment_likes (comment_id, user_id)
     VALUES (:c, :u)"
  );
  $ins->execute([
    ':c' => $comment_id,
    ':u' => auth_user_id(),
  ]);
} catch (Throwable $e) {
  // already liked -> ignore (or implement unlike in the future)
}

header('Location: ' . base_url() . '/posts/show.php?id=' . $post_id);
exit;
