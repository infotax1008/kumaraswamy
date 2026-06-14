<?php
require_once __DIR__ . '/config.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Invalid form token. Please try again.';
    }

    $name = trim($_POST['name'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($name === '' || $mobile === '' || $email === '' || $password === '') {
        $errors[] = 'All fields are required.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id FROM clients WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $errors[] = 'An account with this email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO clients (name, mobile, email, password_hash, role) VALUES (?, ?, ?, ?, "client")');
            $stmt->execute([$name, $mobile, $email, $hash]);
            $success = 'Registration successful. You can now login.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Client Registration | Kumaraswamy Tax Consultancy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="auth-body">
<div class="auth-shell">
    <div class="auth-card">
        <a class="back-link" href="index.php"><i class="bi bi-arrow-left"></i> Home</a>
        <h1>Create Client Account</h1>
        <p>Register to upload documents and access files shared by the office.</p>
        <?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endforeach; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
            <label class="form-label">Name</label>
            <input class="form-control mb-3" name="name" required>
            <label class="form-label">Mobile Number</label>
            <input class="form-control mb-3" name="mobile" required>
            <label class="form-label">Email</label>
            <input class="form-control mb-3" type="email" name="email" required>
            <label class="form-label">Password</label>
            <input class="form-control mb-4" type="password" name="password" minlength="8" required>
            <button class="btn btn-primary w-100" type="submit">Register</button>
        </form>
        <div class="auth-alt">Already registered? <a href="login.php">Login here</a></div>
    </div>
</div>
</body>
</html>
