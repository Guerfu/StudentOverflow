<?php
// modules/create.php (controller) - Create a new module
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../lib/view.php';
require_once __DIR__ . '/../lib/media.php';

auth_require_admin();

$errors = [];
$code = $name = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $code = trim($_POST['code'] ?? '');
  $name = trim($_POST['name'] ?? '');

  if ($code === '') $errors[] = 'Module code is required.';
  if ($name === '') $errors[] = 'Module name is required.';

  if (!$errors) {
    $stmt = $pdo->prepare("INSERT INTO modules (code, name) VALUES (:c, :n)");
    try {
      $stmt->execute([':c'=>$code, ':n'=>$name]);
      header('Location: ' . base_url() . '/modules/index.php');
      exit;
    } catch (PDOException $e) {
      $msg = $e->getCode() === '23000' ? 'Module code already exists.' : 'DB error.';
      $errors[] = $msg;
    }
  }
}

render('modules/create.html.php', [
  'errors' => $errors,
  'code'   => $code,
  'name'   => $name,
]);
