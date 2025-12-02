<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../lib/view.php';
require_once __DIR__ . '/../lib/media.php';

$uid      = auth_user_id();
$isAdmin  = auth_is_admin();
$username = $_SESSION['username'] ?? null;

// Quick stats (optional, lightweight)
$counts = [
  'posts'   => (int)$pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn(),
  'users'   => (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
  'modules' => (int)$pdo->query("SELECT COUNT(*) FROM modules")->fetchColumn(),
];

// Fetch latest 5 posts for teaser
$recent = $pdo->query("
  SELECT p.id, p.title, p.created_at,
         u.username,
         m.code AS module_code
  FROM posts p
  JOIN users u   ON u.id = p.user_id
  JOIN modules m ON m.id = p.module_id
  ORDER BY p.created_at DESC
  LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

render('public/index.html.php', [
  'uid'      => $uid,
  'isAdmin'  => $isAdmin,
  'username' => $username,
  'counts'   => $counts,
  'recent'   => $recent,
]);
