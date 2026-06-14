<?php
declare(strict_types=1);

const DB_HOST = 'localhost';
const DB_NAME = 'kumaraswamy_tax_portal';
const DB_USER = 'root';
const DB_PASS = '';
const APP_NAME = 'Kumaraswamy Tax Consultancy';
const ADMIN_EMAIL = 'infotax1008@gmail.com';
const REMEMBER_COOKIE = 'ktc_remember';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        exit('Invalid security token. Please go back and try again.');
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function render_flash(): void
{
    foreach ($_SESSION['flash'] ?? [] as $item) {
        echo '<div class="alert alert-' . e($item['type']) . ' alert-dismissible fade show" role="alert">';
        echo e($item['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
    unset($_SESSION['flash']);
}

function current_user(): ?array
{
    if (empty($_SESSION['client_id'])) {
        remember_login();
    }
    if (empty($_SESSION['client_id'])) {
        return null;
    }

    $stmt = db()->prepare('SELECT id, name, mobile, email, role, created_at FROM clients WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['client_id']]);
    return $stmt->fetch() ?: null;
}

function require_login(): array
{
    $user = current_user();
    if (!$user) {
        redirect('login.php');
    }
    return $user;
}

function require_admin(): array
{
    $user = require_login();
    if ($user['role'] !== 'admin') {
        http_response_code(403);
        exit('Access denied.');
    }
    return $user;
}

function remember_login(): void
{
    if (empty($_COOKIE[REMEMBER_COOKIE])) {
        return;
    }

    [$clientId, $token] = array_pad(explode(':', $_COOKIE[REMEMBER_COOKIE], 2), 2, '');
    if (!ctype_digit($clientId) || strlen($token) < 32) {
        return;
    }

    $stmt = db()->prepare('SELECT id, remember_token_hash FROM clients WHERE id = ? LIMIT 1');
    $stmt->execute([(int) $clientId]);
    $client = $stmt->fetch();
    if ($client && hash_equals((string) $client['remember_token_hash'], hash('sha256', $token))) {
        session_regenerate_id(true);
        $_SESSION['client_id'] = (int) $client['id'];
    }
}

function set_remember_cookie(int $clientId): void
{
    $token = bin2hex(random_bytes(32));
    $stmt = db()->prepare('UPDATE clients SET remember_token_hash = ? WHERE id = ?');
    $stmt->execute([hash('sha256', $token), $clientId]);
    setcookie(REMEMBER_COOKIE, $clientId . ':' . $token, [
        'expires' => time() + 60 * 60 * 24 * 30,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
}

function clear_remember_cookie(): void
{
    if (!empty($_SESSION['client_id'])) {
        $stmt = db()->prepare('UPDATE clients SET remember_token_hash = NULL WHERE id = ?');
        $stmt->execute([$_SESSION['client_id']]);
    }
    setcookie(REMEMBER_COOKIE, '', time() - 3600, '/');
}

function generate_captcha(): string
{
    $a = random_int(2, 9);
    $b = random_int(1, 9);
    $_SESSION['captcha_answer'] = (string) ($a + $b);
    return "$a + $b";
}

function verify_captcha(string $answer): bool
{
    return hash_equals($_SESSION['captcha_answer'] ?? '', trim($answer));
}

function save_uploaded_file(int $clientId, string $inputName, string $uploadedBy): void
{
    if (empty($_FILES[$inputName]) || $_FILES[$inputName]['error'] === UPLOAD_ERR_NO_FILE) {
        throw new RuntimeException('Please choose a file to upload.');
    }
    if ($_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed. Please try again.');
    }
    if ($_FILES[$inputName]['size'] > 10 * 1024 * 1024) {
        throw new RuntimeException('File size must be 10 MB or less.');
    }

    $originalName = basename((string) $_FILES[$inputName]['name']);
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx', 'zip'];
    if (!in_array($extension, $allowed, true)) {
        throw new RuntimeException('Allowed files: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX, ZIP.');
    }

    $dir = __DIR__ . '/uploads/client_' . $clientId;
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        throw new RuntimeException('Could not create upload folder.');
    }

    $storedName = bin2hex(random_bytes(16)) . '.' . $extension;
    $target = $dir . '/' . $storedName;
    if (!move_uploaded_file($_FILES[$inputName]['tmp_name'], $target)) {
        throw new RuntimeException('Could not save uploaded file.');
    }

    $stmt = db()->prepare('INSERT INTO client_files (client_id, uploaded_by, file_name, stored_name, file_path, file_size, mime_type) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $clientId,
        $uploadedBy,
        $originalName,
        $storedName,
        'uploads/client_' . $clientId . '/' . $storedName,
        (int) $_FILES[$inputName]['size'],
        (string) ($_FILES[$inputName]['type'] ?? 'application/octet-stream'),
    ]);
}

function handle_file_download(array $user): void
{
    if (empty($_GET['download']) || !ctype_digit((string) $_GET['download'])) {
        return;
    }

    $fileId = (int) $_GET['download'];
    if ($user['role'] === 'admin') {
        $stmt = db()->prepare('SELECT * FROM client_files WHERE id = ? LIMIT 1');
        $stmt->execute([$fileId]);
    } else {
        $stmt = db()->prepare('SELECT * FROM client_files WHERE id = ? AND client_id = ? LIMIT 1');
        $stmt->execute([$fileId, $user['id']]);
    }

    $file = $stmt->fetch();
    $path = $file ? __DIR__ . '/' . $file['file_path'] : '';
    if (!$file || !is_file($path)) {
        http_response_code(404);
        exit('File not found.');
    }

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file['file_name']) . '"');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
}

function render_header(string $title): void
{
    $user = current_user();
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title) ?> | <?= e(APP_NAME) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root { --ktc-blue:#0b3775; --ktc-accent:#1260b3; --ktc-soft:#eef7ff; --ktc-ink:#172033; }
    body { background: linear-gradient(135deg, #eef7ff 0%, #ffffff 44%, #f7fbff 100%); color: var(--ktc-ink); min-height: 100vh; }
    .portal-nav { background: rgba(255,255,255,.94); border-bottom: 1px solid #d9e5f2; backdrop-filter: blur(14px); }
    .brand-badge { width: 44px; height: 44px; display: grid; place-items: center; border-radius: 8px; color: #fff; font-weight: 800; background: linear-gradient(135deg, var(--ktc-blue), var(--ktc-accent)); }
    .portal-card { border: 1px solid #d9e5f2; border-radius: 8px; box-shadow: 0 18px 45px rgba(8,37,77,.10); }
    .btn-primary { background: var(--ktc-accent); border-color: var(--ktc-accent); }
    .btn-primary:hover { background: var(--ktc-blue); border-color: var(--ktc-blue); }
    .hero-strip { background: linear-gradient(135deg, var(--ktc-blue), var(--ktc-accent)); color: #fff; border-radius: 8px; }
    .muted-small { color: #65748b; font-size: .92rem; }
    .table td, .table th { vertical-align: middle; }
    .form-control, .form-select, .btn { border-radius: 8px; }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top portal-nav">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
      <span class="brand-badge">KT</span>
      <span><strong><?= e(APP_NAME) ?></strong><small class="d-block text-muted">Client Portal</small></span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#portalNav" aria-controls="portalNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="portalNav">
      <div class="navbar-nav ms-auto">
        <a class="nav-link" href="index.php">Portal Home</a>
        <?php if ($user): ?>
          <a class="nav-link" href="dashboard.php">Dashboard</a>
          <?php if ($user['role'] === 'admin'): ?><a class="nav-link" href="admin.php">Admin</a><?php endif; ?>
          <a class="nav-link" href="logout.php">Logout</a>
        <?php else: ?>
          <a class="nav-link" href="login.php">Login</a>
          <a class="nav-link" href="register.php">Register</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
<main class="container py-4 py-lg-5">
<?php render_flash(); ?>
    <?php
}

function render_footer(): void
{
    ?>
</main>
<footer class="container pb-4 text-center muted-small">
  &copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. 1-1, Bhavani Nagar, Boduppal, Hyderabad - 500092.
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
    <?php
}
