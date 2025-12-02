<?php
// users/create.php (controller)
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../lib/view.php';
require_once __DIR__ . '/../lib/media.php';

auth_require_admin();

$errors = [];
$username = $email = '';

// NOTE: This project expects a `password` column (VARCHAR) on `users` for authentication.
// Run: ALTER TABLE users ADD COLUMN password VARCHAR(255) NULL; then set a password for existing users.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $email    = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($username === '') $errors[] = 'Username is required.';
  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';
  if ($password === '') $errors[] = 'Password is required.';

  if (!$errors) {
    $pwdHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (:u, :e, :p)");
    try {
      $stmt->execute([':u' => $username, ':e' => $email, ':p' => $pwdHash]);
      header('Location: ' . base_url() . '/users/index.php'); 
      exit;
    } catch (PDOException $e) {
      $msg = $e->getCode() === '23000' ? 'Username or email already exists.' : 'DB error.';
      $errors[] = $msg;
    }
  }
}

render('users/create.html.php', [
  'errors'  => $errors,
  'username'=> $username,
  'email'   => $email,
]);
