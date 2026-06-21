<div class="container py-5" style="max-width:800px;">
  <h1 class="fw-bold mb-4 text-center">Frequently Asked Questions</h1>
  <div class="accordion" id="faqAccordion">
    <?php foreach ($faqs as $i => $faq): ?>
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button <?= $i > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?= $i ?>">
          <?= e($faq['q']) ?>
        </button>
      </h2>
      <div id="faq<?= $i ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordion">
        <div class="accordion-body small text-secondary"><?= e($faq['a']) ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <p class="text-center text-secondary mt-4">Still have questions? <a href="<?= url('contact') ?>">Contact our support team</a>.</p>
</div>
