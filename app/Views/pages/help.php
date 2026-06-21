<div class="container py-5" style="max-width:900px;">
  <h1 class="fw-bold mb-2 text-center">Help Center</h1>
  <p class="text-secondary text-center mb-5">Guides to help you get the most out of Invoxaco.</p>

  <div class="row g-4">
    <?php
    $topics = [
      ['title' => 'Getting Started', 'icon' => 'bi-rocket-takeoff', 'body' => 'Create a free account, browse generators, and create your first document in under two minutes.'],
      ['title' => 'Creating a Document', 'icon' => 'bi-file-earmark-plus', 'body' => 'Choose a generator from the catalog, fill in the form, and save it as a draft or finalize it.'],
      ['title' => 'Downloading &amp; Sharing', 'icon' => 'bi-download', 'body' => 'Export any finished document as PDF or DOCX, print it, email it, or generate a secure shareable link.'],
      ['title' => 'Managing Clients', 'icon' => 'bi-people', 'body' => 'Add clients once and select them when creating documents to auto-fill their details.'],
      ['title' => 'Billing &amp; Plans', 'icon' => 'bi-credit-card', 'body' => 'View, upgrade, downgrade, or cancel your subscription from your account dashboard at any time.'],
      ['title' => 'Account &amp; Security', 'icon' => 'bi-shield-lock', 'body' => 'Verify your email, reset your password, and manage your branding (logo and signature) from account settings.'],
    ];
    ?>
    <?php foreach ($topics as $t): ?>
    <div class="col-md-6">
      <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-body p-4">
          <i class="bi <?= $t['icon'] ?> fs-2 text-primary mb-2 d-block"></i>
          <h5 class="fw-bold"><?= $t['title'] ?></h5>
          <p class="text-secondary small mb-0"><?= $t['body'] ?></p>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="text-center mt-5">
    <p class="text-secondary">Can't find what you're looking for?</p>
    <a href="<?= url('contact') ?>" class="btn btn-outline-primary me-2">Contact Us</a>
    <a href="<?= url('faq') ?>" class="btn btn-primary">View FAQ</a>
  </div>
</div>
