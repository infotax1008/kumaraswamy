<?php
require_once __DIR__ . '/config.php';
$admin = require_admin();
handle_file_download($admin);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'add_client') {
            $password = (string) ($_POST['password'] ?? '');
            if (strlen($password) < 8) {
                throw new RuntimeException('Password must be at least 8 characters.');
            }
            $stmt = db()->prepare('INSERT INTO clients (name, mobile, email, password_hash, role) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([
                trim($_POST['name'] ?? ''),
                trim($_POST['mobile'] ?? ''),
                strtolower(trim($_POST['email'] ?? '')),
                password_hash($password, PASSWORD_DEFAULT),
                ($_POST['role'] ?? '') === 'admin' ? 'admin' : 'client',
            ]);
            flash('success', 'Client added.');
        }

        if ($action === 'edit_client') {
            $clientId = (int) ($_POST['client_id'] ?? 0);
            $password = (string) ($_POST['password'] ?? '');
            if ($password !== '') {
                $stmt = db()->prepare('UPDATE clients SET name = ?, mobile = ?, email = ?, role = ?, password_hash = ? WHERE id = ?');
                $stmt->execute([trim($_POST['name'] ?? ''), trim($_POST['mobile'] ?? ''), strtolower(trim($_POST['email'] ?? '')), ($_POST['role'] ?? '') === 'admin' ? 'admin' : 'client', password_hash($password, PASSWORD_DEFAULT), $clientId]);
            } else {
                $stmt = db()->prepare('UPDATE clients SET name = ?, mobile = ?, email = ?, role = ? WHERE id = ?');
                $stmt->execute([trim($_POST['name'] ?? ''), trim($_POST['mobile'] ?? ''), strtolower(trim($_POST['email'] ?? '')), ($_POST['role'] ?? '') === 'admin' ? 'admin' : 'client', $clientId]);
            }
            flash('success', 'Client updated.');
        }

        if ($action === 'delete_client') {
            $clientId = (int) ($_POST['client_id'] ?? 0);
            if ($clientId === (int) $admin['id']) {
                throw new RuntimeException('You cannot delete your own admin account.');
            }
            $stmt = db()->prepare('DELETE FROM clients WHERE id = ?');
            $stmt->execute([$clientId]);
            flash('success', 'Client deleted.');
        }

        if ($action === 'upload_file') {
            save_uploaded_file((int) ($_POST['client_id'] ?? 0), 'admin_file', 'admin');
            flash('success', 'File uploaded for client.');
        }

        if ($action === 'notify') {
            $clientId = (int) ($_POST['client_id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $message = trim($_POST['message'] ?? '');
            if ($title === '' || $message === '') {
                throw new RuntimeException('Notification title and message are required.');
            }
            $stmt = db()->prepare('INSERT INTO notifications (client_id, title, message) VALUES (?, ?, ?)');
            $stmt->execute([$clientId > 0 ? $clientId : null, $title, $message]);
            flash('success', 'Notification sent.');
        }
    } catch (Throwable $ex) {
        flash('danger', $ex->getMessage());
    }

    redirect('admin.php');
}

$clients = db()->query('SELECT id, name, mobile, email, role, created_at FROM clients ORDER BY created_at DESC')->fetchAll();
$files = db()->query('SELECT f.*, c.name AS client_name FROM client_files f JOIN clients c ON c.id = f.client_id ORDER BY f.uploaded_at DESC LIMIT 30')->fetchAll();

render_header('Admin Panel');
?>
<div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
  <div><h1 class="h2 mb-1">Admin Panel</h1><p class="muted-small mb-0">View clients, manage accounts, upload files and send notifications.</p></div>
  <a class="btn btn-outline-danger align-self-start" href="logout.php">Logout</a>
</div>

