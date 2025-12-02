<?php
// templates/modules/create.html.php
// expects: $errors, $code, $name
?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <h1 class="h4 mb-0">Create Module</h1>
  <a href="<?= base_url(); ?>/modules/index.php" class="btn btn-outline-secondary btn-sm">Back</a>
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
      <label class="form-label">Code</label>
      <input name="code" class="form-control" value="<?= htmlspecialchars($code) ?>" required>
    </div>

    <div class="mb-1">
      <label class="form-label">Name</label>
      <input name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
    </div>
  </div>

  <div class="card-footer d-flex justify-content-end gap-2">
    <a class="btn btn-outline-secondary" href="<?= base_url(); ?>/modules/index.php">Cancel</a>
    <button class="btn btn-primary">Save</button>
  </div>
</form>
