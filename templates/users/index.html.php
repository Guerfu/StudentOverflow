<?php
// expects: $users (array of rows: id, username, email, role, post_count)

// Filter out the "system" account (no schema changes required)
$visibleUsers = array_values(array_filter($users ?? [], function ($u) {
    return isset($u['username']) && strcasecmp($u['username'], 'deleted_account') !== 0;
}));
?>

<h1 class="mb-3 page-title">Users</h1>
<p><a href="create.php" class="btn btn-sm btn-primary">+ Create User</a></p>

<?php if (!$visibleUsers): ?>
  <div class="alert alert-info">No users yet.</div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table table-sm align-middle">
      <thead>
        <tr>
          <th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Posts</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($visibleUsers as $u): ?>
          <tr>
            <td><?= (int)$u['id'] ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td>
              <span class="badge bg-<?= ($u['role'] === 'admin') ? 'danger' : 'secondary' ?>">
                <?= htmlspecialchars($u['role']) ?>
              </span>
            </td>
            <td><?= (int)$u['post_count'] ?></td>
            <td>
              <a href="edit.php?id=<?= (int)$u['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
              <a href="delete.php?id=<?= (int)$u['id'] ?>" class="btn btn-sm btn-outline-danger"
                 onclick="return confirm('Delete this user?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>
