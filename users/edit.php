<?php
// users/edit.php (controller) — Admin edits a user (including role)
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../lib/view.php';
require_once __DIR__ . '/../lib/media.php';

auth_require_admin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: ' . base_url() . '/users/index.php'); exit; }

// Fetch user
$stmt = $pdo->prepare("SELECT id, username, email, role FROM users WHERE id = :id");
$stmt->execute([':id' => $id]);
$user = $stmt->fetch();
if (!$user) { header('Location: ' . base_url() . '/users/index.php'); exit; }

$errors = [];
$ok = false;

// Form model
$username = $user['username'];
$email    = $user['email'];
$role     = $user['role']; // 'admin' | 'student'
$pass1 = $pass2 = '';

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $email    = trim($_POST['email'] ?? '');
  $role     = ($_POST['role'] ?? 'student') === 'admin' ? 'admin' : 'student';
  $pass1    = $_POST['password']  ?? '';
  $pass2    = $_POST['password2'] ?? '';

  // Basic validation
  if ($username === '') $errors[] = 'Username is required.';
  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
  if ($pass1 !== '' && $pass1 !== $pass2) $errors[] = 'Passwords do not match.';
  if (!in_array($role, ['admin','student'], true)) $errors[] = 'Invalid role value.';

  // Uniqueness (exclude current user)
  if (!$errors) {
    $uq = $pdo->prepare("SELECT 1 FROM users WHERE (username = :u OR email = :e) AND id <> :id LIMIT 1");
    $uq->execute([':u' => $username, ':e' => $email, ':id' => $id]);
    if ($uq->fetchColumn()) $errors[] = 'Username or email already exists.';
  }

  // Prevent removing the final admin
  if (!$errors && $user['role'] === 'admin' && $role === 'student') {
    $countAdmins = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
    if ($countAdmins <= 1) {
      $errors[] = 'Cannot demote the last remaining admin.';
    }
  }

  if (!$errors) {
    try {
      if ($pass1 !== '') {
        $hash = password_hash($pass1, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET username = :u, email = :e, role = :r, password_hash = :h WHERE id = :id";
        $params = [':u'=>$username, ':e'=>$email, ':r'=>$role, ':h'=>$hash, ':id'=>$id];
      } else {
        $sql = "UPDATE users SET username = :u, email = :e, role = :r WHERE id = :id";
        $params = [':u'=>$username, ':e'=>$email, ':r'=>$role, ':id'=>$id];
      }
      $upd = $pdo->prepare($sql);
      $upd->execute($params);
      $ok = true;

      header('Location: ' . base_url() . '/users/index.php');
      exit;
    } catch (Throwable $e) {
      $errors[] = 'Database error updating user.';
    }
  }
}

render('users/edit.html.php', [
  'id'       => $id,
  'errors'   => $errors,
  'username' => $username,
  'email'    => $email,
  'role'     => $role,
]);