<div class="row g-4 mb-4">
  <div class="col-lg-5">
    <div class="portal-card bg-white p-4 h-100">
      <h2 class="h4">Add Client</h2>
      <form method="post" class="row g-3">
        <?= csrf_field() ?><input type="hidden" name="action" value="add_client">
        <div class="col-md-6"><input class="form-control" name="name" placeholder="Name" required></div>
        <div class="col-md-6"><input class="form-control" name="mobile" placeholder="Mobile" required></div>
        <div class="col-md-6"><input class="form-control" name="email" type="email" placeholder="Email" required></div>
        <div class="col-md-6"><input class="form-control" name="password" type="password" placeholder="Password" minlength="8" required></div>
        <div class="col-md-6"><select class="form-select" name="role"><option value="client">Client</option><option value="admin">Admin</option></select></div>
        <div class="col-md-6 d-grid"><button class="btn btn-primary" type="submit">Add</button></div>
      </form>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="portal-card bg-white p-4 h-100">
      <h2 class="h4">Upload File for Client</h2>
      <form method="post" enctype="multipart/form-data" class="row g-3">
        <?= csrf_field() ?><input type="hidden" name="action" value="upload_file">
        <div class="col-md-5"><select class="form-select" name="client_id" required><?php foreach ($clients as $client): ?><option value="<?= (int) $client['id'] ?>"><?= e($client['name']) ?> - <?= e($client['email']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-4"><input class="form-control" name="admin_file" type="file" required></div>
        <div class="col-md-3 d-grid"><button class="btn btn-primary" type="submit">Upload</button></div>
      </form>
      <hr>
      <h2 class="h4">Send Notification</h2>
      <form method="post" class="row g-3">
        <?= csrf_field() ?><input type="hidden" name="action" value="notify">
        <div class="col-md-4"><select class="form-select" name="client_id"><option value="0">All Clients</option><?php foreach ($clients as $client): ?><option value="<?= (int) $client['id'] ?>"><?= e($client['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-4"><input class="form-control" name="title" placeholder="Title" required></div>
        <div class="col-md-4"><input class="form-control" name="message" placeholder="Message" required></div>
        <div class="col-12 d-grid"><button class="btn btn-outline-primary" type="submit">Send Notification</button></div>
      </form>
    </div>
  </div>
</div>

<div class="portal-card bg-white p-4 mb-4">
  <h2 class="h4">All Clients</h2>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead><tr><th>Name</th><th>Mobile</th><th>Email</th><th>Role</th><th>New Password</th><th>Actions</th></tr></thead>
      <tbody>
      <?php foreach ($clients as $client): ?>
        <tr>
          <?php $editFormId = 'edit-client-' . (int) $client['id']; ?>
          <td><input class="form-control form-control-sm" form="<?= e($editFormId) ?>" name="name" value="<?= e($client['name']) ?>" required></td>
          <td><input class="form-control form-control-sm" form="<?= e($editFormId) ?>" name="mobile" value="<?= e($client['mobile']) ?>" required></td>
          <td><input class="form-control form-control-sm" form="<?= e($editFormId) ?>" name="email" type="email" value="<?= e($client['email']) ?>" required></td>
          <td><select class="form-select form-select-sm" form="<?= e($editFormId) ?>" name="role"><option value="client" <?= $client['role'] === 'client' ? 'selected' : '' ?>>Client</option><option value="admin" <?= $client['role'] === 'admin' ? 'selected' : '' ?>>Admin</option></select></td>
          <td><input class="form-control form-control-sm" form="<?= e($editFormId) ?>" name="password" type="password" placeholder="Leave blank"></td>
          <td class="d-flex gap-2">
            <form id="<?= e($editFormId) ?>" method="post">
              <?= csrf_field() ?><input type="hidden" name="action" value="edit_client"><input type="hidden" name="client_id" value="<?= (int) $client['id'] ?>">
              <button class="btn btn-sm btn-primary" type="submit">Save</button>
            </form>
            <form method="post" onsubmit="return confirm('Delete this client?');">
              <?= csrf_field() ?><input type="hidden" name="action" value="delete_client"><input type="hidden" name="client_id" value="<?= (int) $client['id'] ?>">
            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="portal-card bg-white p-4">
  <h2 class="h4">Recent Files</h2>
  <div class="table-responsive">
    <table class="table table-hover"><thead><tr><th>Client</th><th>File</th><th>Uploaded By</th><th>Date</th><th>Action</th></tr></thead><tbody>
      <?php foreach ($files as $file): ?>
        <tr><td><?= e($file['client_name']) ?></td><td><?= e($file['file_name']) ?></td><td><?= e($file['uploaded_by']) ?></td><td><?= e($file['uploaded_at']) ?></td><td><a class="btn btn-sm btn-outline-primary" href="admin.php?download=<?= (int) $file['id'] ?>">Download</a></td></tr>
      <?php endforeach; ?>
      <?php if (!$files): ?><tr><td colspan="5" class="text-muted">No files yet.</td></tr><?php endif; ?>
    </tbody></table>
  </div>
</div>
<?php render_footer(); ?>
