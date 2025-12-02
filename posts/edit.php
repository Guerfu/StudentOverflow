<?php
// posts/edit.php (controller)
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../lib/view.php';
require_once __DIR__ . '/../lib/media.php';

$postId = (int)($_GET['id'] ?? 0);
if ($postId <= 0) { http_response_code(404); exit('Post not found'); }

/* ---------- helpers (logic-only; keep behavior identical) ---------- */
function normalize_filename(string $name): string {
  $base = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($name));
  return time() . '_' . $base;
}
function is_image_upload_ok(array $f): array {
  if (!isset($f['error']) || $f['error'] === UPLOAD_ERR_NO_FILE) return [true, null]; // optional
  if ($f['error'] !== UPLOAD_ERR_OK) return [false, 'Upload failed (code '.$f['error'].').'];
  if ($f['size'] > 5 * 1024 * 1024) return [false, 'Image is larger than 5MB.'];
  $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
  $finfo = finfo_open(FILEINFO_MIME_TYPE);
  $mime = finfo_file($finfo, $f['tmp_name']); finfo_close($finfo);
  if (!in_array($mime, $allowed, true)) return [false, 'Unsupported image type.'];
  return [true, null];
}
/* ------------------------------------------------------------------ */

// fetch post + first image
$sql = "
  SELECT p.*, u.username,
    (SELECT file_name FROM post_images WHERE post_id = p.id ORDER BY id ASC LIMIT 1) AS image_file
  FROM posts p
  JOIN users u ON u.id = p.user_id
  WHERE p.id = :id
  LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $postId]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$post) { http_response_code(404); exit('Post not found'); }

// authorization
if (!(auth_is_admin() || (int)$post['user_id'] === (int)auth_user_id())) {
  http_response_code(403);
  exit('Forbidden');
}

// modules for dropdown
$mods = $pdo->query("SELECT id, code FROM modules ORDER BY code")->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
$title = $post['title'];
$content = $post['content'];
$module_id = (int)$post['module_id'];
$currentImage = $post['image_file']; // may be null

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title'] ?? '');
  $content = trim($_POST['content'] ?? '');
  $module_id = (int)($_POST['module_id'] ?? 0);
  $remove_image = isset($_POST['remove_image']);

  if ($title === '')   $errors[] = 'Title is required.';
  if ($content === '') $errors[] = 'Content is required.';
  if ($module_id <= 0) $errors[] = 'Please choose a module.';

  [$ok, $msg] = is_image_upload_ok($_FILES['image'] ?? []);
  if (!$ok) $errors[] = $msg;

  if (!$errors) {
    // update post
    $pdo->prepare("UPDATE posts SET title=:t, content=:c, module_id=:m WHERE id=:id")
        ->execute([':t'=>$title, ':c'=>$content, ':m'=>$module_id, ':id'=>$postId]);

    // handle image removal
    if ($remove_image && $currentImage) {
      $pdo->prepare("DELETE FROM post_images WHERE post_id = :p")
          ->execute([':p' => $postId]);

      $fs = uploads_dir_fs() . '/' . preg_replace('~.*[\\\\/]~', '', $currentImage);
      if (is_file($fs)) @unlink($fs);
      $currentImage = null;
    }

    // handle replacement upload
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
      $uploadsDir = uploads_dir_fs();
      if (!is_dir($uploadsDir)) { @mkdir($uploadsDir, 0777, true); }
      $fileName = normalize_filename($_FILES['image']['name']);
      $dest = $uploadsDir . '/' . $fileName;

      if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
        // remove existing DB rows (we keep only one "first" image for now)
        $pdo->prepare("DELETE FROM post_images WHERE post_id = :p")->execute([':p' => $postId]);
        $pdo->prepare("INSERT INTO post_images (post_id, file_name) VALUES (:p,:f)")
            ->execute([':p'=>$postId, ':f'=>$fileName]);
        $currentImage = $fileName;
      }
    }

    header('Location: ' . base_url() . '/posts/show.php?id=' . $postId);
    exit;
  }
}

$thumb = resolve_image_url($currentImage);

// Render (layout wraps header/footer)
render('posts/edit.html.php', [
  'postId'     => $postId,
  'errors'     => $errors,
  'title'      => $title,
  'content'    => $content,
  'module_id'  => $module_id,
  'mods'       => $mods,
  'thumb'      => $thumb,
]);
