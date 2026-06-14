<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $mode = $_POST['mode'] ?? 'login';

    if ($mode === 'forgot') {
        $email = strtolower(trim($_POST['forgot_email'] ?? ''));
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $token = bin2hex(random_bytes(24));
            $stmt = db()->prepare('UPDATE clients SET reset_token_hash = ?, reset_token_expires_at = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?');
            $stmt->execute([hash('sha256', $token), $email]);
        }
        flash('info', 'If this email exists, a reset request has been recorded. Please contact the office to complete password reset.');
        redirect('login.php');
    }

    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $captcha = (string) ($_POST['captcha'] ?? '');

    if (!verify_captcha($captcha)) {
        flash('danger', 'CAPTCHA answer is incorrect.');
        redirect('login.php');
    }

    $stmt = db()->prepare('SELECT * FROM clients WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $client = $stmt->fetch();

    if (!$client || !password_verify($password, $client['password_hash'])) {
        flash('danger', 'Invalid email or password.');
        redirect('login.php');
    }

    session_regenerate_id(true);
    $_SESSION['client_id'] = (int) $client['id'];
    if (!empty($_POST['remember_me'])) {
        set_remember_cookie((int) $client['id']);
    }

    redirect($client['role'] === 'admin' ? 'admin.php' : 'dashboard.php');
}

$captchaQuestion = generate_captcha();
render_header('Login');
?>
<div class="row g-4 justify-content-center">
  <div class="col-lg-6">
    <div class="portal-card bg-white p-4 p-lg-5">
      <h1 class="h3 mb-1">Client Login</h1>
      <p class="muted-small mb-4">Login using your email and password.</p>
      <form method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="mode" value="login">
        <div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" id="email" name="email" type="email" required></div>
        <div class="mb-3"><label class="form-label" for="password">Password</label><input class="form-control" id="password" name="password" type="password" required></div>
        <div class="mb-3"><label class="form-label" for="captcha">CAPTCHA: What is <?= e($captchaQuestion) ?>?</label><input class="form-control" id="captcha" name="captcha" inputmode="numeric" required></div>
        <div class="form-check mb-4"><input class="form-check-input" id="remember_me" name="remember_me" type="checkbox" value="1"><label class="form-check-label" for="remember_me">Remember Me</label></div>
        <button class="btn btn-primary w-100" type="submit">Login</button>
      </form>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="portal-card bg-white p-4 p-lg-5">
      <h2 class="h4">Forgot Password</h2>
      <p class="muted-small">Submit your registered email. The office can verify and reset your password securely.</p>
      <form method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="mode" value="forgot">
        <div class="mb-3"><label class="form-label" for="forgot_email">Registered Email</label><input class="form-control" id="forgot_email" name="forgot_email" type="email" required></div>
        <button class="btn btn-outline-primary w-100" type="submit">Request Reset</button>
      </form>
      <p class="mt-3 mb-0 muted-small">New client? <a href="register.php">Register here</a>.</p>
    </div>
  </div>
</div>
<?php render_footer(); ?>
