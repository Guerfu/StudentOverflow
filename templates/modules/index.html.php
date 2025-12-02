<?php
// templates/modules/index.html.php (template)
// expects: $rows
?>

<h1 class="mb-3">Modules</h1>
<p><a href="<?= base_url(); ?>/modules/create.php" class="btn btn-sm btn-primary">+ Create Module</a></p>

<?php if (!$rows): ?>
  <div class="alert alert-info">No modules yet.</div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table table-sm align-middle">
      <thead>
        <tr>
          <th>ID</th><th>Code</th><th>Name</th><th>Posts</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $m): ?>
          <tr>
            <td><?= (int)$m['id'] ?></td>
            <td><?= htmlspecialchars($m['code']) ?></td>
            <td><?= htmlspecialchars($m['name']) ?></td>
            <td><?= (int)$m['post_count'] ?></td>
            <td>
              <a href="<?= base_url(); ?>/modules/edit.php?id=<?= (int)$m['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
              <a href="<?= base_url(); ?>/modules/delete.php?id=<?= (int)$m['id'] ?>" class="btn btn-sm btn-outline-danger"
                 onclick="return confirm('Delete this module?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>
