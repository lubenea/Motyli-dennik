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
        header('Location: index.php');
        exit;
    } else {
        $err = 'Invalid';
    }
}
?>
<form method="post">
  <label>username: <input name="username"></label><br>
  <label>password: <input type="password" name="password"></label><br>
  <button type="submit">Login</button>
</form>
