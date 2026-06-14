<?php
declare(strict_types=1);

session_start();

define('APP_NAME', 'Kumaraswamy Tax Consultancy');
define('APP_URL', 'http://localhost/kumaraswamy-tax-consultancy');
define('ADMIN_EMAIL', 'admin@kumaraswamytax.local');
define('DEFAULT_ADMIN_PASSWORD', 'Admin@12345');
define('STARTER_ADMIN_HASH', '$2y$10$sKlm/lvwwsS2RXbz9x6f.uWk0jvrlwR.6nmlZE1Er61KJ7YMfT1cq');

$dbHost = 'localhost';
$dbName = 'kumaraswamy_tax';
$dbUser = 'root';
$dbPass = '';

try {
    $pdo = new PDO(
        "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    exit('Database connection failed. Please check config.php settings.');
}

try {
    $stmt = $pdo->prepare('SELECT id, password_hash FROM clients WHERE email = ? AND role = "admin" LIMIT 1');
    $stmt->execute([ADMIN_EMAIL]);
    $admin = $stmt->fetch();

    if (!$admin) {
        $stmt = $pdo->prepare('INSERT INTO clients (name, mobile, email, password_hash, role) VALUES (?, ?, ?, ?, "admin")');
        $stmt->execute(['Kumaraswamy Admin', '+91 9494990637', ADMIN_EMAIL, password_hash(DEFAULT_ADMIN_PASSWORD, PASSWORD_DEFAULT)]);
    } elseif ($admin['password_hash'] === STARTER_ADMIN_HASH) {
        $stmt = $pdo->prepare('UPDATE clients SET password_hash = ? WHERE id = ?');
        $stmt->execute([password_hash(DEFAULT_ADMIN_PASSWORD, PASSWORD_DEFAULT), (int) $admin['id']]);
    }
} catch (PDOException $e) {
    // Tables may not exist before database.sql is imported.
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function isLoggedIn(): bool
{
    return isset($_SESSION['client_id']);
}

function isAdmin(): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function requireAdmin(): void
{
    requireLogin();
    if (!isAdmin()) {
        header('Location: dashboard.php');
        exit;
    }
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verifyCsrf(?string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string) $token);
}

function createCaptcha(): int
{
    $a = random_int(2, 9);
    $b = random_int(2, 9);
    $_SESSION['captcha_answer'] = $a + $b;
    $_SESSION['captcha_question'] = "{$a} + {$b}";

    return $_SESSION['captcha_answer'];
}

function validateCaptcha(?string $answer): bool
{
    return isset($_SESSION['captcha_answer']) && (int) $answer === (int) $_SESSION['captcha_answer'];
}

function uploadDir(): string
{
    $dir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    return $dir;
}
?>
