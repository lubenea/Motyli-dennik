<?php
require_once 'db.php';
session_start();
if (!isset($_SESSION['user'])) { http_response_code(403); exit; }
$data = [
    'date'=>$_POST['date'] ?? null,
    'species_sk'=>$_POST['species_sk'] ?? null,
    'count'=>$_POST['count'] ? intval($_POST['count']) : 1,
    'stage'=>$_POST['stage'] ?? null,
    'location_name'=>$_POST['location_name'] ?? null,
    'lat'=>$_POST['lat'] ? floatval($_POST['lat']) : null,
    'lng'=>$_POST['lng'] ? floatval($_POST['lng']) : null,
    'pluscode'=>$_POST['pluscode'] ?? null,
    'note'=>$_POST['note'] ?? null
];
$pdo = getPdo();
$stmt = $pdo->prepare('INSERT INTO observations (date,species_sk,count,stage,location_name,lat,lng,pluscode,note) VALUES (?,?,?,?,?,?,?,?,?)');
$stmt->execute([$data['date'],$data['species_sk'],$data['count'],$data['stage'],$data['location_name'],$data['lat'],$data['lng'],$data['pluscode'],$data['note']]);
header('Location: list_observations.php');
