<footer class="bg-dark text-light pt-5 pb-4 mt-5">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="d-flex align-items-center gap-2 mb-3">
          <img src="<?= asset('img/logo.png') ?>" alt="Invoxaco" height="32">
          <h5 class="fw-bold text-white mb-0">Invoxaco</h5>
        </div>
        <p class="text-secondary small">Generate professional business documents — invoices, contracts, quotations, and more — in minutes. Save, edit, download, and email them directly from your dashboard.</p>
        <p class="text-secondary small mb-0">Support: <a class="text-light" href="mailto:support@invoxaco.com">support@invoxaco.com</a></p>
      </div>
      <div class="col-lg-2 col-6">
        <h6 class="text-white mb-3">Product</h6>
        <ul class="list-unstyled small">
          <li class="mb-2"><a class="text-secondary text-decoration-none" href="<?= url('generators') ?>">Generators</a></li>
          <li class="mb-2"><a class="text-secondary text-decoration-none" href="<?= url('features') ?>">Features</a></li>
          <li class="mb-2"><a class="text-secondary text-decoration-none" href="<?= url('pricing') ?>">Pricing</a></li>
          <li class="mb-2"><a class="text-secondary text-decoration-none" href="<?= url('templates') ?>">Templates</a></li>
        </ul>
      </div>
      <div class="col-lg-2 col-6">
        <h6 class="text-white mb-3">Company</h6>
        <ul class="list-unstyled small">
          <li class="mb-2"><a class="text-secondary text-decoration-none" href="<?= url('about') ?>">About Us</a></li>
          <li class="mb-2"><a class="text-secondary text-decoration-none" href="<?= url('blog') ?>">Blog</a></li>
          <li class="mb-2"><a class="text-secondary text-decoration-none" href="<?= url('contact') ?>">Contact</a></li>
          <li class="mb-2"><a class="text-secondary text-decoration-none" href="<?= url('faq') ?>">FAQ</a></li>
          <li class="mb-2"><a class="text-secondary text-decoration-none" href="<?= url('help') ?>">Help Center</a></li>
        </ul>
      </div>
      <div class="col-lg-4">
        <h6 class="text-white mb-3">Legal</h6>
        <div class="row">
          <div class="col-6">
            <ul class="list-unstyled small">
              <li class="mb-2"><a class="text-secondary text-decoration-none" href="<?= url('legal/privacy-policy') ?>">Privacy Policy</a></li>
              <li class="mb-2"><a class="text-secondary text-decoration-none" href="<?= url('legal/terms-of-service') ?>">Terms of Service</a></li>
              <li class="mb-2"><a class="text-secondary text-decoration-none" href="<?= url('legal/refund-policy') ?>">Refund Policy</a></li>
              <li class="mb-2"><a class="text-secondary text-decoration-none" href="<?= url('legal/cookie-policy') ?>">Cookie Policy</a></li>
              <li class="mb-2"><a class="text-secondary text-decoration-none" href="<?= url('legal/disclaimer') ?>">Disclaimer</a></li>
            </ul>
          </div>
          <div class="col-6">
            <ul class="list-unstyled small">
              <li class="mb-2"><a class="text-secondary text-decoration-none" href="<?= url('legal/acceptable-use-policy') ?>">Acceptable Use</a></li>
              <li class="mb-2"><a class="text-secondary text-decoration-none" href="<?= url('legal/data-processing-policy') ?>">Data Processing</a></li>
              <li class="mb-2"><a class="text-secondary text-decoration-none" href="<?= url('legal/gdpr-compliance') ?>">GDPR</a></li>
              <li class="mb-2"><a class="text-secondary text-decoration-none" href="<?= url('legal/ccpa-compliance') ?>">CCPA</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <hr class="border-secondary my-4">
    <div class="d-flex flex-wrap justify-content-between small text-secondary">
      <span>&copy; <?= date('Y') ?> Invoxaco. All rights reserved.</span>
      <span>Made for businesses that move fast.</span>
    </div>
  </div>
</footer>
