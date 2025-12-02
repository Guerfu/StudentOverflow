<?php
// templates/public/contact.html.php (template)
// expects: $okDb, $okMail, $errors, $name, $senderEmail, $subject, $message
?>

<h1 class="mb-3">Contact Admin</h1>

<?php if ($okDb): ?>
  <div class="alert alert-success">
    Thanks! Your message was saved<?php if ($okMail): ?> and emailed<?php else: ?> (email send unavailable)<?php endif; ?>.
  </div>
<?php endif; ?>

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
    <label class="form-label">Your Name</label>
    <input class="form-control" name="name" value="<?= htmlspecialchars($name) ?>" required>
  </div>

  <div class="mb-2">
    <label class="form-label">Your Account Email</label>
    <input class="form-control" value="<?= htmlspecialchars($senderEmail ?? '') ?>" readonly>
    <div class="form-text">Email is taken from your logged-in account.</div>
  </div>

  <div class="mb-2">
    <label class="form-label">Subject</label>
    <input class="form-control" name="subject" value="<?= htmlspecialchars($subject) ?>" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Message</label>
    <textarea class="form-control" name="message" rows="6" required><?= htmlspecialchars($message) ?></textarea>
  </div>

  <div class="d-flex justify-content-end gap-2">
  <a href="/studentoverflow/public/index.php" class="btn btn-outline-secondary">Back</a>
  <button class="btn btn-primary">Send</button>
  </div>
</form>
