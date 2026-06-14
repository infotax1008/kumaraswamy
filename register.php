<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($name === '' || $mobile === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        flash('danger', 'Please enter a valid name, mobile, email and a password of at least 8 characters.');
    } else {
        try {
            $stmt = db()->prepare('INSERT INTO clients (name, mobile, email, password_hash, role) VALUES (?, ?, ?, ?, "client")');
            $stmt->execute([$name, $mobile, $email, password_hash($password, PASSWORD_DEFAULT)]);
            flash('success', 'Registration successful. Please login.');
            redirect('login.php');
        } catch (PDOException $ex) {
            flash('danger', 'This email is already registered.');
        }
    }
}

render_header('Register');
?>
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="portal-card bg-white p-4 p-lg-5">
      <h1 class="h3 mb-1">Client Registration</h1>
      <p class="muted-small mb-4">Create your portal account for document uploads and file downloads.</p>
      <form method="post" novalidate>
        <?= csrf_field() ?>
        <div class="mb-3"><label class="form-label" for="name">Name</label><input class="form-control" id="name" name="name" required></div>
        <div class="mb-3"><label class="form-label" for="mobile">Mobile Number</label><input class="form-control" id="mobile" name="mobile" required></div>
        <div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" id="email" name="email" type="email" required></div>
        <div class="mb-4"><label class="form-label" for="password">Password</label><input class="form-control" id="password" name="password" type="password" minlength="8" required></div>
        <button class="btn btn-primary w-100" type="submit">Register</button>
      </form>
      <p class="mt-3 mb-0 muted-small">Already registered? <a href="login.php">Login here</a>.</p>
    </div>
  </div>
</div>
<?php render_footer(); ?>
