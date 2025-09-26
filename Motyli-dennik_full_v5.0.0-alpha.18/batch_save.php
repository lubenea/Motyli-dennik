
<?php
require_once 'db.php';
session_start();
if (!isset($_SESSION['user'])) { http_response_code(403); exit('Forbidden'); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Method Not Allowed'); }
if (empty($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'])) { http_response_code(400); exit('CSRF token missing or invalid'); }
$date = $_POST['date'] ?? null;
$location_name = trim($_POST['location_name'] ?? '');
$lat = isset($_POST['lat']) && $_POST['lat'] !== '' ? floatval($_POST['lat']) : null;
$lng = isset($_POST['lng']) && $_POST['lng'] !== '' ? floatval($_POST['lng']) : null;
$observer_name = trim($_POST['observer_name'] ?? 'LuBenea');
$source_text = trim($_POST['source_text'] ?? '');
$habitat = trim($_POST['habitat'] ?? '');
$skipDup = isset($_POST['skip_duplicates']);
$species_sk = $_POST['species_sk'] ?? [];
$species_lat = $_POST['species_lat'] ?? [];
$counts = $_POST['count'] ?? [];
$stages = $_POST['stage'] ?? [];
if (!$date) { http_response_code(400); exit('Missing date'); }
$pdo = getPdo();
$pdo->beginTransaction();
$ins=$pdo->prepare('INSERT INTO observations (date,species_sk,species_lat,count,stage,location_name,lat,lng,observer_name,source_text,habitat) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
$dupQ=$pdo->prepare("SELECT id FROM observations WHERE date=? AND species_sk=? AND stage=? AND ( (location_name != '' AND location_name = ?) OR (? IS NOT NULL AND ? IS NOT NULL AND lat=? AND lng=?) ) LIMIT 1");
$added=0; $skipped=0;
for ($i=0; $i < max(count($species_sk), count($species_lat), count($counts), count($stages)); $i++) {
    $sk = trim($species_sk[$i] ?? '');
    $latn = trim($species_lat[$i] ?? '');
    $cnt = max(0, intval($counts[$i] ?? 0));
    $stage = $stages[$i] ?? 'imago';
    if (!$sk && !$latn) continue;
    if ($cnt <= 0) $cnt = 1;
    // pair names
    if ($sk && !$latn) { $s=$pdo->prepare('SELECT lat FROM species WHERE sk=?'); $s->execute([$sk]); $latn=$s->fetchColumn() ?: $latn; }
    elseif ($latn && !$sk) { $s=$pdo->prepare('SELECT sk FROM species WHERE lat=?'); $s->execute([$latn]); $sk=$s->fetchColumn() ?: $sk; }
    if ($skipDup) { $dupQ->execute([$date,$sk,$stage,$location_name,$lat,$lng,$lat,$lng]); if ($dupQ->fetch()) { $skipped++; continue; } }
    $ins->execute([$date,$sk?:null,$latn?:null,$cnt,$stage,$location_name?:null,$lat,$lng,$observer_name?:null,$source_text?:null,$habitat?:null]);
    // upsert species
    if ($sk || $latn) {
      $row=$pdo->prepare('SELECT id, sk, lat FROM species WHERE sk=? OR lat=? LIMIT 1'); $row->execute([$sk?:'',$latn?:'']);
      $ex=$row->fetch(PDO::FETCH_ASSOC);
      if ($ex){ $upd=$pdo->prepare('UPDATE species SET sk=?, lat=? WHERE id=?'); $upd->execute([$ex['sk']?:$sk, $ex['lat']?:$latn, $ex['id']]); }
      else { $insS=$pdo->prepare('INSERT INTO species (sk, lat) VALUES (?, ?)'); try{$insS->execute([$sk?:null,$latn?:null]);}catch(Exception $e){} }
    }
    $added++;
}
$pdo->commit();
header('Location: list_observations.php?batch=1&added='.$added.'&skipped='.$skipped);
