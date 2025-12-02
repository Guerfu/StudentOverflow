<?php
// templates/posts/create.html.php (template)
// expects: $errors, $title, $content, $module_id, $mods
?>

<h1 class="mb-3">New Question</h1>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="border p-3 rounded" novalidate>
  <div class="mb-3">
    <label class="form-label">Title</label>
    <input
      type="text"
      name="title"
      class="form-control"
      value="<?= htmlspecialchars($title) ?>"
      required
    >
  </div>

  <div class="mb-3">
    <label class="form-label">Module</label>
    <select name="module_id" class="form-select" required>
      <option value="">Select a module…</option>
      <?php foreach ($mods as $m): ?>
        <option value="<?= (int)$m['id'] ?>" <?= $module_id===(int)$m['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($m['code']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">Content</label>
    <textarea
      name="content"
      rows="6"
      class="form-control"
      required
    ><?= htmlspecialchars($content) ?></textarea>
  </div>

  <div class="mb-3">
    <label class="form-label">Screenshot (optional)</label>
    <input type="file" name="image" class="form-control" accept="image/*">
    <div class="form-text">JPEG/PNG/GIF/WEBP, up to 5MB.</div>
  </div>

  <div class="d-flex justify-content-end gap-2">
    <a href="<?= base_url(); ?>/" class="btn btn-outline-secondary">Back</a>
    <button class="btn btn-primary">Create</button>
  </div>
</form>
