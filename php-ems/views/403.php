<?php include __DIR__ . '/partials/head.php'; ?>

<div class="container py-5">
  <div class="card shadow-sm">
    <div class="card-body">
      <h1 class="h4 mb-2">403 — Access Denied</h1>
      <p class="text-muted mb-4">You don't have permission to access this page.</p>
      <a class="btn btn-primary" href="<?= e(base_url('dashboard')) ?>">Back to Dashboard</a>
    </div>
  </div>
</div>
<?php include __DIR__ . '/partials/scripts.php'; ?>
