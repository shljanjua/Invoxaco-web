<div class="container py-5">
  <div class="text-center mb-5">
    <h1 class="fw-bold">Simple, Transparent Pricing</h1>
    <p class="text-secondary fs-5">Start free. Upgrade when you need more.</p>
  </div>

  <div class="row g-4 justify-content-center">
    <?php foreach ($plans as $key => $plan): ?>
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm rounded-4 h-100 <?= $key === 'pro' ? 'border-primary border-2' : '' ?>">
        <div class="card-body p-4 d-flex flex-column h-100">
          <?php if ($key === 'pro'): ?><span class="badge bg-primary mb-2 align-self-start">Most Popular</span><?php endif; ?>
          <h4 class="fw-bold"><?= e($plan['name']) ?></h4>
          <div class="mb-3">
            <span class="fs-2 fw-bold">$<?= number_format($plan['price_monthly'], 2) ?></span>
            <span class="text-secondary">/month</span>
            <?php if ($plan['price_yearly'] > 0): ?>
              <div class="text-secondary small">or $<?= number_format($plan['price_yearly'], 2) ?>/year</div>
            <?php endif; ?>
          </div>
          <ul class="list-unstyled small flex-grow-1">
            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><?= $plan['document_limit'] === null ? 'Unlimited documents' : $plan['document_limit'] . ' documents / month' ?></li>
            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><?= $plan['templates'] === 'premium' ? 'Access to all templates' : 'Basic templates' ?></li>
            <li class="mb-2"><i class="bi <?= $plan['watermark'] ? 'bi-x-circle text-secondary' : 'bi-check-circle-fill text-success' ?> me-2"></i><?= $plan['watermark'] ? 'Watermarked exports' : 'No watermark' ?></li>
            <li class="mb-2"><i class="bi <?= $plan['custom_logo'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle text-secondary' ?> me-2"></i>Custom logo &amp; signature</li>
            <li class="mb-2"><i class="bi <?= $plan['docx_export'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle text-secondary' ?> me-2"></i>PDF + DOCX export</li>
            <li class="mb-2"><i class="bi <?= $plan['email_sending'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle text-secondary' ?> me-2"></i>Email documents to clients</li>
            <li class="mb-2"><i class="bi <?= $plan['client_management'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle text-secondary' ?> me-2"></i>Client management</li>
            <li class="mb-2"><i class="bi <?= $plan['white_label'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle text-secondary' ?> me-2"></i>White-label branding</li>
            <li class="mb-2"><i class="bi <?= $plan['team_members'] !== 0 ? 'bi-check-circle-fill text-success' : 'bi-x-circle text-secondary' ?> me-2"></i><?= $plan['team_members'] === null ? 'Unlimited team members' : ($plan['team_members'] === 0 ? 'No team members' : $plan['team_members'] . ' team members') ?></li>
            <li class="mb-2"><i class="bi <?= $plan['api_access'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle text-secondary' ?> me-2"></i>API access</li>
            <li class="mb-2"><i class="bi <?= $plan['ai_writer'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle text-secondary' ?> me-2"></i>AI writing assistant</li>
            <li class="mb-2"><i class="bi <?= $plan['priority_support'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle text-secondary' ?> me-2"></i>Priority support</li>
            <li class="mb-2"><i class="bi <?= $plan['bulk_generation'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle text-secondary' ?> me-2"></i>Bulk generation</li>
          </ul>
          <a href="<?= url($key === 'free' ? 'register' : 'register') ?>" class="btn <?= $key === 'pro' ? 'btn-primary' : 'btn-outline-primary' ?> w-100 mt-3">
            <?= $key === 'free' ? 'Start Free' : 'Choose ' . e($plan['name']) ?>
          </a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <p class="text-center text-secondary small mt-5">Prices in USD. Plan changes take effect at the end of the current billing cycle. See our <a href="<?= url('legal/refund-policy') ?>">Refund Policy</a> for details.</p>
</div>
