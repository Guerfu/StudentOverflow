<?php
// templates/users/create.html.php
// expects: $errors, $username, $email
?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <h1 class="h4 mb-0">Create User</h1>
  <a href="<?= base_url(); ?>/users/index.php" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $er): ?>
        <li><?= htmlspecialchars($er) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" class="card">
  <div class="card-body">
    <div class="mb-3">
      <label class="form-label">Username</label>
      <input name="username" class="form-control" value="<?= htmlspecialchars($username) ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Email</label>
      <input name="email" type="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
    </div>

    <div class="mb-1">
      <label class="form-label">Password</label>
      <input name="password" type="password" class="form-control" required>
    </div>
  </div>

  <div class="card-footer d-flex justify-content-end gap-2">
    <a class="btn btn-outline-secondary" href="<?= base_url(); ?>/users/index.php">Cancel</a>
    <button class="btn btn-primary">Save</button>
  </div>
</form>
