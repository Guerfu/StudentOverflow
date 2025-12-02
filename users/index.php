<?php
// users/index.php (controller) — List users + their posts in collapsible rows
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../lib/view.php';
require_once __DIR__ . '/../lib/media.php';

auth_require_admin(); // only admins

// Get users (same as before)
$users = $pdo->query("
  SELECT u.id, u.username, u.email, u.role,
         (SELECT COUNT(*) FROM posts p WHERE p.user_id = u.id) AS post_count
  FROM users u
  ORDER BY u.username
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch posts for these users in one query and group by user_id
$postsByUser = [];
if ($users) {
  $ids = array_map(fn($u) => (int)$u['id'], $users);
  $in  = implode(',', array_fill(0, count($ids), '?'));

  $stmt = $pdo->prepare("
    SELECT p.id, p.user_id, p.title, p.created_at, p.updated_at
    FROM posts p
    WHERE p.user_id IN ($in)
    ORDER BY p.created_at DESC, p.id DESC
  ");
  $stmt->execute($ids);
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $uid = (int)$row['user_id'];
    if (!isset($postsByUser[$uid])) $postsByUser[$uid] = [];
    $postsByUser[$uid][] = $row;
  }
}

render('users/index.html.php', [
  'users'       => $users,
  'postsByUser' => $postsByUser,
]);
