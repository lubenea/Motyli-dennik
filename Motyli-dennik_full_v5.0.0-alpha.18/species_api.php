
<?php
require_once 'db.php';
session_start();
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION['user'])) { http_response_code(403); echo json_encode([]); exit; }
$pdo = getPdo();
$q = trim($_GET['q'] ?? '');
$sk = trim($_GET['sk'] ?? '');
$lat = trim($_GET['lat'] ?? '');
if ($sk !== '') { $stmt=$pdo->prepare('SELECT sk, lat FROM species WHERE sk = ? LIMIT 1'); $stmt->execute([$sk]); echo json_encode($stmt->fetch(PDO::FETCH_ASSOC) ?: [], JSON_UNESCAPED_UNICODE); exit; }
if ($lat !== '') { $stmt=$pdo->prepare('SELECT sk, lat FROM species WHERE lat = ? LIMIT 1'); $stmt->execute([$lat]); echo json_encode($stmt->fetch(PDO::FETCH_ASSOC) ?: [], JSON_UNESCAPED_UNICODE); exit; }
if ($q === '') { $rows=$pdo->query('SELECT sk, lat FROM species ORDER BY sk LIMIT 100')->fetchAll(PDO::FETCH_ASSOC); echo json_encode($rows ?: [], JSON_UNESCAPED_UNICODE); exit; }
$qLike = '%'.mb_strtolower($q, 'UTF-8').'%';
$stmt=$pdo->prepare('SELECT sk, lat FROM species WHERE LOWER(sk) LIKE ? OR LOWER(lat) LIKE ? ORDER BY sk LIMIT 50'); $stmt->execute([$qLike,$qLike]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [], JSON_UNESCAPED_UNICODE);
