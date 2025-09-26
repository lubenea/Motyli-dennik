
<?php
// migrate_alpha18.php — run once, then delete
header('Content-Type: text/plain; charset=utf-8');
$db = __DIR__ . '/data/app.db';
if (!file_exists($db)) { http_response_code(500); echo "DB not found at data/app.db\n"; exit; }
$backup = __DIR__ . '/data/app.backup-' . date('Ymd-His') . '.sqlite';
copy($db, $backup);
echo "Backup: " . basename($backup) . "\n";
$pdo = new PDO('sqlite:' . $db);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Ensure key columns exist (idempotent adds)
$cols = array_column($pdo->query('PRAGMA table_info(observations)')->fetchAll(PDO::FETCH_ASSOC), 'name');
$toAdd = [
  'species_lat TEXT',
  'observer_name TEXT',
  'source_text TEXT',
  'habitat TEXT'
];
foreach ($toAdd as $def) {
  $name = trim(explode(' ', $def)[0]);
  if (!in_array($name, $cols, true)) {
    $pdo->exec('ALTER TABLE observations ADD COLUMN ' . $def);
    echo "Added column: $name\n";
  } else {
    echo "Exists: $name\n";
  }
}
// Ensure species table exists
$pdo->exec('CREATE TABLE IF NOT EXISTS species (id INTEGER PRIMARY KEY, sk TEXT UNIQUE, lat TEXT UNIQUE)');
echo "Ensured species table\n";
echo "Migration alpha.18 done.\n";
