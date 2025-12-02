<?php
// modules/edit.php (controller) - Edit an existing module
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../lib/view.php';
require_once __DIR__ . '/../lib/media.php';

auth_require_admin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: ' . base_url() . '/modules/index.php'); exit; }

// Fetch module
$stmt = $pdo->prepare("SELECT * FROM modules WHERE id = :id");
$stmt->execute([':id' => $id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) { header('Location: ' . base_url() . '/modules/index.php'); exit; }

$errors = [];
$code = $row['code'];
$name = $row['name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $code = trim($_POST['code'] ?? '');
  $name = trim($_POST['name'] ?? '');

  if ($code === '') $errors[] = 'Module code is required.';
  if ($name === '') $errors[] = 'Module name is required.';

  if (!$errors) {
    $upd = $pdo->prepare("UPDATE modules SET code = :c, name = :n WHERE id = :id");
    try {
      $upd->execute([':c' => $code, ':n' => $name, ':id' => $id]);
      header('Location: ' . base_url() . '/modules/index.php');
      exit;
    } catch (PDOException $e) {
      $msg = $e->getCode() === '23000' ? 'Module code already exists.' : 'DB error.';
      $errors[] = $msg;
    }
  }
}

render('modules/edit.html.php', [
  'errors' => $errors,
  'code'   => $code,
  'name'   => $name,
]);
