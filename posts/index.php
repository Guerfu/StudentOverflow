<?php
// posts/index.php (controller)
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../lib/view.php';
require_once __DIR__ . '/../lib/media.php';

// ----- filters -----
$order         = $_GET['order']  ?? 'urgent_desc'; // urgent_desc | urgent_asc | newest | oldest
$moduleFilter  = (int)($_GET['module'] ?? 0);
$q             = trim((string)($_GET['q'] ?? ''));

// modules for dropdown
$allModules = $pdo->query("SELECT id, code FROM modules ORDER BY code")->fetchAll(PDO::FETCH_ASSOC);

// ordering SQL
switch ($order) {
  case 'urgent_asc':  $orderSql = "pu_cnt ASC, p.created_at DESC";  break;
  case 'newest':      $orderSql = "p.created_at DESC";              break;
  case 'oldest':      $orderSql = "p.created_at ASC";               break;
  default:            $orderSql = "pu_cnt DESC, p.created_at DESC"; break;
}

$uid = auth_user_id();
$isAdmin = auth_is_admin();

// WHERE fragments + params
$whereSql = "WHERE 1=1";
$params   = [':uid' => (int)$uid];

if ($moduleFilter > 0) {
  $whereSql .= " AND p.module_id = :mid";
  $params[':mid'] = $moduleFilter;
}

if ($q !== '') {
  // Use DISTINCT placeholders to avoid HY093 with native prepares
  $whereSql .= " AND (p.title LIKE :q1 OR p.content LIKE :q2 OR u.username LIKE :q3 OR m.code LIKE :q4)";
  $like = '%' . $q . '%';
  $params[':q1'] = $like;
  $params[':q2'] = $like;
  $params[':q3'] = $like;
  $params[':q4'] = $like;
}

// ----- query posts (includes first image filename + upvote flags) -----
$sql = "
  SELECT
    p.id, p.title, p.content, p.created_at, p.user_id, p.module_id,
    u.username,
    m.code AS module_code,
    (SELECT COUNT(*) FROM post_upvotes pu WHERE pu.post_id = p.id) AS pu_cnt,
    (
      SELECT pi.file_name
      FROM post_images pi
      WHERE pi.post_id = p.id
      ORDER BY pi.id ASC
      LIMIT 1
    ) AS image_path,
    (CASE WHEN up.post_id IS NULL THEN 0 ELSE 1 END) AS already_upvoted
  FROM posts p
  JOIN users u   ON u.id = p.user_id
  JOIN modules m ON m.id = p.module_id
  LEFT JOIN (SELECT post_id FROM post_upvotes WHERE user_id = :uid) up ON up.post_id = p.id
  $whereSql
  ORDER BY {$orderSql}
  LIMIT 100
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Build suggestions (latest 20 titles). Keep light to avoid huge headers.
$suggStmt = $pdo->query("
  SELECT title
  FROM posts
  ORDER BY created_at DESC
  LIMIT 20
");
$searchOptions = array_column($suggStmt->fetchAll(PDO::FETCH_ASSOC), 'title');

// ... then pass it to the view:
render('posts/index.html.php', [
  'posts'        => $posts,
  'allModules'   => $allModules,
  'order'        => $order,
  'moduleFilter' => $moduleFilter,
  'q'            => $q ?? '',
  'uid'          => $uid,
  'isAdmin'      => $isAdmin,
  'searchOptions'=> $searchOptions,   // <-- important
]);
