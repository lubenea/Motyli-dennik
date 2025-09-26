
<?php
require_once 'db.php';
session_start();
if (!isset($_SESSION['user'])) { http_response_code(403); exit('Forbidden'); }
$pdo = getPdo();
$counts = [
  'users' => $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
  'observations' => $pdo->query('SELECT COUNT(*) FROM observations')->fetchColumn(),
  'species' => $pdo->query('SELECT COUNT(*) FROM species')->fetchColumn()
];
?>
<!doctype html><html lang="sk"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Diagnostika</title><link rel="stylesheet" href="assets/style.css"></head><body>
<header><h1>Diagnostika</h1><nav><a href="index.php">Rýchly zápis</a> · <a href="batch.php">Hromadný zápis</a> · <a href="list_observations.php">Zoznam</a> · <a href="logout.php">Logout</a></nav></header>
<main>
  <h2>Počty</h2>
  <ul class="list-plain">
    <li>Používatelia: <?php echo h($counts['users']); ?></li>
    <li>Pozorovania: <?php echo h($counts['observations']); ?></li>
    <li>Druhy v DB: <?php echo h($counts['species']); ?></li>
  </ul>
  <h2>ENV</h2>
  <pre><?php echo h(json_encode(parse_ini_file(__DIR__.'/.env') ?: [], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)); ?></pre>
</main>
<footer>Verzia: <?php echo h(appVersion()); ?> · <a href="health.php">health</a></footer>
</body></html>
