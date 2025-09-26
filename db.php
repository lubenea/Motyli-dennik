<?php
// Simple PDO SQLite helper
$env = parse_ini_file(__DIR__.'/.env') ?: [];
$dbPath = $env['DB_PATH'] ?? 'data/app.db';
function getPdo() {
    global $dbPath;
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO('sqlite:'.$dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return $pdo;
}
