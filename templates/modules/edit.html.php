<?php
// templates/modules/edit.html.php
// expects: $errors, $code, $name
?>

<h1 class="mb-3">Edit Module</h1>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $er): ?>
        <li><?= htmlspecialchars($er) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" class="card" novalidate>
  <div class="card-body">
    <div class="mb-3">
      <label class="form-label">Code</label>
      <input class="form-control" name="code" value="<?= htmlspecialchars($code) ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Name</label>
      <input class="form-control" name="name" value="<?= htmlspecialchars($name) ?>" required>
    </div>
  </div>

  <div class="card-footer d-flex justify-content-end gap-2">
    <a class="btn btn-outline-secondary" href="<?= base_url(); ?>/modules/index.php">Cancel</a>
    <button class="btn btn-primary">Save</button>
  </div>
</form>
