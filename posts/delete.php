<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth.php';
auth_require_login(); // must be logged in
// no header/footer for a quick redirect-only action

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: /studentoverflow/public/index.php'); exit; }

// Load post for ownership check
$stmt = $pdo->prepare("SELECT id, user_id FROM posts WHERE id = :id");
$stmt->execute([':id'=>$id]);
$post = $stmt->fetch();
if (!$post) { header('Location: /studentoverflow/public/index.php'); exit; }

// owner-or-admin guard
$isOwner = (int)$post['user_id'] === (int)auth_user_id();
if (!$isOwner && !auth_is_admin()) { http_response_code(403); exit('Forbidden: not your post'); }

// Unlink image files first (DB will cascade metadata)
$imgs = $pdo->prepare("SELECT file_name FROM post_images WHERE post_id = :pid");
$imgs->execute([':pid'=>$id]);
foreach ($imgs->fetchAll() as $row) {
  $abs = dirname(__DIR__) . '/' . ltrim($row['file_name'], '/');
  if (is_file($abs)) @unlink($abs);
}

// Delete post
$pdo->prepare("DELETE FROM posts WHERE id = :id")->execute([':id'=>$id]);

header('Location: /studentoverflow/public/index.php'); exit;
