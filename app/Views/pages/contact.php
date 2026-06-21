<div class="container py-5" style="max-width:700px;">
  <h1 class="fw-bold mb-2 text-center">Contact Us</h1>
  <p class="text-secondary text-center mb-5">Have a question or feedback? We'd love to hear from you.</p>

  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
      <form method="POST" action="<?= url('contact') ?>">
        <?= csrf_field() ?>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="<?= old('name') ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
          </div>
          <div class="col-12">
            <label class="form-label">Subject</label>
            <input type="text" name="subject" class="form-control" value="<?= old('subject') ?>">
          </div>
          <div class="col-12">
            <label class="form-label">Message</label>
            <textarea name="message" rows="5" class="form-control" required><?= old('message') ?></textarea>
          </div>
        </div>
        <button class="btn btn-primary mt-4 w-100">Send Message</button>
      </form>
    </div>
  </div>

  <p class="text-secondary text-center mt-4 small">Or email us directly at <a href="mailto:support@invoxaco.com">support@invoxaco.com</a></p>
</div>
