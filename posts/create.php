<?php
// posts/create.php (controller)
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../lib/view.php';
require_once __DIR__ . '/../lib/media.php';

if (!auth_user_id()) {
  header('Location: ' . base_url() . '/users/login.php');
  exit;
}

/* ---------- helpers ---------- */
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

$errors = [];
$title = '';
$content = '';
$module_id = 0;

// load modules for dropdown
$mods = $pdo->query("SELECT id, code FROM modules ORDER BY code")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title'] ?? '');
  $content = trim($_POST['content'] ?? '');
  $module_id = (int)($_POST['module_id'] ?? 0);

  if ($title === '')   $errors[] = 'Title is required.';
  if ($content === '') $errors[] = 'Content is required.';
  if ($module_id <= 0) $errors[] = 'Please choose a module.';

  [$ok, $msg] = is_image_upload_ok($_FILES['image'] ?? []);
  if (!$ok) $errors[] = $msg;

  if (!$errors) {
    // insert post
    $stmt = $pdo->prepare("INSERT INTO posts (title, content, user_id, module_id) VALUES (:t,:c,:u,:m)");
    $stmt->execute([
      ':t' => $title,
      ':c' => $content,
      ':u' => (int)auth_user_id(),
      ':m' => $module_id
    ]);
    $postId = (int)$pdo->lastInsertId();

    // optional image
    if (!empty($_FILES['image']['name']) && ($_FILES['image']['error'] === UPLOAD_ERR_OK)) {
      $uploadsDir = uploads_dir_fs(); // from lib/media.php
      if (!is_dir($uploadsDir)) { @mkdir($uploadsDir, 0777, true); }
      $fileName = normalize_filename($_FILES['image']['name']);
      $dest = $uploadsDir . '/' . $fileName;
      if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
        $pdo->prepare("INSERT INTO post_images (post_id, file_name) VALUES (:p,:f)")
            ->execute([':p'=>$postId, ':f'=>$fileName]); // store filename only
      }
    }

    header('Location: ' . base_url() . '/posts/show.php?id=' . $postId);
    exit;
  }
}

// Render (layout wraps header/footer)
render('posts/create.html.php', [
  'errors'    => $errors,
  'title'     => $title,
  'content'   => $content,
  'module_id' => $module_id,
  'mods'      => $mods,
]);

