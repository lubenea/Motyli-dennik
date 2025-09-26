
<?php
if (file_exists('data/app.db')) { echo "DB already exists at data/app.db"; exit; }
@mkdir('data', 0755, true);
if (!file_exists('.env')) { copy('ENV.sample', '.env'); }
$env = parse_ini_file('.env');
$adminUser = $env['ADMIN_USER'] ?? 'admin';
$adminPass = $env['ADMIN_PASS'] ?? 'password';
$pdo = new PDO('sqlite:data/app.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec("CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT UNIQUE, password TEXT);");
$pdo->exec("CREATE TABLE observations (
    id INTEGER PRIMARY KEY,
    date TEXT,
    species_sk TEXT,
    species_lat TEXT,
    count INTEGER,
    stage TEXT,
    location_name TEXT,
    lat REAL,
    lng REAL,
    pluscode TEXT,
    note TEXT,
    observer_name TEXT,
    source_text TEXT,
    habitat TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);");
$pdo->exec("CREATE TABLE IF NOT EXISTS species (
    id INTEGER PRIMARY KEY,
    sk TEXT UNIQUE,
    lat TEXT UNIQUE
);");
$stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
$stmt->execute([$adminUser, password_hash($adminPass, PASSWORD_DEFAULT)]);
echo 'DB created (users, observations, species). Change admin password after first login.';
