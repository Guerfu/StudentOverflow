<?php
// templates/posts/show.html.php (template)
// expects: $post, $uid, $isOwner, $comments, $imgUrl
?>

<div class="d-flex align-items-center justify-content-between mb-2">
  <h1 class="h4 mb-0"><?= htmlspecialchars($post['title']) ?></h1>
  <div>
    <?php if ($isOwner): ?>
      <a class="btn btn-primary btn-sm" href="<?= base_url(); ?>/posts/edit.php?id=<?= (int)$post['id'] ?>">Edit</a>
      <a class="btn btn-outline-danger btn-sm"
         href="<?= base_url(); ?>/posts/delete.php?id=<?= (int)$post['id'] ?>"
         onclick="return confirm('Delete this post?');">Delete</a>
    <?php endif; ?>
  </div>
</div>

<?php if ($imgUrl): ?>
  <div class="mb-2">
    <img src="<?= htmlspecialchars($imgUrl) ?>"
         alt="screenshot"
         class="img-fluid rounded"
         style="height:100%;width:100%;object-fit:cover;">
  </div>
<?php endif; ?>

<div class="text-muted small mb-3">
  by <strong><?= htmlspecialchars($post['username']) ?></strong> •
  <?= htmlspecialchars($post['module_code']) ?> •
  <time datetime="<?= htmlspecialchars($post['created_at']) ?>"><?= htmlspecialchars($post['created_at']) ?></time>
</div>

<div class="card mb-3">
  <div class="card-body">
    <div class="mb-3">
      <?= nl2br(htmlspecialchars($post['content'])) ?>
    </div>

    <div class="d-flex align-items-center gap-2">
      <form method="post" action="<?= base_url(); ?>/posts/upvote.php" class="d-inline">
        <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
        <input type="hidden" name="back" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
        <?php if ((int)$post['already_upvoted'] === 1): ?>
          <button class="btn btn-success btn-sm" disabled>▲ Upvoted</button>
        <?php else: ?>
          <button class="btn btn-outline-success btn-sm" <?= $uid ? '' : 'disabled' ?>>▲ Upvote</button>
        <?php endif; ?>
      </form>

      <span class="badge text-bg-success">Urgency: <?= (int)$post['pu_cnt'] ?></span>
    </div>
  </div>
</div>

<h2 class="h5 mb-3">Comments</h2>

<?php if ($uid): ?>
  <form method="post" action="<?= base_url(); ?>/comments/create.php" class="border p-3 rounded mb-3">
    <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
    <div class="mb-2">
      <textarea name="content" class="form-control" rows="3" placeholder="Write a comment..." required></textarea>
    </div>
    <button class="btn btn-primary btn-sm">Post Comment</button>
  </form>
<?php else: ?>
  <div class="alert alert-info">Log in to comment.</div>
<?php endif; ?>

<?php if (!$comments): ?>
  <div class="alert alert-secondary">No comments yet.</div>
<?php else: ?>
  <?php foreach ($comments as $c): ?>
    <div class="border rounded p-2 mb-2">
      <div class="small text-muted mb-1">
        <strong><?= htmlspecialchars($c['username']) ?></strong> •
        <time datetime="<?= htmlspecialchars($c['created_at']) ?>"><?= htmlspecialchars($c['created_at']) ?></time>
      </div>
      <div class="mb-2"><?= nl2br(htmlspecialchars($c['content'])) ?></div>

      <div class="d-flex align-items-center gap-2">
        <?php if ($uid): ?>
          <!-- Like -->
          <form method="post" action="<?= base_url(); ?>/comments/like.php" class="d-inline">
            <input type="hidden" name="comment_id" value="<?= (int)$c['id'] ?>">
            <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
            <button class="btn btn-sm <?= $c['i_liked'] ? 'btn-secondary' : 'btn-outline-secondary' ?>"
                    <?= $c['i_liked'] ? 'disabled' : '' ?>>
              👍 Like (<?= (int)$c['like_count'] ?>)
            </button>
          </form>
        <?php else: ?>
          <span class="badge bg-secondary-subtle">👍 <?= (int)$c['like_count'] ?></span>
        <?php endif; ?>

        <!-- Kudos (only post owner or admin) -->
        <?php if ($isOwner): ?>
          <form method="post" action="<?= base_url(); ?>/comments/kudos.php" class="d-inline">
            <input type="hidden" name="comment_id" value="<?= (int)$c['id'] ?>">
            <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
            <button class="btn btn-sm <?= $c['i_kudosed'] ? 'btn-success' : 'btn-outline-success' ?>"
                    <?= $c['i_kudosed'] ? 'disabled' : '' ?>>
              🎉 Kudos (<?= (int)$c['kudos_count'] ?>)
            </button>
          </form>
        <?php else: ?>
          <span class="badge bg-success-subtle">🎉 <?= (int)$c['kudos_count'] ?></span>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
