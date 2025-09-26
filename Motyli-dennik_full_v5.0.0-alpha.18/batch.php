
<?php
require_once 'db.php';
session_start();
if (!isset($_SESSION['user'])) { header('Location: login.php'); exit; }
if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }
$prefill_date = date('Y-m-d');
?>
<!doctype html>
<html lang="sk">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hromadný zápis — Motýlí denník</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body>
  <header><h1>Hromadný zápis</h1><nav><a href="index.php">Rýchly zápis</a> · <a href="batch.php">Hromadný zápis</a> · <a href="list_observations.php">Zoznam</a> · <a href="logout.php">Logout</a></nav></header>
  <main>
    <form id="batchForm" method="post" action="batch_save.php">
      <input type="hidden" name="csrf" value="<?php echo h($_SESSION['csrf']); ?>">

      <fieldset>
        <legend>Spoločné</legend>
        <div class="grid g3">
          <label>Dátum <input type="date" name="date" required value="<?php echo h($prefill_date); ?>"></label>
          <label>Miesto <input id="loc_name" name="location_name" list="loc_dl" placeholder="Železná studnička"><datalist id="loc_dl"></datalist></label>
        </div>
        <div class="grid g3">
          <label>Lat <input id="lat" name="lat" type="number" step="any" readonly></label>
          <label>Lng <input id="lng" name="lng" type="number" step="any" readonly></label>
          <label><input type="checkbox" name="skip_duplicates" value="1" checked> Preskočiť možné duplicity</label>
        </div>
        <div id="map"></div>
        <div class="grid g3" style="margin-top:6px">
          <label>Pozorovateľ <input name="observer_name" value="LuBenea"></label>
          <label>Zdroj pozorovania <input name="source_text" placeholder="poznámka o zdroji"></label>
          <label>Biotop <input name="habitat" placeholder="lúka, okraj lesa…"></label>
        </div>
      </fieldset>

      <h2 style="margin:6px 0">Druhy a počty</h2>
      <div id="rows" class="grid" style="gap:6px">
      </div>
      <div style="display:flex;gap:8px;margin-top:6px">
        <button type="button" id="addRow">+ Pridať riadok</button>
        <button type="submit">Uložiť všetko</button>
      </div>
    </form>
  </main>
  <footer>Verzia: <?php echo h(appVersion()); ?> · <a href="health.php">health</a> · <a href="diag.php">diag</a></footer>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="assets/batch.js"></script>
</body>
</html>
