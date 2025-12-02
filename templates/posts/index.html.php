<?php
// templates/posts/index.html.php
// expects: $posts, $allModules, $order, $moduleFilter, $uid, $isAdmin
?>



<form class="row g-2 mb-3" method="get">
  <div class="col-md-4">
    <label class="form-label">Order</label>
    <select name="order" class="form-select">
      <option value="urgent_desc" <?= $order==='urgent_desc'?'selected':'' ?>>Most urgent (most upvotes)</option>
      <option value="urgent_asc"  <?= $order==='urgent_asc'?'selected':'' ?>>Least urgent</option>
      <option value="newest"      <?= $order==='newest'?'selected':'' ?>>Newest</option>
      <option value="oldest"      <?= $order==='oldest'?'selected':'' ?>>Oldest</option>
    </select>
  </div>
  <div class="col-md-4">
    <label class="form-label">Module</label>
    <select name="module" class="form-select">
      <option value="0">All modules</option>
      <?php foreach ($allModules as $m): ?>
        <option value="<?= (int)$m['id'] ?>" <?= $moduleFilter===(int)$m['id']?'selected':'' ?>>
          <?= htmlspecialchars($m['code']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-4 d-flex align-items-end">
    <button class="btn btn-outline-primary">Apply</button>
  </div>
</form>

<div class="d-flex align-items-center justify-content-between mb-3">
  <h1 class="h4 mb-0">Recent Questions</h1>
  <?php if ($uid): ?>
    <a href="<?= base_url(); ?>/posts/create.php" class="btn btn-primary btn-sm">+ Ask a question</a>
  <?php endif; ?>
</div>

<?php if (!$posts): ?>
  <div class="alert alert-info">
    No posts yet. <a href="<?= base_url(); ?>/posts/create.php">Create the first post</a>.
  </div>
<?php else: ?>
  <div class="row g-3">
    <?php foreach ($posts as $p): ?>
      <?php $imgUrl = resolve_image_url($p['image_path'] ?? null); ?>
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="d-flex gap-3">
              <div class="flex-grow-1">
                <h5 class="card-title mb-1">
                  <a href="<?= base_url(); ?>/posts/show.php?id=<?= (int)$p['id'] ?>" class="text-decoration-none">
                    <?= htmlspecialchars($p['title']) ?>
                  </a>
                </h5>

                <div class="text-muted mb-2 small">
                  by <?= htmlspecialchars($p['username']) ?> • <?= htmlspecialchars($p['module_code']) ?> •
                  <time datetime="<?= htmlspecialchars($p['created_at']) ?>">
                    <?= htmlspecialchars($p['created_at']) ?>
                  </time>
                </div>

                <p class="card-text mb-2"><?= nl2br(htmlspecialchars(excerpt($p['content']))) ?></p>

                <div class="d-flex align-items-center gap-2">
                  <form method="post" action="<?= base_url(); ?>/posts/upvote.php" class="d-inline">
                    <input type="hidden" name="post_id" value="<?= (int)$p['id'] ?>">
                    <input type="hidden" name="back" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                    <?php if ((int)$p['already_upvoted'] === 1): ?>
                      <button class="btn btn-sm btn-success" disabled>▲ Upvoted</button>
                    <?php else: ?>
                      <button class="btn btn-sm btn-outline-success" <?= $uid ? '' : 'disabled' ?>>▲ Upvote</button>
                    <?php endif; ?>
                  </form>

                  <span class="badge text-bg-success">Urgency: <?= (int)$p['pu_cnt'] ?></span>

                  <a href="<?= base_url(); ?>/posts/show.php?id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-outline-secondary">
                    View
                  </a>

                  <?php if ($uid && ($isAdmin || (int)$p['user_id'] === (int)$uid)): ?>
                    <a href="<?= base_url(); ?>/posts/edit.php?id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="<?= base_url(); ?>/posts/delete.php?id=<?= (int)$p['id'] ?>"
                       class="btn btn-sm btn-outline-danger"
                       onclick="return confirm('Delete this post?');">Delete</a>
                  <?php endif; ?>
                </div>
              </div>
            <div class="d-flex gap-3">
              <?php if ($imgUrl): ?>
                <div class="flex-shrink-0" style="width:160px;">
                  <img
                    src="<?= htmlspecialchars($imgUrl) ?>"
                    alt="screenshot"
                    class="img-fluid rounded"
                    style="width:160px;height:110px;object-fit:cover;">
                </div>
              <?php endif; ?>
            </div><!-- /flex -->
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
