<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth.php';
auth_require_admin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: index.php'); exit; }

// block delete if posts exist
$cnt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE module_id = :id");
$cnt->execute([':id' => $id]);
if ((int)$cnt->fetchColumn() > 0) {
  header('Location: /studentoverflow/modules/index.php?err=has_posts'); exit;
}

$stmt = $pdo->prepare("DELETE FROM modules WHERE id = :id");
$stmt->execute([':id' => $id]);
header('Location: /studentoverflow/modules/index.php'); exit;
