<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php echo \App\Core\View::renderRaw('partials/meta', get_defined_vars()); ?>
<link rel="icon" href="<?= asset('img/favicon.png') ?>">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="<?= asset('css/app.css') ?>" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="text-center mb-4">
    <a href="<?= url() ?>" class="fw-bold fs-3 text-primary text-decoration-none">Invoxaco</a>
  </div>
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-md-5">
          <?= \App\Core\View::renderRaw('partials/flash', []) ?>
          <?= $content ?? '' ?>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
