<?php $user = auth_user(); ?>
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom shadow-sm py-2">
  <div class="container">
    <a class="navbar-brand fw-bold fs-4 text-primary" href="<?= url() ?>">Invoxaco</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1">
        <li class="nav-item"><a class="nav-link" href="<?= url('generators') ?>">Generators</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('features') ?>">Features</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('pricing') ?>">Pricing</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('blog') ?>">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('contact') ?>">Contact</a></li>
      </ul>
      <div class="d-flex gap-2">
        <?php if ($user): ?>
          <a href="<?= url($user['role'] === 'admin' ? 'admin/dashboard' : 'dashboard') ?>" class="btn btn-outline-primary">Dashboard</a>
        <?php else: ?>
          <a href="<?= url('login') ?>" class="btn btn-outline-primary">Log In</a>
          <a href="<?= url('register') ?>" class="btn btn-primary">Sign Up Free</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
