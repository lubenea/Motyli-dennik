
<?php
require_once 'db.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    $pdo = getPdo();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$u]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($p, $user['password'])) {
        $_SESSION['user'] = $user['username'];
        header('Location: index.php'); exit;
    } else { $err = 'Nesprávne meno alebo heslo'; }
}
?>
<!doctype html><html lang="sk"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login — Motýlí denník</title><link rel="stylesheet" href="assets/style.css"></head><body>
<main class="login">
  <h1>Motýlí denník</h1>
  <?php if (!empty($err)): ?><div class="banner" style="background:#ffd1d1"><?php echo h($err) ?></div><?php endif; ?>
  <form method="post">
    <label>Používateľ <input name="username" autofocus></label>
    <label>Heslo <input type="password" name="password"></label>
    <button type="submit">Prihlásiť</button>
  </form>
</main>
<footer>Verzia: <?php echo h(appVersion()); ?> · <a href="health.php">health</a></footer>
</body></html>
