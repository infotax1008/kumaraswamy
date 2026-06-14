<?php
require_once __DIR__ . '/config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['captcha_question'])) {
    createCaptcha();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Invalid form token. Please try again.';
    }
    if (!validateCaptcha($_POST['captcha'] ?? null)) {
        $errors[] = 'CAPTCHA answer is incorrect.';
    }

    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT * FROM clients WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $client = $stmt->fetch();

        if ($client && password_verify($password, $client['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['client_id'] = (int) $client['id'];
            $_SESSION['client_name'] = $client['name'];
            $_SESSION['role'] = $client['role'];

            if (!empty($_POST['remember'])) {
                setcookie('remember_email', $email, time() + 60 * 60 * 24 * 30, '', '', false, true);
            } else {
                setcookie('remember_email', '', time() - 3600, '', '', false, true);
            }

            header('Location: ' . ($client['role'] === 'admin' ? 'admin.php' : 'dashboard.php'));
            exit;
        }

        $errors[] = 'Invalid email or password.';
    }

    createCaptcha();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Client Login | Kumaraswamy Tax Consultancy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="auth-body">
<div class="auth-shell">
    <div class="auth-card">
        <a class="back-link" href="index.php"><i class="bi bi-arrow-left"></i> Home</a>
        <h1>Client Login</h1>
        <p>Access your dashboard, documents and consultancy updates.</p>
        <?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endforeach; ?>
        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
            <label class="form-label">Email</label>
            <input class="form-control mb-3" type="email" name="email" value="<?= e($_COOKIE['remember_email'] ?? '') ?>" required>
            <label class="form-label">Password</label>
            <input class="form-control mb-3" type="password" name="password" required>
            <label class="form-label">CAPTCHA: What is <?= e($_SESSION['captcha_question'] ?? '') ?>?</label>
            <input class="form-control mb-3" name="captcha" inputmode="numeric" required>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <label class="form-check-label"><input class="form-check-input me-2" type="checkbox" name="remember" <?= isset($_COOKIE['remember_email']) ? 'checked' : '' ?>>Remember me</label>
                <a href="mailto:infotax1008@gmail.com?subject=Forgot%20Password%20Request">Forgot Password?</a>
            </div>
            <button class="btn btn-primary w-100" type="submit">Login</button>
        </form>
        <div class="auth-alt">New client? <a href="register.php">Create account</a></div>
    </div>
</div>
</body>
</html>
