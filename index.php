<?php
require_once __DIR__ . '/config.php';
$user = current_user();
render_header('Client Portal');
?>
<section class="hero-strip p-4 p-lg-5 mb-4">
  <div class="row align-items-center g-4">
    <div class="col-lg-8">
      <p class="text-uppercase fw-bold mb-2">Secure Client Login System</p>
      <h1 class="display-5 fw-bold">Manage tax documents, client files and consultancy updates.</h1>
      <p class="lead mb-0">A professional portal for Kumaraswamy Tax Consultancy clients and administrators.</p>
    </div>
    <div class="col-lg-4 d-grid gap-2">
      <?php if ($user): ?>
        <a class="btn btn-light btn-lg" href="dashboard.php">Open Dashboard</a>
        <?php if ($user['role'] === 'admin'): ?><a class="btn btn-outline-light btn-lg" href="admin.php">Open Admin Panel</a><?php endif; ?>
      <?php else: ?>
        <a class="btn btn-light btn-lg" href="login.php">Client Login</a>
        <a class="btn btn-outline-light btn-lg" href="register.php">New Client Registration</a>
      <?php endif; ?>
    </div>
  </div>
</section>

<div class="row g-4">
  <div class="col-md-4"><div class="portal-card bg-white p-4 h-100"><h2 class="h5">Secure Access</h2><p class="muted-small mb-0">Password hashing, session management, CAPTCHA and remember-me login support.</p></div></div>
  <div class="col-md-4"><div class="portal-card bg-white p-4 h-100"><h2 class="h5">Documents</h2><p class="muted-small mb-0">Clients can upload documents and download files shared by the office.</p></div></div>
  <div class="col-md-4"><div class="portal-card bg-white p-4 h-100"><h2 class="h5">Admin Tools</h2><p class="muted-small mb-0">Manage clients, upload files and send notifications from one responsive panel.</p></div></div>
</div>
<?php render_footer(); ?>
