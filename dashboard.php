<?php
require_once __DIR__ . '/config.php';
$user = require_login();
handle_file_download($user);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    try {
        save_uploaded_file((int) $user['id'], 'client_document', 'client');
        flash('success', 'Document uploaded successfully.');
    } catch (RuntimeException $ex) {
        flash('danger', $ex->getMessage());
    }
    redirect('dashboard.php');
}

$stmt = db()->prepare('SELECT * FROM client_files WHERE client_id = ? ORDER BY uploaded_at DESC');
$stmt->execute([$user['id']]);
$files = $stmt->fetchAll();

$stmt = db()->prepare('SELECT * FROM notifications WHERE client_id IS NULL OR client_id = ? ORDER BY created_at DESC LIMIT 10');
$stmt->execute([$user['id']]);
$notifications = $stmt->fetchAll();

render_header('Dashboard');
?>
<div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
  <div>
    <h1 class="h2 mb-1">Welcome, <?= e($user['name']) ?></h1>
    <p class="muted-small mb-0">Your secure client dashboard for tax consultancy documents and updates.</p>
  </div>
  <a class="btn btn-outline-danger align-self-start" href="logout.php">Logout</a>
</div>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="portal-card bg-white p-4 h-100">
      <h2 class="h4">Profile Details</h2>
      <p class="mb-1"><strong>Name:</strong> <?= e($user['name']) ?></p>
      <p class="mb-1"><strong>Mobile:</strong> <?= e($user['mobile']) ?></p>
      <p class="mb-1"><strong>Email:</strong> <?= e($user['email']) ?></p>
      <p class="mb-0"><strong>Account:</strong> <?= e(ucfirst($user['role'])) ?></p>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="portal-card bg-white p-4 h-100">
      <h2 class="h4">Upload Documents</h2>
      <p class="muted-small">Upload tax documents, GST files, PAN records or other requested documents. Maximum 10 MB.</p>
      <form method="post" enctype="multipart/form-data" class="row g-3">
        <?= csrf_field() ?>
        <div class="col-md-8"><input class="form-control" name="client_document" type="file" required></div>
        <div class="col-md-4 d-grid"><button class="btn btn-primary" type="submit">Upload</button></div>
      </form>
    </div>
  </div>
</div>

<div class="row g-4 mt-1">
  <div class="col-lg-8">
    <div class="portal-card bg-white p-4">
      <h2 class="h4">Download Files</h2>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead><tr><th>File</th><th>Uploaded By</th><th>Date</th><th>Action</th></tr></thead>
          <tbody>
          <?php foreach ($files as $file): ?>
            <tr>
              <td><?= e($file['file_name']) ?></td>
              <td><?= e(ucfirst($file['uploaded_by'])) ?></td>
              <td><?= e($file['uploaded_at']) ?></td>
              <td><a class="btn btn-sm btn-outline-primary" href="dashboard.php?download=<?= (int) $file['id'] ?>">Download</a></td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$files): ?><tr><td colspan="4" class="text-muted">No files uploaded yet.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="portal-card bg-white p-4">
      <h2 class="h4">Notifications</h2>
      <?php foreach ($notifications as $note): ?>
        <div class="border-bottom pb-3 mb-3">
          <strong><?= e($note['title']) ?></strong>
          <p class="muted-small mb-1"><?= e($note['message']) ?></p>
          <small class="text-muted"><?= e($note['created_at']) ?></small>
        </div>
      <?php endforeach; ?>
      <?php if (!$notifications): ?><p class="text-muted mb-0">No notifications yet.</p><?php endif; ?>
    </div>
  </div>
</div>
<?php render_footer(); ?>
