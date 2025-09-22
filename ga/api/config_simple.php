<?php
// Simplified config for debugging 500 errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simple session start without fancy settings
session_start();

header('Content-Type: application/json; charset=utf-8');

// Database credentials
$DB_HOST = '139.59.124.208';
$DB_NAME = 'acqmgdrqzp';
$DB_USER = 'acqmgdrqzp';
$DB_PASS = 'nC3gUCnutG';

try {
  $pdo = new PDO(
    "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
    $DB_USER,
    $DB_PASS,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
  );
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false, 'error'=>'DB connection failed: ' . $e->getMessage()]);
  exit;
}

function json_ok($data = [], int $code = 200): void {
  http_response_code($code);
  echo json_encode(array_merge(['ok'=>true], $data));
  exit;
}

function json_err(string $msg, int $code = 400): void {
  http_response_code($code);
  echo json_encode(['ok'=>false, 'error'=>$msg]);
  exit;
}

function require_auth(): array {
  if (!isset($_SESSION['uid']) && !isset($_SESSION['user_id'])) {
    json_err('Not authenticated', 401);
  }
  
  $user_id = $_SESSION['user_id'] ?? $_SESSION['uid'] ?? 0;
  return [
    'id' => $user_id,
    'email' => $_SESSION['email'] ?? '',
    'name' => $_SESSION['name'] ?? '',
    'role' => $_SESSION['role'] ?? '',
  ];
}
?>