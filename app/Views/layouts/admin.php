<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? 'Admin') ?> - Invoxaco Admin</title>
<meta name="robots" content="noindex,nofollow">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="<?= asset('css/admin.css') ?>" rel="stylesheet">
</head>
<body>
<div class="d-flex" style="min-height:100vh;">
  <?= \App\Core\View::renderRaw('partials/sidebar_admin', []) ?>
  <div class="flex-grow-1 bg-light">
    <div class="container-fluid p-4">
      <?= \App\Core\View::renderRaw('partials/flash', []) ?>
      <?= $content ?? '' ?>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
