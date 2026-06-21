<?php
$success = flash('success');
$error = flash('error');
?>
<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert" data-auto-dismiss>
    <?= e($success) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert" data-auto-dismiss>
    <?= e($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>
