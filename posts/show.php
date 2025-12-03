<?php
// posts/show.php (controller)
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../lib/view.php';
require_once __DIR__ . '/../lib/media.php';

$postId = (int)($_GET['id'] ?? 0);
if ($postId <= 0) { http_response_code(404); exit('Post not found'); }

$uid = auth_user_id();

// Fetch post 
$sql = "
  SELECT
    p.id, p.title, p.content, p.created_at, p.user_id, p.module_id,
    u.username,
    m.code AS module_code,
    (SELECT COUNT(*) FROM post_upvotes pu WHERE pu.post_id = p.id) AS pu_cnt,
    (
      SELECT pi.file_name FROM post_images pi
      WHERE pi.post_id = p.id ORDER BY pi.id ASC LIMIT 1
    ) AS image_path,
    (CASE WHEN up.post_id IS NULL THEN 0 ELSE 1 END) AS already_upvoted
  FROM posts p
  JOIN users u   ON u.id = p.user_id
  JOIN modules m ON m.id = p.module_id
  LEFT JOIN (SELECT post_id FROM post_upvotes WHERE user_id = :uid) up ON up.post_id = p.id
  WHERE p.id = :pid
  LIMIT 1
";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':pid', $postId, PDO::PARAM_INT);
$stmt->bindValue(':uid', (int)$uid, PDO::PARAM_INT);
$stmt->execute();
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$post) { http_response_code(404); exit('Post not found'); }

$isOwner = ($uid && (int)$post['user_id'] === (int)$uid) || auth_is_admin();

// Fetch comments
$csql = "
  SELECT
    c.id, c.post_id, c.user_id, c.content, c.created_at,
    u.username,
    (SELECT COUNT(*) FROM comment_likes cl WHERE cl.comment_id = c.id) AS like_count,
    (SELECT COUNT(*) FROM comment_kudos ck WHERE ck.comment_id = c.id) AS kudos_count,
    EXISTS(SELECT 1 FROM comment_likes cl2 WHERE cl2.comment_id = c.id AND cl2.user_id = :viewer1) AS i_liked,
    EXISTS(SELECT 1 FROM comment_kudos ck2 WHERE ck2.comment_id = c.id AND ck2.giver_user_id = :viewer2) AS i_kudosed
  FROM comments c
  JOIN users u ON u.id = c.user_id
  WHERE c.post_id = :pid
  ORDER BY c.created_at ASC
";
$cstmt = $pdo->prepare($csql);
$cstmt->execute([
  ':pid'     => $postId,
  ':viewer1' => (int)$uid,
  ':viewer2' => (int)$uid,
]);
$comments = $cstmt->fetchAll(PDO::FETCH_ASSOC);

// Resolve first image URL now 
$imgUrl = resolve_image_url($post['image_path'] ?? null);

// Render 
render('posts/show.html.php', [
  'post'     => $post,
  'uid'      => $uid,
  'isOwner'  => $isOwner,
  'comments' => $comments,
  'imgUrl'   => $imgUrl,
]);

