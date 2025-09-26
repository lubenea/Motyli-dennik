
<?php
require_once 'db.php';
session_start();
if (!isset($_SESSION['user'])) { header('Location: login.php'); exit; }
if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }
$prefill_date = $_GET['date'] ?? date('Y-m-d');
$prefill_stage = $_GET['stage'] ?? 'imago';
$saved = isset($_GET['saved']);
?>
<!doctype html>
<html lang="sk">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Motýlí denník — Rýchly zápis</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body>
  <header><h1>Motýlí denník</h1><nav><a href="index.php">Rýchly zápis</a> · <a href="batch.php">Hromadný zápis</a> · <a href="list_observations.php">Zoznam</a> · <a href="logout.php">Logout</a></nav></header>
  <main>
    <section id="add">
      <h2 style="margin:4px 0">Rýchly zápis</h2>
      <?php if ($saved): ?><div class="banner">Uložené ✅ — pokračuj v zadávaní.</div><?php endif; ?>
      <form id="obsForm" method="post" action="add_observation.php" novalidate>
        <input type="hidden" name="csrf" value="<?php echo h($_SESSION['csrf']); ?>">

        <fieldset>
          <legend>Základ</legend>
          <div class="grid g4">
            <label>Dátum
              <input type="date" name="date" required value="<?php echo h($prefill_date); ?>">
            </label>
            <label>Druh (SK)
              <input id="species_sk" name="species_sk" list="species_sk_dl" placeholder="Babočka pávooká">
              <datalist id="species_sk_dl"></datalist>
            </label>
            <label>Druh (LAT)
              <input id="species_lat" name="species_lat" list="species_lat_dl" placeholder="Aglais io">
              <datalist id="species_lat_dl"></datalist>
            </label>
            <label>Počet
              <input type="number" name="count" min="1" step="1" value="1" required>
            </label>
          </div>
          <div style="margin-top:4px">
            <?php
              $stages = ['imago'=>'🦋 imago','pupa'=>'🟫 pupa','larva'=>'🟩 larva','egg'=>'⚪ egg'];
              foreach ($stages as $val=>$label) {
                $active = $prefill_stage === $val ? 'active' : '';
                echo '<label class="pill '.$active.'"><input type="radio" name="stage" value="'.$val.'" '.($prefill_stage===$val?'checked':'').'>'.$label.'</label>';
              }
            ?>
          </div>
        </fieldset>

        <fieldset>
          <legend>Miesto</legend>
          <div id="map"></div>
          <div class="grid g3" style="margin-top:6px">
            <label>Miesto (názov)
              <input id="loc_name" name="location_name" list="loc_dl" placeholder="Železná studnička">
              <datalist id="loc_dl"></datalist>
            </label>
            <label>Lat
              <input id="lat" name="lat" type="number" step="any" readonly>
            </label>
            <label>Lng
              <input id="lng" name="lng" type="number" step="any" readonly>
            </label>
          </div>
        </fieldset>

        <fieldset class="grid g3">
          <legend>Meta</legend>
          <label>Pozorovateľ
            <input name="observer_name" value="LuBenea">
          </label>
          <label>Zdroj pozorovania
            <input name="source_text" placeholder="napr. iNaturalist #12345, email od Janka…">
          </label>
          <label>Biotop
            <input name="habitat" placeholder="lúka, okraj lesa…">
          </label>
        </fieldset>

        <input type="hidden" id="dup_ok" name="dup_ok" value="0">
        <div style="display:flex;gap:8px;align-items:center">
          <button type="submit" name="next" value="1" title="Ctrl+Enter">Uložiť a ďalšie</button>
          <button type="submit">Uložiť</button>
          <a class="buttonlike" href="list_observations.php">Zoznam</a>
        </div>
        <p class="muted">Tip: Ctrl/Cmd+Enter = uložiť a pridať ďalšie. Kliknutím do mapy sa vyplní Lat/Lng. SK/LAT sa dopĺňajú z databázy.</p>
      </form>
    </section>
  </main>
  <footer>Verzia: <?php echo h(appVersion()); ?> · <a href="health.php">health</a> · <a href="diag.php">diag</a></footer>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="assets/app.js"></script>
</body>
</html>
