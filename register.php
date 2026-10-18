<?php
require 'config.php';

if (isset($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }

$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (!csrf_ok()) { $errors[] = 'Session expired. Please try again.'; }
    if (mb_strlen($name) < 2 || mb_strlen($name) > 60) { $errors[] = 'Name must be 2 to 60 characters.'; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Enter a valid email address.'; }
    if (strlen($password) < 8) { $errors[] = 'Password must be at least 8 characters.'; }
    if ($password !== $confirm) { $errors[] = 'Passwords do not match.'; }

    if (!$errors) {
        $stmt = db()->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'This email is already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = db()->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
            $ins->execute([$name, $email, $hash]);
            header('Location: login.php?registered=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Create account</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<main class="card">
  <h1>Create account</h1>
  <?php if ($errors): ?>
    <div class="alert error"><?php foreach ($errors as $err): ?><p><?= e($err) ?></p><?php endforeach; ?></div>
  <?php endif; ?>
  <form method="post" id="regForm" novalidate>
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <label>Name<input type="text" name="name" value="<?= e($name) ?>" required></label>
    <label>Email<input type="email" name="email" value="<?= e($email) ?>" required></label>
    <label>Password<input type="password" name="password" id="password" minlength="8" required></label>
    <label>Confirm password<input type="password" name="confirm" id="confirm" required></label>
    <p class="hint" id="hint"></p>
    <label class="check"><input type="checkbox" id="show"> Show passwords</label>
    <button type="submit">Create account</button>
  </form>
  <p class="alt">Already have an account? <a href="login.php">Log in</a></p>
</main>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
$(function () {
  $('#show').on('change', function () {
    $('#password, #confirm').attr('type', this.checked ? 'text' : 'password');
  });
  $('#password, #confirm').on('input', function () {
    var p = $('#password').val(), c = $('#confirm').val(), msg = '';
    if (p.length > 0 && p.length < 8) { msg = 'Use at least 8 characters.'; }
    else if (c.length > 0 && p !== c) { msg = 'Passwords do not match yet.'; }
    $('#hint').text(msg);
  });
});
</script>
</body>
</html>
