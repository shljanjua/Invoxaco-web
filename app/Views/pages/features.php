<div class="container py-5">
  <div class="text-center mb-5">
    <h1 class="fw-bold">Features</h1>
    <p class="text-secondary fs-5">Everything you need to create, manage, and deliver business documents.</p>
  </div>

  <div class="row g-4">
    <?php
    $features = [
      ['icon' => 'bi-file-earmark-text', 'title' => '110+ Document Generators', 'body' => 'Invoices, receipts, quotations, purchase orders, contracts, NDAs, offer letters, proposals, and more across 10 categories.'],
      ['icon' => 'bi-cloud-arrow-up', 'title' => 'Autosave', 'body' => 'Your work is saved automatically as you type, so you never lose progress on a document.'],
      ['icon' => 'bi-file-pdf', 'title' => 'PDF Export', 'body' => 'Download any document as a polished, print-ready PDF in one click.'],
      ['icon' => 'bi-file-word', 'title' => 'DOCX Export', 'body' => 'Pro and Premium users can export documents as editable Microsoft Word files.'],
      ['icon' => 'bi-envelope', 'title' => 'Email Delivery', 'body' => 'Send a finished document directly to a client\'s inbox as a PDF or DOCX attachment.'],
      ['icon' => 'bi-link-45deg', 'title' => 'Shareable Links', 'body' => 'Generate a secure link to let anyone view a document online without needing an account.'],
      ['icon' => 'bi-people', 'title' => 'Client Management', 'body' => 'Save client details once and reuse them across every document you create.'],
      ['icon' => 'bi-person-badge', 'title' => 'Custom Branding', 'body' => 'Add your company logo and signature so every document looks professionally branded.'],
      ['icon' => 'bi-diagram-3', 'title' => 'Team Collaboration', 'body' => 'Premium plans support inviting team members to collaborate on documents together.'],
      ['icon' => 'bi-printer', 'title' => 'Print Ready', 'body' => 'Every document is formatted to print cleanly straight from your browser.'],
      ['icon' => 'bi-copy', 'title' => 'Duplicate &amp; Reuse', 'body' => 'Clone any document as a starting point for similar future paperwork.'],
      ['icon' => 'bi-shield-lock', 'title' => 'Secure by Default', 'body' => 'Encrypted connections, hashed passwords, and account-level access controls protect your data.'],
    ];
    ?>
    <?php foreach ($features as $f): ?>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-body p-4">
          <i class="bi <?= $f['icon'] ?> fs-2 text-primary mb-3 d-block"></i>
          <h5 class="fw-bold"><?= e($f['title']) ?></h5>
          <p class="text-secondary small mb-0"><?= $f['body'] ?></p>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="text-center mt-5">
    <a href="<?= url('register') ?>" class="btn btn-primary btn-lg">Start Creating Documents Free</a>
  </div>
</div>
