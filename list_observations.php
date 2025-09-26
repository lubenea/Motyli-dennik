<?php
require_once 'db.php';
session_start();
if (!isset($_SESSION['user'])) { header('Location: login.php'); exit; }
$pdo = getPdo();
$rows = $pdo->query('SELECT * FROM observations ORDER BY date DESC, id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Zoznam</title><link rel="stylesheet" href="assets/style.css"></head><body>
<header><a href="index.php">Dashboard</a> · <a href="logout.php">Logout</a></header>
<h1>Zoznam pozorovaní</h1>
<table>
<thead><tr><th>ID</th><th>Dátum</th><th>Druh</th><th>Počet</th><th>Stádium</th><th>Miesto</th><th>Poznámka</th></tr></thead>
<tbody>
<?php foreach($rows as $r): ?>
  <tr>
    <td><?php echo $r['id'] ?></td>
    <td><?php echo htmlspecialchars($r['date']) ?></td>
    <td><?php echo htmlspecialchars($r['species_sk']) ?></td>
    <td><?php echo htmlspecialchars($r['count']) ?></td>
    <td><?php echo htmlspecialchars($r['stage']) ?></td>
    <td><?php echo htmlspecialchars($r['location_name']) ?></td>
    <td><?php echo htmlspecialchars($r['note']) ?></td>
  </tr>
<?php endforeach; ?>
</tbody>
</table>
</body></html>
