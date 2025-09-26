
<?php
require_once 'db.php';
session_start();
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION['user'])) { http_response_code(403); echo json_encode([]); exit; }
$pdo = getPdo();
$rows = $pdo->query("SELECT DISTINCT location_name FROM observations WHERE location_name IS NOT NULL AND location_name != '' ORDER BY location_name")->fetchAll(PDO::FETCH_COLUMN);
echo json_encode($rows ?: [] , JSON_UNESCAPED_UNICODE);
