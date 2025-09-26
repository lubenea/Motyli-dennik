
<?php
$envFile = __DIR__.'/.env';
$env = file_exists($envFile) ? parse_ini_file($envFile) : parse_ini_file(__DIR__.'/ENV.sample');
if (!is_array($env)) { $env = []; }
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
function appVersion() { global $env; return $env['VERSION'] ?? 'v5.0.0-alpha.18'; }
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
