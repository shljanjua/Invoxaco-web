<?php $user = auth_user(); ?>
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom shadow-sm py-2" id="mainNavbar">
  <div class="container">
    <a class="navbar-brand fw-bold fs-4 d-flex align-items-center gap-2" href="<?= url() ?>" id="navbarBrand">
      <img src="<?= asset('img/logo.png') ?>" alt="Invoxaco" height="34">
      Invoxaco
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse bg-white" id="mainNav">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1">
        <li class="nav-item"><a class="nav-link" href="<?= url('generators') ?>">Generators</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('store') ?>">Store</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('calculators') ?>">Calculators</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('features') ?>">Features</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('pricing') ?>">Pricing</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('blog') ?>">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('contact') ?>">Contact</a></li>
      </ul>
      <div class="d-flex gap-2 align-items-center">
        <button type="button" id="themeToggle" class="btn btn-light border" title="Toggle dark / light mode">
          <i class="bi bi-moon-stars"></i>
        </button>
        <?php if ($user): ?>
          <a href="<?= url($user['role'] === 'admin' ? 'admin/dashboard' : 'dashboard') ?>" class="btn btn-outline-primary">Dashboard</a>
          <div class="dropdown">
            <button class="btn btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
              <i class="bi bi-person-circle me-1"></i><?= e(explode(' ', $user['name'])[0]) ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <?php if ($user['role'] !== 'admin'): ?>
              <li><a class="dropdown-item" href="<?= url('downloads') ?>"><i class="bi bi-download me-2"></i>My Downloads</a></li>
              <li><a class="dropdown-item" href="<?= url('settings') ?>"><i class="bi bi-gear me-2"></i>Settings</a></li>
              <?php endif; ?>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form method="POST" action="<?= url('logout') ?>" class="px-3">
                  <?= csrf_field() ?>
                  <button class="dropdown-item p-0 text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                </form>
              </li>
            </ul>
          </div>
        <?php else: ?>
          <a href="<?= url('login') ?>" class="btn btn-outline-primary">Log In</a>
          <a href="<?= url('register') ?>" class="btn btn-primary">Sign Up Free</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
<script>
(function () {
  var btn = document.getElementById('themeToggle');
  if (!btn) return;
  var icon = btn.querySelector('i');
  function sync() {
    var dark = document.documentElement.getAttribute('data-theme') === 'dark';
    icon.className = dark ? 'bi bi-sun' : 'bi bi-moon-stars';
  }
  sync();
  btn.addEventListener('click', function () {
    var next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('invox-theme', next);
    sync();
  });
})();
</script>
