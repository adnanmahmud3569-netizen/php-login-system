<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$stmt = db()->prepare('SELECT name, email, created_at FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) { session_destroy(); header('Location: login.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<main class="card">
  <h1>Welcome, <?= e($user['name']) ?></h1>
  <dl>
    <dt>Email</dt><dd><?= e($user['email']) ?></dd>
    <dt>Member since</dt><dd><?= e($user['created_at']) ?></dd>
  </dl>
  <form method="post" action="logout.php">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <button type="submit">Log out</button>
  </form>
</main>
</body>
</html>
