<!-- templates/public/index.html.php -->
<?php
// expects: $uid, $isAdmin, $username, $counts (posts, users, modules), $recent (list)
$base = base_url();
?>

<h1 class="page-title">Welcome</h1>

<div class="row g-3 mb-3">
  <div class="col-md-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title mb-1">Questions</h5>
        <div class="text-muted small mb-2"><?= (int)($counts['posts'] ?? 0) ?> total</div>
        <a class="btn btn-info btn-sm" href="<?= $base ?>/posts/index.php">View all questions</a>
        <?php if ($uid): ?>
          <a class="btn btn-outline-secondary btn-sm" href="<?= $base ?>/posts/create.php">Ask a question</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title mb-1">Contact</h5>
        <div class="text-muted small mb-2">Need help from admin?</div>
        <a class="btn btn-outline-primary btn-sm" href="<?= $base ?>/public/contact.php">Contact admin</a>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title mb-1">Directory</h5>
        <div class="text-muted small mb-2">
          Users: <?= (int)($counts['users'] ?? 0) ?> · Modules: <?= (int)($counts['modules'] ?? 0) ?>
        </div>
        <?php if ($isAdmin): ?>
          <a class="btn btn-secondary btn-sm" href="<?= $base ?>/users/index.php">Manage Users</a>
          <a class="btn btn-secondary btn-sm" href="<?= $base ?>/modules/index.php">Manage Modules</a>
        <?php else: ?>
          <a class="btn btn-outline-secondary btn-sm" href="<?= $base ?>/posts/index.php">Browse by module</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<h2 class="h5 mb-2">Latest questions</h2>
<?php if (!$recent): ?>
  <div class="alert alert-secondary">No recent questions yet. Be the first to <a href="<?= $base ?>/posts/create.php">ask one</a>!</div>
<?php else: ?>
  <div class="list-group">
    <?php foreach ($recent as $p): ?>
      <a class="list-group-item list-group-item-action" href="<?= $base ?>/posts/show.php?id=<?= (int)$p['id'] ?>">
        <div class="d-flex w-100 justify-content-between">
          <h5 class="mb-1"><?= htmlspecialchars($p['title']) ?></h5>
          <small class="text-muted">
            <time datetime="<?= htmlspecialchars($p['created_at']) ?>"><?= htmlspecialchars($p['created_at']) ?></time>
          </small>
        </div>
        <small class="text-muted">
          <?= htmlspecialchars($p['module_code']) ?> · by <?= htmlspecialchars($p['username']) ?>
        </small>
      </a>
    <?php endforeach; ?>
  </div>
  <div class="mt-3">
    <a class="btn btn-link btn-sm" href="<?= $base ?>/posts/index.php">See all questions →</a>
  </div>
<?php endif; ?>
