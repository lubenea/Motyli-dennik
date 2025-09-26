
<?php
require_once 'db.php';
session_start();
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION['user'])) { http_response_code(403); echo json_encode(['error'=>'forbidden']); exit; }
$date = $_POST['date'] ?? null;
$species_sk = trim($_POST['species_sk'] ?? '');
$stage = $_POST['stage'] ?? '';
$location_name = trim($_POST['location_name'] ?? '');
$lat = isset($_POST['lat']) && $_POST['lat'] !== '' ? floatval($_POST['lat']) : null;
$lng = isset($_POST['lng']) && $_POST['lng'] !== '' ? floatval($_POST['lng']) : null;
if (!$date || !$species_sk || !$stage) { echo json_encode(['duplicate'=>false,'ok'=>true]); exit; }
$pdo = getPdo();
$sql = "SELECT id FROM observations WHERE date = ? AND species_sk = ? AND stage = ? AND ( (location_name != '' AND location_name = ?) OR (? IS NOT NULL AND ? IS NOT NULL AND lat = ? AND lng = ?) ) LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$date, $species_sk, $stage, $location_name, $lat, $lng, $lat, $lng]);
$dup = $stmt->fetch() ? true : false;
echo json_encode(['duplicate'=>$dup,'ok'=>false]);
