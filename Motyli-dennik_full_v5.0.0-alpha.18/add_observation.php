
<?php
require_once 'db.php';
session_start();
if (!isset($_SESSION['user'])) { http_response_code(403); exit('Forbidden'); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Method Not Allowed'); }
if (empty($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'])) { http_response_code(400); exit('CSRF token missing or invalid'); }
$date = $_POST['date'] ?? null;
$species_sk = trim($_POST['species_sk'] ?? '');
$species_lat = trim($_POST['species_lat'] ?? '');
$count = max(1, intval($_POST['count'] ?? 1));
$stage = $_POST['stage'] ?? 'imago';
if (!in_array($stage, ['imago','pupa','larva','egg'], true)) $stage = 'imago';
$location_name = trim($_POST['location_name'] ?? '');
$lat = isset($_POST['lat']) && $_POST['lat'] !== '' ? floatval($_POST['lat']) : null;
$lng = isset($_POST['lng']) && $_POST['lng'] !== '' ? floatval($_POST['lng']) : null;
$observer_name = trim($_POST['observer_name'] ?? 'LuBenea');
$source_text = trim($_POST['source_text'] ?? '');
$habitat = trim($_POST['habitat'] ?? '');
$dup_ok = ($_POST['dup_ok'] ?? '0') === '1';
$next = isset($_POST['next']);
if (!$date || (!$species_sk && !$species_lat)) { http_response_code(400); exit('Missing required fields'); }
$pdo = getPdo();
// Pair SK/LAT if one missing
if ($species_sk && !$species_lat) { $s=$pdo->prepare('SELECT lat FROM species WHERE sk=?'); $s->execute([$species_sk]); $species_lat = $s->fetchColumn() ?: $species_lat; }
elseif ($species_lat && !$species_sk) { $s=$pdo->prepare('SELECT sk FROM species WHERE lat=?'); $s->execute([$species_lat]); $species_sk = $s->fetchColumn() ?: $species_sk; }
// duplicate guard by SK
if (!$dup_ok && $species_sk) {
  $stmt=$pdo->prepare("SELECT id FROM observations WHERE date=? AND species_sk=? AND stage=? AND ( (location_name != '' AND location_name = ?) OR (? IS NOT NULL AND ? IS NOT NULL AND lat=? AND lng=?) ) LIMIT 1");
  $stmt->execute([$date,$species_sk,$stage,$location_name,$lat,$lng,$lat,$lng]);
  if ($stmt->fetch()) { header('Location: index.php?duplicate=1&date=' . urlencode($date) . '&stage=' . urlencode($stage)); exit; }
}
$stmt=$pdo->prepare('INSERT INTO observations (date,species_sk,species_lat,count,stage,location_name,lat,lng,observer_name,source_text,habitat) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
$stmt->execute([$date,$species_sk?:null,$species_lat?:null,$count,$stage,$location_name?:null,$lat,$lng,$observer_name?:null,$source_text?:null,$habitat?:null]);
// upsert species
if ($species_sk || $species_lat) {
  $row=$pdo->prepare('SELECT id, sk, lat FROM species WHERE sk = ? OR lat = ? LIMIT 1'); $row->execute([$species_sk?:'',$species_lat?:'']);
  $ex=$row->fetch(PDO::FETCH_ASSOC);
  if ($ex){ $upd=$pdo->prepare('UPDATE species SET sk=?, lat=? WHERE id=?'); $upd->execute([$ex['sk']?:$species_sk, $ex['lat']?:$species_lat, $ex['id']]); }
  else { $insS=$pdo->prepare('INSERT INTO species (sk, lat) VALUES (?, ?)'); try{$insS->execute([$species_sk?:null,$species_lat?:null]);}catch(Exception $e){} }
}
if ($next) { header('Location: index.php?saved=1&date=' . urlencode($date) . '&stage=' . urlencode($stage)); }
else { header('Location: list_observations.php'); }
