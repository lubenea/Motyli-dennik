
<?php
require_once 'db.php';
session_start();
if (!isset($_SESSION['user'])) { header('Location: login.php'); exit; }
$pdo = getPdo();
$rows = $pdo->query('SELECT * FROM observations ORDER BY date DESC, id DESC')->fetchAll(PDO::FETCH_ASSOC);
$banner = '';
if (isset($_GET['batch'])) { $banner = "Hromadný import: pridané ".intval($_GET['added']??0).", preskočené ".intval($_GET['skipped']??0)."."; }
?>
<!doctype html><html lang="sk"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Zoznam pozorovaní</title><link rel="stylesheet" href="assets/style.css"></head><body>
<header><h1>Zoznam pozorovaní</h1><nav><a href="index.php">Rýchly zápis</a> · <a href="batch.php">Hromadný zápis</a> · <a href="locations.php">Miesta</a> · <a href="logout.php">Logout</a></nav></header>
<main>
  <?php if ($banner): ?><div class="banner"><?php echo h($banner); ?></div><?php endif; ?>
  <table>
    <thead>
      <tr><th>ID</th><th>Dátum</th><th>Druh (SK)</th><th>Druh (LAT)</th><th>Počet</th><th>Stádium</th><th>Miesto</th><th>Lat</th><th>Lng</th><th>Pozorovateľ</th><th>Zdroj</th><th>Biotop</th></tr>
    </thead>
    <tbody>
      <?php foreach($rows as $r): ?>
        <tr>
          <td><?php echo h($r['id']); ?></td>
          <td><?php echo h($r['date']); ?></td>
          <td><?php echo h($r['species_sk']); ?></td>
          <td><?php echo h($r['species_lat']); ?></td>
          <td><?php echo h($r['count']); ?></td>
          <td><?php echo h($r['stage']); ?></td>
          <td><?php echo h($r['location_name']); ?></td>
          <td><?php echo h($r['lat']); ?></td>
          <td><?php echo h($r['lng']); ?></td>
          <td><?php echo h($r['observer_name']); ?></td>
          <td><?php echo h($r['source_text']); ?></td>
          <td><?php echo h($r['habitat']); ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</main>
<footer>Verzia: <?php echo h(appVersion()); ?> · <a href="health.php">health</a> · <a href="diag.php">diag</a></footer>
</body></html>
