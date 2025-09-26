
<?php
require_once 'db.php';
session_start();
if (!isset($_SESSION['user'])) { header('Location: login.php'); exit; }
$pdo = getPdo();
$rows = $pdo->query("SELECT DISTINCT location_name, lat, lng FROM observations WHERE location_name IS NOT NULL ORDER BY location_name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html><html lang="sk"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Miesta</title><link rel="stylesheet" href="assets/style.css"></head><body>
<header><h1>Miesta</h1><nav><a href="index.php">Rýchly zápis</a> · <a href="batch.php">Hromadný zápis</a> · <a href="list_observations.php">Zoznam</a> · <a href="logout.php">Logout</a></nav></header>
<main>
  <ul class="list-plain">
    <?php foreach($rows as $r): ?>
      <li><?php echo h($r['location_name']) ?> — <?php echo h($r['lat']) ?>, <?php echo h($r['lng']) ?></li>
    <?php endforeach; ?>
  </ul>
</main>
<footer>Verzia: <?php echo h(appVersion()); ?> · <a href="health.php">health</a> · <a href="diag.php">diag</a></footer>
</body></html>
