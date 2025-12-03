<?php
// header partial
$base = base_url();
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin    = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$username   = $_SESSION['username'] ?? 'Guest';

$searchOptions = $searchOptions ?? [];
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">

    <!-- Brand with logo  -->
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?= $base ?>/public/index.php" style="text-decoration:none">
      <picture>
        <source srcset="<?= $base ?>/public/assets/img/logo-light.png" media="(prefers-color-scheme: light)">
        <!-- Default / dark mode -->
        <img
          src="<?= $base ?>/public/assets/img/logo-dark.png"
          alt="StudentOverflow logo"
          width="200px" height="60"
          style="width:200px;height:60px;object-fit:contain;border-radius:6px"
          loading="eager" decoding="async"
        >
      </picture>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNav"
            aria-controls="topNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="topNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>/posts/index.php">Posts</a></li>
        <?php if ($isLoggedIn): ?>
          <li class="nav-item"><a class="nav-link" href="<?= $base ?>/posts/create.php">Ask</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= $base ?>/public/contact.php">Contact</a></li>
        <?php endif; ?>

        <?php if ($isAdmin): ?>
          <li class="nav-item"><a class="nav-link" href="<?= $base ?>/users/index.php">Users</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= $base ?>/modules/index.php">Modules</a></li>
        <?php endif; ?>
      </ul>

      <!-- Quick Search (GET -> posts/index.php?q=...) -->
      <form class="d-flex ms-auto" role="search" action="<?= $base ?>/posts/index.php" method="get">
        <input
          class="form-control form-control-sm"
          type="search"
          name="q"
          placeholder="Search posts…"
          aria-label="Search"
          list="post-suggestions"
          value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
        >
        <?php if (!empty($searchOptions) && is_array($searchOptions)): ?>
          <datalist id="post-suggestions">
            <?php foreach ($searchOptions as $opt): ?>
              <option value="<?= htmlspecialchars($opt) ?>"></option>
            <?php endforeach; ?>
          </datalist>
        <?php endif; ?>
        <button class="btn btn-sm btn-outline-primary ms-2" type="submit">Search</button>
      </form>

      <ul class="navbar-nav ms-3">
        <?php if ($isLoggedIn): ?>
          <li class="nav-item d-flex align-items-center">
            <span class="navbar-text bg-dark text-dark px-3 py-1 rounded-pill me-2 small">
              Hi, <?= htmlspecialchars($username) ?>
            </span>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= $base ?>/users/logout.php">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= $base ?>/users/login.php">Login / Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

