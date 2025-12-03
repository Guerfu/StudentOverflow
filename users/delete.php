<?php
// users/delete.php 
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../lib/media.php'; // base_url()
require_once __DIR__ . '/../lib/view.php';  // redirect_to()
auth_require_admin();

$targetId = (int)($_GET['id'] ?? 0);
if ($targetId <= 0) {
    redirect_to(base_url() . '/users/index.php');
}

// Fetch target user
$uStmt = $pdo->prepare("SELECT id, username, email, role FROM users WHERE id = :id");
$uStmt->execute([':id' => $targetId]);
$user = $uStmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    redirect_to(base_url() . '/users/index.php');
}

// Prevent deleting the special "deleted_account" user (by username)
if (strcasecmp($user['username'] ?? '', 'deleted_account') === 0) {
    redirect_to(base_url() . '/users/index.php');
}

// Prevent deleting the last remaining admin
if (($user['role'] ?? '') === 'admin') {
    $adminCount = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
    if ($adminCount <= 1) {
        // Can't delete: would leave zero admins
        redirect_to(base_url() . '/users/index.php');
    }
}

$pdo->beginTransaction();

try {
    // Ensure there is a "deleted_account" user (no special columns required)
    $find = $pdo->prepare("SELECT id FROM users WHERE LOWER(username) = 'deleted_account' LIMIT 1");
    $find->execute();
    $deletedId = $find->fetchColumn();

    if (!$deletedId) {
        $name   = 'deleted_account';
        $email  = 'deleted@local.invalid'; // guaranteed non-deliverable
        $hash   = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);

        $ins = $pdo->prepare("
            INSERT INTO users (username, email, password_hash, role)
            VALUES (:u, :e, :h, 'admin')
        ");
        $ins->execute([':u' => $name, ':e' => $email, ':h' => $hash]);
        $deletedId = (int)$pdo->lastInsertId();
    } else {
        $deletedId = (int)$deletedId;
    }

    // Safety: don't allow deleting the "deleted_account" itself via id
    if ($deletedId === $targetId) {
        $pdo->rollBack();
        redirect_to(base_url() . '/users/index.php');
    }

    // Comment out any line that doesn't match your schema.
    $updates = [
        // authored entities
        "UPDATE posts            SET user_id       = :to WHERE user_id       = :uid",
        "UPDATE comments         SET user_id       = :to WHERE user_id       = :uid",
        // reactions / relations
        "UPDATE post_upvotes     SET user_id       = :to WHERE user_id       = :uid",
        "UPDATE comment_likes    SET user_id       = :to WHERE user_id       = :uid",
        "UPDATE comment_kudos    SET giver_user_id = :to WHERE giver_user_id = :uid",
        // e.g. "UPDATE contact_messages SET email = CONCAT('deleted+', :uid, '@local.invalid') WHERE email = :oldEmail"
    ];

    foreach ($updates as $sql) {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':to' => $deletedId, ':uid' => $targetId]);
    }

    // 3) Now safely delete the user
    $del = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $del->execute([':id' => $targetId]);

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
}

redirect_to(base_url() . '/users/index.php');
