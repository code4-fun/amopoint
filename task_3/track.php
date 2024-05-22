<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  http_response_code(204);
  exit;
}

$dsn = 'sqlite:sqlite.db';
$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
  $pdo = new PDO($dsn, null, null, $options);
  $pdo->exec("CREATE TABLE IF NOT EXISTS visits (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ip TEXT,
        city TEXT,
        device TEXT,
        visit_time DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

  $data = json_decode(file_get_contents('php://input'), true);

  if ($data) {
    $stmt = $pdo->prepare("INSERT INTO visits (ip, city, device) VALUES (?, ?, ?)");
    $stmt->execute([$data['ip'], $data['city'], $data['device']]);
  }
} catch (Exception $e) {
  error_log($e->getMessage());
  http_response_code(500);
  exit('Error connecting to the database');
}
