<?php
require_once 'db.php';
session_start();
if (!isset($_SESSION['user'])) { header('Location: login.php'); exit; }
// Simple locations view: dedupe from observations
$pdo = getPdo();
$rows = $pdo->query("SELECT DISTINCT location_name, lat, lng FROM observations WHERE location_name IS NOT NULL ORDER BY location_name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html><html><head><meta charset="utf-8"><title>Miesta</title><link rel="stylesheet" href="assets/style.css"></head><body>
<header><a href="index.php">Dashboard</a></header>
<h1>Miesta (z pozorovaní)</h1>
<ul>
<?php foreach($rows as $r): ?>
  <li><?php echo htmlspecialchars($r['location_name']) ?> — <?php echo $r['lat'] ?>, <?php echo $r['lng'] ?></li>
<?php endforeach; ?>
</ul>
</body></html>
