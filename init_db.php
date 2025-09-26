<?php
// Run once to create DB
if (file_exists('data/app.db')) {
    echo "DB already exists at data/app.db";
    exit;
}
@mkdir('data', 0755, true);
copy('ENV.sample', '.env');
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
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);");
$stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
// default admin / password — change after first login
$stmt->execute(['admin', password_hash('password', PASSWORD_DEFAULT)]);
echo 'DB created and default admin user added. Please change password after first login.';
