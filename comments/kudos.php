<?php
// comments/kudos.php (controller only) — only the post owner or admin can give kudos
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

// Verify current user is owner of the post (or admin)
$q = $pdo->prepare("SELECT user_id FROM posts WHERE id = :p");
$q->execute([':p' => $post_id]);
$postOwner = (int)($q->fetchColumn() ?: 0);

if ($postOwner !== (int)auth_user_id() && !auth_is_admin()) {
  http_response_code(403);
  exit('Forbidden');
}

try {
  $ins = $pdo->prepare(
    "INSERT INTO comment_kudos (comment_id, giver_user_id)
     VALUES (:c, :u)"
  );
  $ins->execute([
    ':c' => $comment_id,
    ':u' => auth_user_id(),
  ]);
} catch (Throwable $e) {
  // already gave kudos to this comment → ignore
}

header('Location: ' . base_url() . '/posts/show.php?id=' . $post_id);
exit;
