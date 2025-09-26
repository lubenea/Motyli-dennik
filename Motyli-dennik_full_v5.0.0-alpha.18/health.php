
<?php
header('Content-Type: application/json; charset=utf-8');
$ok = true;
$info = ['php' => PHP_VERSION, 'pdo_sqlite' => extension_loaded('pdo_sqlite')];
if (!$info['pdo_sqlite']) $ok = false;
$dbExists = file_exists(__DIR__ . '/data/app.db');
$info['db_exists'] = $dbExists;
http_response_code($ok ? 200 : 500);
echo json_encode(['status'=>$ok?'ok':'fail','info'=>$info]);
