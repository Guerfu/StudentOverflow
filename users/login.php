<?php
// users/login.php (controller)
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../lib/view.php';
require_once __DIR__ . '/../lib/media.php';

// If already logged in, go home
if (auth_user_id()) {
  header('Location: ' . base_url() . '/public/index.php');
  exit;
}

// --- CSRF token bootstrap (one token for both forms) ---
if (empty($_SESSION['csrf_login'])) {
  $_SESSION['csrf_login'] = bin2hex(random_bytes(16));
}

$loginErr = '';
$regErrs = [];
$login_identifier = '';
$reg_username = $reg_email = '';

// Handle POST for login OR register (action hidden field)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $sentToken = $_POST['csrf'] ?? '';
  if (!hash_equals($_SESSION['csrf_login'], $sentToken)) {
    http_response_code(400);
    // Generic error to avoid leaking details
    $loginErr = 'Invalid form token. Please refresh and try again.';
    $regErrs[] = 'Invalid form token. Please refresh and try again.';
  } else {
    $action = $_POST['action'] ?? 'login';

    if ($action === 'login') {
      $login_identifier = trim($_POST['identifier'] ?? ''); // username or email
      $password = $_POST['password'] ?? '';

      if ($login_identifier === '' || $password === '') {
        $loginErr = 'Please enter both username/email and password.';
      } else {
        // Use two placeholders (PDO named reuse can error on native prepares)
        $stmt = $pdo->prepare("
          SELECT id, username, email, password_hash, role
          FROM users
          WHERE username = :id1 OR email = :id2
          LIMIT 1
        ");
        $stmt->execute([':id1' => $login_identifier, ':id2' => $login_identifier]);
        $u = $stmt->fetch();

        if ($u && !empty($u['password_hash']) && password_verify($password, $u['password_hash'])) {
          session_regenerate_id(true);
          $_SESSION['user_id']  = (int)$u['id'];
          $_SESSION['username'] = $u['username'];
          $_SESSION['role']     = $u['role'];
          // rotate token
          $_SESSION['csrf_login'] = bin2hex(random_bytes(16));
          header('Location: ' . base_url() . '/public/index.php');
          exit;
        } else {
          $loginErr = 'Invalid username/email or password.';
        }
      }

    } elseif ($action === 'register') {
      // Gather + validate registration input
      $reg_username = trim($_POST['reg_username'] ?? '');
      $reg_email    = trim($_POST['reg_email'] ?? '');
      $reg_pass     = $_POST['reg_password'] ?? '';
      $reg_pass2    = $_POST['reg_password2'] ?? '';

      if ($reg_username === '') $regErrs[] = 'Username is required.';
      if ($reg_email === '' || !filter_var($reg_email, FILTER_VALIDATE_EMAIL)) $regErrs[] = 'Valid email is required.';
      if ($reg_pass === '') $regErrs[] = 'Password is required.';
      if ($reg_pass !== $reg_pass2) $regErrs[] = 'Passwords do not match.';

      if ($reg_username !== '' && mb_strlen($reg_username) > 50) $regErrs[] = 'Username must be 50 characters or fewer.';
      if ($reg_email !== '' && mb_strlen($reg_email) > 120) $regErrs[] = 'Email must be 120 characters or fewer.';

      if (!$regErrs) {
        try {
          // Check uniqueness
          $chk = $pdo->prepare("SELECT 1 FROM users WHERE username = :u OR email = :e LIMIT 1");
          $chk->execute([':u' => $reg_username, ':e' => $reg_email]);
          if ($chk->fetchColumn()) {
            $regErrs[] = 'Username or email already exists.';
          } else {
            // Create account as student
            $hash = password_hash($reg_pass, PASSWORD_DEFAULT);
            $ins = $pdo->prepare("
              INSERT INTO users (username, email, password_hash, role)
              VALUES (:u, :e, :h, 'student')
            ");
            $ins->execute([':u' => $reg_username, ':e' => $reg_email, ':h' => $hash]);

            // Auto-login
            $uid = (int)$pdo->lastInsertId();
            session_regenerate_id(true);
            $_SESSION['user_id']  = $uid;
            $_SESSION['username'] = $reg_username;
            $_SESSION['role']     = 'student';
            $_SESSION['csrf_login'] = bin2hex(random_bytes(16));

            header('Location: ' . base_url() . '/public/index.php');
            exit;
          }
        } catch (Throwable $e) {
          // If unique keys exist on username/email, a duplicate insert will throw here
          $regErrs[] = 'Unable to create account (possibly duplicate username/email).';
        }
      }
    }
  }
}

// Render
render('users/login.html.php', [
  'csrf'             => $_SESSION['csrf_login'],
  'loginErr'         => $loginErr,
  'regErrs'          => $regErrs,
  'login_identifier' => $login_identifier,
  'reg_username'     => $reg_username,
  'reg_email'        => $reg_email,
]);

