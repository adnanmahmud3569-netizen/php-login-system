<?php
require 'config.php';

if (isset($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }

$errors = [];
$email = '';
$notice = isset($_GET['registered']) ? 'Account created. You can log in now.' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!csrf_ok()) {
        $errors[] = 'Session expired. Please try again.';
    } elseif (($_SESSION['lock_until'] ?? 0) > time()) {
        $errors[] = 'Too many attempts. Wait a minute and try again.';
    } else {
        $stmt = db()->prepare('SELECT id, name, password_hash FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            unset($_SESSION['attempts'], $_SESSION['lock_until']);
            header('Location: dashboard.php');
            exit;
        }

        $_SESSION['attempts'] = ($_SESSION['attempts'] ?? 0) + 1;
        if ($_SESSION['attempts'] >= 5) {
            $_SESSION['lock_until'] = time() + 60;
            $_SESSION['attempts'] = 0;
        }
        $errors[] = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Log in</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<main class="card">
  <h1>Log in</h1>
  <?php if ($notice): ?><div class="alert ok"><p><?= e($notice) ?></p></div><?php endif; ?>
  <?php if ($errors): ?>
    <div class="alert error"><?php foreach ($errors as $err): ?><p><?= e($err) ?></p><?php endforeach; ?></div>
  <?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <label>Email<input type="email" name="email" value="<?= e($email) ?>" required></label>
    <label>Password<input type="password" name="password" id="password" required></label>
    <label class="check"><input type="checkbox" id="show"> Show password</label>
    <button type="submit">Log in</button>
  </form>
  <p class="alt">New here? <a href="register.php">Create an account</a></p>
</main>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
$(function () {
  $('#show').on('change', function () {
    $('#password').attr('type', this.checked ? 'text' : 'password');
  });
});
</script>
</body>
</html>
