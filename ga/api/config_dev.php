<?php
// Development configuration for local testing
// This file provides a secure way to test the application locally

// Set development environment
ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL);
ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');

// Start session
session_name('GA_WINNERSESS_DEV');
session_start();

header('Content-Type: application/json; charset=utf-8');

// Development database configuration (localhost)
// Replace these with your local database credentials
$DB_HOST = 'localhost';
$DB_NAME = 'goalive_winners_dev';
$DB_USER = 'root';
$DB_PASS = '';

// For MAMP/XAMPP users:
// $DB_HOST = 'localhost:3306'; // or '127.0.0.1:3306'
// $DB_NAME = 'goalive_winners_dev';
// $DB_USER = 'root';
// $DB_PASS = 'root'; // MAMP default password

try {
  $pdo = new PDO(
    "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
    $DB_USER,
    $DB_PASS,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
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

function json_err(string $msg, int $code = 400, $extra = null): void {
  http_response_code($code);
  $payload = ['ok'=>false, 'error'=>$msg];
  if ($extra !== null) $payload['extra'] = $extra;
  echo json_encode($payload);
  exit;
}

function require_auth(array $roles = []): array {
  if (!isset($_SESSION['user_id'])) {
    json_err('Not authenticated', 401);
  }
  
  $stmt = $GLOBALS['pdo']->prepare('SELECT id, name, email, role FROM users WHERE id = ? AND active = 1');
  $stmt->execute([$_SESSION['user_id']]);
  $user = $stmt->fetch();
  
  if (!$user) {
    json_err('User not found or inactive', 401);
  }
  
  if (!empty($roles) && !in_array($user['role'], $roles)) {
    json_err('Access denied', 403);
  }
  
  return $user;
}

function get_current_user(): ?array {
  if (!isset($_SESSION['user_id'])) {
    return null;
  }
  
  $stmt = $GLOBALS['pdo']->prepare('SELECT id, name, email, role FROM users WHERE id = ? AND active = 1');
  $stmt->execute([$_SESSION['user_id']]);
  return $stmt->fetch() ?: null;
}
?>