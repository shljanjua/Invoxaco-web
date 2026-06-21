<?php $path = \App\Core\Request::path(); ?>
<div class="d-flex flex-column p-3 text-white bg-dark h-100" style="min-width:240px;">
  <a href="<?= url('admin/dashboard') ?>" class="d-flex align-items-center mb-3 text-white text-decoration-none fs-5 fw-bold">Invoxaco Admin</a>
  <hr class="text-secondary">
  <ul class="nav nav-pills flex-column mb-auto gap-1">
    <?php
    $links = [
        'admin/dashboard' => ['Dashboard', 'bi-speedometer2'],
        'admin/users' => ['Users', 'bi-people'],
        'admin/subscriptions' => ['Subscriptions', 'bi-credit-card'],
        'admin/payments' => ['Payments', 'bi-cash-stack'],
        'admin/payment-settings' => ['Payment Settings', 'bi-credit-card-2-back'],
        'admin/categories' => ['Categories', 'bi-collection'],
        'admin/generators' => ['Generators', 'bi-file-earmark-text'],
        'admin/blog' => ['Blog', 'bi-newspaper'],
        'admin/contact-messages' => ['Contact Messages', 'bi-envelope'],
        'admin/support-tickets' => ['Support Tickets', 'bi-life-preserver'],
        'admin/seo' => ['SEO Settings', 'bi-search'],
        'admin/smtp' => ['SMTP Settings', 'bi-envelope-paper'],
        'admin/settings' => ['Website Settings', 'bi-gear'],
        'admin/analytics' => ['Analytics', 'bi-bar-chart'],
    ];
    foreach ($links as $href => [$label, $icon]):
        $active = str_starts_with($path, '/' . $href) ? 'active' : '';
    ?>
    <li class="nav-item">
      <a href="<?= url($href) ?>" class="nav-link text-white <?= $active ?>"><i class="bi <?= $icon ?> me-2"></i><?= e($label) ?></a>
    </li>
    <?php endforeach; ?>
  </ul>
  <hr class="text-secondary">
  <a href="<?= url() ?>" class="text-secondary small text-decoration-none mb-2"><i class="bi bi-box-arrow-up-right me-1"></i>View Site</a>
  <form method="POST" action="<?= url('logout') ?>"><?= csrf_field() ?>
    <button class="btn btn-outline-light btn-sm w-100"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
  </form>
</div>
