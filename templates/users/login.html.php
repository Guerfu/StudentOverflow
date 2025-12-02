<?php
// templates/users/login.html.php (template)
// expects: $csrf, $loginErr, $regErrs, $login_identifier, $reg_username, $reg_email
?>

<h1 class="mb-3">Welcome</h1>

<div class="row">
  <div class="col-md-6">
    <h2 class="h5">Log in</h2>

    <?php if ($loginErr): ?>
      <div class="alert alert-danger py-2"><?= htmlspecialchars($loginErr) ?></div>
    <?php endif; ?>

    <form method="post" novalidate class="border p-3 rounded">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
      <input type="hidden" name="action" value="login">

      <div class="mb-2">
        <label class="form-label">Username or Email</label>
        <input class="form-control" name="identifier" value="<?= htmlspecialchars($login_identifier) ?>" required>
      </div>

      <div class="mb-2">
        <label class="form-label">Password</label>
        <input class="form-control" type="password" name="password" required>
      </div>

      <button class="btn btn-primary">Log in</button>
      <a href="<?= base_url(); ?>/public/index.php" class="btn btn-link">Cancel</a>
    </form>
  </div>

  <div class="col-md-6">
    <h2 class="h5">Create a new student account</h2>

    <?php if ($regErrs): ?>
      <div class="alert alert-danger py-2">
        <ul class="mb-0">
          <?php foreach ($regErrs as $er): ?>
            <li><?= htmlspecialchars($er) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" novalidate class="border p-3 rounded">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
      <input type="hidden" name="action" value="register">

      <div class="mb-2">
        <label class="form-label">Username</label>
        <input class="form-control" name="reg_username" value="<?= htmlspecialchars($reg_username) ?>" maxlength="50" required>
      </div>

      <div class="mb-2">
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="reg_email" value="<?= htmlspecialchars($reg_email) ?>" maxlength="120" required>
      </div>

      <div class="mb-2">
        <label class="form-label">Password</label>
        <input class="form-control" type="password" name="reg_password" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input class="form-control" type="password" name="reg_password2" required>
      </div>

      <button class="btn btn-success">Create account</button>
    </form>
  </div>
</div>
