<?php
// comments/create.php (controller only)
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../lib/media.php'; // for base_url()
auth_require_login();

$post_id   = (int)($_POST['post_id'] ?? 0);
$content   = trim($_POST['content'] ?? '');
$parent_id = (int)($_POST['parent_id'] ?? 0) ?: null;

if ($post_id <= 0 || $content === '') {
  header('Location: ' . base_url() . '/public/index.php');
  exit;
}

$stmt = $pdo->prepare(
  "INSERT INTO comments (post_id, user_id, content, parent_id)
   VALUES (:p, :u, :c, :pid)"
);
$stmt->execute([
  ':p'   => $post_id,
  ':u'   => auth_user_id(),
  ':c'   => $content,
  ':pid' => $parent_id,
]);

header('Location: ' . base_url() . '/posts/show.php?id=' . $post_id);
exit;
