<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php echo \App\Core\View::renderRaw('partials/meta', get_defined_vars()); ?>
<link rel="icon" href="<?= asset('img/favicon.png') ?>">
<link rel="preconnect" href="https://cdn.jsdelivr.net">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="<?= asset('css/app.css') ?>" rel="stylesheet">
<script>
(function () {
  var theme = localStorage.getItem('invox-theme') || 'light';
  document.documentElement.setAttribute('data-theme', theme);
})();
</script>
</head>
<body>
<?= \App\Core\View::renderRaw('partials/analytics', []) ?>
<?= \App\Core\View::renderRaw('partials/navbar', []) ?>
<main>
<div class="container mt-3">
<?= \App\Core\View::renderRaw('partials/flash', []) ?>
</div>
<?= $content ?? '' ?>
</main>
<?= \App\Core\View::renderRaw('partials/footer', []) ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
