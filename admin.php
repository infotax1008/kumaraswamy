<?php
require_once __DIR__ . '/config.php';
requireAdmin();

$notice = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? null)) {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_client') {
        $hash = password_hash($_POST['password'] ?? 'ChangeMe123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO clients (name, mobile, email, password_hash, role) VALUES (?, ?, ?, ?, "client")');
        $stmt->execute([trim($_POST['name']), trim($_POST['mobile']), strtolower(trim($_POST['email'])), $hash]);
        $notice = 'Client added.';
    }

    if ($action === 'edit_client') {
        $stmt = $pdo->prepare('UPDATE clients SET name = ?, mobile = ?, email = ? WHERE id = ? AND role = "client"');
        $stmt->execute([trim($_POST['name']), trim($_POST['mobile']), strtolower(trim($_POST['email'])), (int) $_POST['client_id']]);
        $notice = 'Client updated.';
    }

    if ($action === 'delete_client') {
        $stmt = $pdo->prepare('DELETE FROM clients WHERE id = ? AND role = "client"');
        $stmt->execute([(int) $_POST['client_id']]);
        $notice = 'Client deleted.';
    }

    if ($action === 'notify') {
        $clientId = ($_POST['client_id'] ?? '') === 'all' ? null : (int) $_POST['client_id'];
        $stmt = $pdo->prepare('INSERT INTO notifications (client_id, title, message) VALUES (?, ?, ?)');
        $stmt->execute([$clientId, trim($_POST['title']), trim($_POST['message'])]);
        $notice = 'Notification sent.';
    }

    if ($action === 'upload_file' && isset($_FILES['admin_file']) && $_FILES['admin_file']['error'] === UPLOAD_ERR_OK) {
        $original = basename($_FILES['admin_file']['name']);
        $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        $safeName = uniqid('admin_' . (int) $_POST['client_id'] . '_', true) . '.' . $ext;
        move_uploaded_file($_FILES['admin_file']['tmp_name'], uploadDir() . DIRECTORY_SEPARATOR . $safeName);

        $stmt = $pdo->prepare('INSERT INTO documents (client_id, uploaded_by, original_name, stored_name, file_type) VALUES (?, "admin", ?, ?, ?)');
        $stmt->execute([(int) $_POST['client_id'], $original, $safeName, $ext]);
        $notice = 'File uploaded for client.';
    }
}

$clients = $pdo->query('SELECT id, name, mobile, email, role, created_at FROM clients ORDER BY created_at DESC')->fetchAll();
$clientOnly = array_values(array_filter($clients, fn ($client) => $client['role'] === 'client'));
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel | Kumaraswamy Tax Consultancy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="dashboard-body">
<nav class="navbar navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="index.php">Admin Panel</a>
        <a class="btn btn-outline-danger" href="logout.php"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
    </div>
</nav>
<main class="container dashboard-wrap">
    <div class="dash-header"><div><span class="eyebrow">Administration</span><h1>Client Management</h1></div></div>
    <?php if ($notice): ?><div class="alert alert-success"><?= e($notice) ?></div><?php endif; ?>
    <div class="row g-4">
        <div class="col-lg-4">
            <section class="dash-card">
                <h2>Add Client</h2>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                    <input type="hidden" name="action" value="add_client">
                    <input class="form-control mb-2" name="name" placeholder="Name" required>
                    <input class="form-control mb-2" name="mobile" placeholder="Mobile" required>
                    <input class="form-control mb-2" type="email" name="email" placeholder="Email" required>
                    <input class="form-control mb-3" type="password" name="password" placeholder="Password" required>
                    <button class="btn btn-primary w-100" type="submit">Add Client</button>
                </form>
            </section>
            <section class="dash-card mt-4">
                <h2>Upload File for Client</h2>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                    <input type="hidden" name="action" value="upload_file">
                    <select class="form-select mb-2" name="client_id" required>
                        <?php foreach ($clientOnly as $client): ?><option value="<?= e((string) $client['id']) ?>"><?= e($client['name']) ?></option><?php endforeach; ?>
                    </select>
                    <input class="form-control mb-3" type="file" name="admin_file" required>
                    <button class="btn btn-primary w-100" type="submit">Upload File</button>
                </form>
            </section>
            <section class="dash-card mt-4">
                <h2>Send Notification</h2>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                    <input type="hidden" name="action" value="notify">
                    <select class="form-select mb-2" name="client_id">
                        <option value="all">All Clients</option>
                        <?php foreach ($clientOnly as $client): ?><option value="<?= e((string) $client['id']) ?>"><?= e($client['name']) ?></option><?php endforeach; ?>
                    </select>
                    <input class="form-control mb-2" name="title" placeholder="Title" required>
                    <textarea class="form-control mb-3" name="message" placeholder="Message" required></textarea>
                    <button class="btn btn-primary w-100" type="submit">Send</button>
                </form>
            </section>
        </div>
        <div class="col-lg-8">
            <section class="dash-card">
                <h2>View All Clients</h2>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Name</th><th>Mobile</th><th>Email</th><th>Role</th><th>Actions</th></tr></thead>
                        <tbody>
                        <?php foreach ($clients as $client): ?>
                            <tr>
                                <form method="post">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                    <input type="hidden" name="client_id" value="<?= e((string) $client['id']) ?>">
                                    <td><input class="form-control form-control-sm" name="name" value="<?= e($client['name']) ?>"></td>
                                    <td><input class="form-control form-control-sm" name="mobile" value="<?= e($client['mobile']) ?>"></td>
                                    <td><input class="form-control form-control-sm" name="email" value="<?= e($client['email']) ?>"></td>
                                    <td><span class="badge text-bg-<?= $client['role'] === 'admin' ? 'primary' : 'secondary' ?>"><?= e($client['role']) ?></span></td>
                                    <td class="text-nowrap">
                                        <?php if ($client['role'] === 'client'): ?>
                                            <button class="btn btn-sm btn-outline-primary" name="action" value="edit_client" type="submit"><i class="bi bi-pencil"></i></button>
                                            <button class="btn btn-sm btn-outline-danger" name="action" value="delete_client" type="submit" onclick="return confirm('Delete this client?')"><i class="bi bi-trash"></i></button>
                                        <?php endif; ?>
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</main>
</body>
</html>
