<?php
require_once 'db.php';
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
// Simple dashboard: add observation form and link to list
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Motýlí denník — dennik.lubenea.sk</title>
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body>
<header><h1>Motýlí denník</h1><a href="logout.php">Logout</a></header>
<main>
<section id="add">
  <h2>Pridať pozorovanie</h2>
  <form id="obsForm" method="post" action="add_observation.php">
    <label>Dátum: <input type="date" name="date" required value="<?php echo date('Y-m-d'); ?>"></label><br>
    <label>Druh (SK): <input id="species" name="species_sk" required></label><br>
    <label>Počet: <input type="number" name="count" min="1" value="1"></label><br>
    <label>Stádium:
      <select name="stage">
        <option value="imago">imago</option>
        <option value="pupa">pupa</option>
        <option value="larva">larva</option>
        <option value="egg">egg</option>
      </select>
    </label><br>
    <label>Miesto (názov): <input id="loc_name" name="location_name"></label><br>
    <div id="map" style="height:300px"></div>
    <input type="hidden" id="lat" name="lat">
    <input type="hidden" id="lng" name="lng">
    <input type="hidden" id="pluscode" name="pluscode">
    <label>Poznámka:<br><textarea name="note"></textarea></label><br>
    <button type="submit">Uložiť</button>
  </form>
</section>
<p><a href="list_observations.php">Zoznam pozorovaní</a> · <a href="locations.php">Miesta</a></p>
</main>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="assets/app.js"></script>
</body>
</html>
