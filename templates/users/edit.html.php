<?php
// templates/users/edit.html.php (template)
// expects: $id, $errors, $username, $email, $role
?>

<h1 class="mb-3">Edit User</h1>

<?php if ($errors): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $er): ?>
        <li><?= htmlspecialchars($er) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" class="border p-3 rounded" novalidate>
  <div class="mb-2">
    <label class="form-label">Username</label>
    <input class="form-control" name="username" value="<?= htmlspecialchars($username) ?>" required maxlength="50">
  </div>

  <div class="mb-2">
    <label class="form-label">Email</label>
    <input class="form-control" type="email" name="email" value="<?= htmlspecialchars($email) ?>" required maxlength="120">
  </div>

  <div class="mb-2">
    <label class="form-label">Role</label>
    <select class="form-select" name="role" required>
      <option value="student" <?= $role==='student'?'selected':'' ?>>student</option>
      <option value="admin"   <?= $role==='admin'  ?'selected':'' ?>>admin</option>
    </select>
    <div class="form-text">Admins can manage users/modules; students cannot.</div>
  </div>

  <hr>

  <div class="mb-2">
    <label class="form-label">New Password (optional)</label>
    <input class="form-control" type="password" name="password" autocomplete="new-password" placeholder="Leave blank to keep current">
  </div>
  <div class="mb-3">
    <label class="form-label">Confirm New Password</label>
    <input class="form-control" type="password" name="password2" autocomplete="new-password">
  </div>

  <button class="btn btn-primary">Save</button>
  <a href="<?= base_url(); ?>/users/index.php" class="btn btn-link">Cancel</a>
</form>
