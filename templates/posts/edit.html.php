<?php
// templates/posts/edit.html.php (template)
// expects: $postId, $errors, $title, $content, $module_id, $mods, $thumb
?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <h1 class="h4 mb-0">Edit Question</h1>
  <a href="<?= base_url(); ?>/posts/show.php?id=<?= (int)$postId ?>" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<?php if ($errors): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="card">
  <div class="card-body">
    <div class="mb-3">
      <label class="form-label">Title</label>
      <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($title) ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Module</label>
      <select name="module_id" class="form-select" required>
        <?php foreach ($mods as $m): ?>
          <option value="<?= (int)$m['id'] ?>" <?= $module_id===(int)$m['id']?'selected':'' ?>>
            <?= htmlspecialchars($m['code']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Content</label>
      <textarea name="content" rows="6" class="form-control" required><?= htmlspecialchars($content) ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Screenshot</label>
      <?php if ($thumb): ?>
        <div class="mb-2">
          <img src="<?= htmlspecialchars($thumb) ?>" alt="current image" class="img-fluid rounded"
               style="max-height:160px;object-fit:cover;">
        </div>
      <?php else: ?>
        <div class="form-text">No image attached.</div>
      <?php endif; ?>
      <input type="file" name="image" class="form-control" accept="image/*">
      <div class="form-text">Upload a new image to replace the current one (optional).</div>
      <?php if ($thumb): ?>
        <div class="form-check mt-2">
          <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image">
          <label class="form-check-label" for="remove_image">Remove current image</label>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="card-footer d-flex justify-content-end gap-2">
    <a href="<?= base_url(); ?>/posts/show.php?id=<?= (int)$postId ?>" class="btn btn-outline-secondary">Cancel</a>
    <button class="btn btn-primary">Save Changes</button>
  </div>
</form>
