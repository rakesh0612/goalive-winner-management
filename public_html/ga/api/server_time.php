<?php
declare(strict_types=1);
require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
// Store in UTC; display Bahrain on the client
$nowUtc = new DateTime('now', new DateTimeZone('UTC'));
echo json_encode([
  'utc' => $nowUtc->format(DateTime::ATOM),   // e.g. 2025-09-21T22:15:00+00:00
  'epoch_ms' => (int) round(microtime(true) * 1000),
], JSON_UNESCAPED_SLASHES);
