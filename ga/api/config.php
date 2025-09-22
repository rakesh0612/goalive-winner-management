<?php
declare(strict_types=1);
ini_set('display_errors', '0');
ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
  ini_set('session.cookie_secure', '1');
}
session_name('GA_WINNERSESS');
session_start();

header('Content-Type: application/json; charset=utf-8');

// >>> REPLACE with your Cloudways DB credentials from Application > Access Details
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
      PDO::ATTR_EMULATE_PREPARES => false,
    ]
  );
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false, 'error'=>'DB connection failed']);
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
  if (!isset($_SESSION['uid'])) json_err('Unauthorized', 401);
  $u = [
    'id' => $_SESSION['uid'],
    'email' => $_SESSION['email'] ?? '',
    'name' => $_SESSION['name'] ?? '',
    'role' => $_SESSION['role'] ?? '',
  ];
  if ($roles && !in_array(strtolower($u['role']), array_map('strtolower', $roles), true)) {
    json_err('Forbidden', 403);
  }
  return $u;
}

function normalize_phone(string $cc, string $local): string {
  $cc = preg_replace('/\D+/', '', $cc);
  $local = preg_replace('/\D+/', '', $local);
  if ($cc == '') $cc = '973'; // default Bahrain
  return '+' . $cc . $local;
}
