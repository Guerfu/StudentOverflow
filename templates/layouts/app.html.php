<?php
// templates/layouts/app.html.php
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>StudentOverflow</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <!-- theme -->
  <link rel="stylesheet" href="<?= base_url(); ?>/public/assets/css/base.css">
  <!-- app CSS -->
  <?php $base = base_url(); ?>
  <link rel="icon" href="<?= $base ?>/public/favicon.ico">
  <link rel="shortcut icon" href="<?= $base ?>/public/favicon.ico">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= $base ?>/public/assets/img/favicon-32.png?v=1">
  <link rel="icon" type="image/png" sizes="16x16" href="<?= $base ?>/public/assets/img/favicon-16.png?v=1">
  <link rel="apple-touch-icon" sizes="180x180" href="<?= $base ?>/public/assets/img/apple-touch-icon.png?v=1">
  <meta name="theme-color" content="#0B1727">

</head>
<body>
  <?php
    // Header
    $headerPath = __DIR__ . '/../partials/header.html.php';
    if (is_file($headerPath)) { include $headerPath; }
  ?>

  <main class="container py-3">
    <?= $content_for_layout ?? '' ?>
  </main>

  <?php
    // Footer
    $footerPath = __DIR__ . '/../partials/footer.html.php';
    if (is_file($footerPath)) { include $footerPath; }
  ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="<?= base_url(); ?>/public/assets/js/app.js"></script>
</body>
</html>
