<?php
require_once __DIR__ . '/config.php';
requireLogin();

$message = '';
$clientId = (int) $_SESSION['client_id'];

$stmt = $pdo->prepare('SELECT id, name, mobile, email, created_at FROM clients WHERE id = ?');
$stmt->execute([$clientId]);
$client = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
    if (!verifyCsrf($_POST['csrf_token'] ?? null)) {
        $message = 'Invalid form token.';
    } elseif ($_FILES['document']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        $original = basename($_FILES['document']['name']);
        $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed, true)) {
            $message = 'Only PDF, image and Word files are allowed.';
        } else {
            $safeName = uniqid('client_' . $clientId . '_', true) . '.' . $ext;
            $target = uploadDir() . DIRECTORY_SEPARATOR . $safeName;
            move_uploaded_file($_FILES['document']['tmp_name'], $target);

            $stmt = $pdo->prepare('INSERT INTO documents (client_id, uploaded_by, original_name, stored_name, file_type) VALUES (?, "client", ?, ?, ?)');
            $stmt->execute([$clientId, $original, $safeName, $ext]);
            $message = 'Document uploaded successfully.';
        }
    }
}

$stmt = $pdo->prepare('SELECT * FROM documents WHERE client_id = ? ORDER BY created_at DESC');
$stmt->execute([$clientId]);
$documents = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT * FROM notifications WHERE client_id = ? OR client_id IS NULL ORDER BY created_at DESC LIMIT 10');
$stmt->execute([$clientId]);
$notifications = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Client Dashboard | Kumaraswamy Tax Consultancy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="dashboard-body">
<nav class="navbar navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="index.php">Kumaraswamy Tax</a>
        <a class="btn btn-outline-danger" href="logout.php"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
    </div>
</nav>
<main class="container dashboard-wrap">
    <div class="dash-header">
        <div>
            <span class="eyebrow">Client Dashboard</span>
            <h1>Welcome, <?= e($client['name'] ?? 'Client') ?></h1>
        </div>
        <a class="btn btn-success" href="https://wa.me/919494990637" target="_blank" rel="noopener"><i class="bi bi-whatsapp me-2"></i>WhatsApp Office</a>
    </div>
    <?php if ($message): ?><div class="alert alert-info"><?= e($message) ?></div><?php endif; ?>
    <div class="row g-4">
        <div class="col-lg-4">
            <section class="dash-card">
                <h2>Profile Details</h2>
                <p><strong>Name:</strong> <?= e($client['name'] ?? '') ?></p>
                <p><strong>Mobile:</strong> <?= e($client['mobile'] ?? '') ?></p>
                <p><strong>Email:</strong> <?= e($client['email'] ?? '') ?></p>
                <p><strong>Joined:</strong> <?= e(date('d M Y', strtotime($client['created_at'] ?? 'now'))) ?></p>
            </section>
            <section class="dash-card mt-4">
                <h2>Notifications</h2>
                <?php if (!$notifications): ?><p>No notifications yet.</p><?php endif; ?>
                <?php foreach ($notifications as $note): ?>
                    <div class="note-item"><strong><?= e($note['title']) ?></strong><p><?= e($note['message']) ?></p></div>
                <?php endforeach; ?>
            </section>
        </div>
        <div class="col-lg-8">
            <section class="dash-card">
                <h2>Upload Documents</h2>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                    <div class="input-group">
                        <input class="form-control" type="file" name="document" required>
                        <button class="btn btn-primary" type="submit"><i class="bi bi-upload me-1"></i>Upload</button>
                    </div>
                </form>
            </section>
            <section class="dash-card mt-4">
                <h2>Download Files</h2>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>File</th><th>Uploaded By</th><th>Date</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach ($documents as $doc): ?>
                            <tr>
                                <td><?= e($doc['original_name']) ?></td>
                                <td><?= e(ucfirst($doc['uploaded_by'])) ?></td>
                                <td><?= e(date('d M Y', strtotime($doc['created_at']))) ?></td>
                                <td><a class="btn btn-sm btn-outline-primary" href="uploads/<?= e($doc['stored_name']) ?>" download><i class="bi bi-download"></i></a></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$documents): ?><tr><td colspan="4">No files uploaded yet.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</main>
</body>
</html>
