<?php
// modules/index.php (controller)
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../lib/view.php';
require_once __DIR__ . '/../lib/media.php';

auth_require_admin(); // only admins

$rows = $pdo->query("
  SELECT m.id, m.code, m.name,
         (SELECT COUNT(*) FROM posts p WHERE p.module_id = m.id) AS post_count
  FROM modules m
  ORDER BY m.code
")->fetchAll(PDO::FETCH_ASSOC);

render('modules/index.html.php', [
  'rows' => $rows,
]);
