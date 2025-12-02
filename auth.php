<?php
// Simple auth helpers used across the app
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

function auth_user_id(): ?int { return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null; }
function auth_username(): ?string { return $_SESSION['username'] ?? null; }
function auth_role(): ?string { return $_SESSION['role'] ?? null; }  // expects 'admin' or 'student'
function auth_is_admin(): bool { return auth_role() === 'admin'; }

function auth_require_login(): void {
  if (!auth_user_id()) { header('Location: /studentoverflow/users/login.php'); exit; }
}
function auth_require_admin(): void {
  if (!auth_is_admin()) { http_response_code(403); exit('Forbidden: admin only'); }
}

function current_user($pdo)
{
    if (empty($_SESSION['user_id'])) return null;
    try {
        $stmt = $pdo->prepare('SELECT id, username, email FROM users WHERE id = :id');
        $stmt->execute([':id' => $_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (Exception $e) {
        return null;
    }
}

function require_login()
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['user_id'])) {
        header('Location: /studentoverflow/users/login.php');
        exit;
    }
}

function is_post_owner($pdo, $post_id, $user_id)
{
    $stmt = $pdo->prepare('SELECT user_id FROM posts WHERE id = :id');
    $stmt->execute([':id' => $post_id]);
    $r = $stmt->fetch();
    return $r && ((int)$r['user_id'] === (int)$user_id);
}
