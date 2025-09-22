<?php
// Upload CSV with header: name,phone_cc,phone_local,contest_type,win_date,show_id,batch_id,seq
require __DIR__ . '/config.php';
$actor = require_auth(['admin','sales']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Content-Type: text/html; charset=utf-8');
  echo '<h3>POST a CSV file to this endpoint.</h3>';
  exit;
}
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
  json_err('No file uploaded', 400);
}

$cooldownDays = 90;
$tmp = $_FILES['file']['tmp_name'];
$fh = fopen($tmp, 'r');
if (!$fh) json_err('Failed to open uploaded file', 500);

$header = fgetcsv($fh);
$map = array_flip(array_map('strtolower', $header));
$required = ['name','phone_local'];
foreach ($required as $req) if (!isset($map[$req])) json_err("Missing column: $req", 422);

$inserted = 0; $skipped = 0; $errors = 0;

$ins = $pdo->prepare('INSERT INTO winners (winner_id, name, phone_cc, phone_local, phone_e164, contest_type, win_date, batch_id, seq, show_id, created_by_email) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
$dupCheck = $pdo->prepare('SELECT COUNT(*) AS c FROM winners WHERE phone_e164 = ? AND win_date >= DATE_SUB(?, INTERVAL ? DAY)');
$seqIns = $pdo->prepare('INSERT INTO id_seq (stub) VALUES (NULL)');

while (($row = fgetcsv($fh)) !== false) {
  $get = function($key, $def='') use ($row, $map) {
    $idx = $map[$key] ?? null;
    if ($idx === null) return $def;
    return trim((string)($row[$idx] ?? $def));
  };

  $name = $get('name');
  $phone_cc = $get('phone_cc', '+973');
  $phone_local = $get('phone_local');
  if ($name === '' || $phone_local === '') { $skipped++; continue; }
  $contest_type = $get('contest_type', 'On-Air');
  $win_date = $get('win_date', date('Y-m-d'));
  $show_id = $get('show_id', '');
  $batch_id = $get('batch_id', '');
  $seq = $get('seq', '');
  $seq = ($seq === '') ? null : (int)$seq;

  $phone_e164 = normalize_phone($phone_cc, $phone_local);
  $dupCheck->execute([$phone_e164, $win_date, $cooldownDays]);
  if ((int)$dupCheck->fetch()['c'] > 0) { $skipped++; continue; }

  try {
    $seqIns->execute();
    $winner_id = sprintf('WIN-%04d', (int)$pdo->lastInsertId());
    $ins->execute([$winner_id, $name, $phone_cc, $phone_local, $phone_e164, $contest_type, $win_date, $batch_id ?: null, $seq, $show_id ?: null, $actor['email']]);
    $inserted++;
  } catch (Throwable $e) {
    $errors++;
  }
}

fclose($fh);
json_ok(['summary'=>['inserted'=>$inserted, 'skipped_duplicates'=>$skipped, 'errors'=>$errors]]);
