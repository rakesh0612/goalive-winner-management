<?php
require __DIR__ . '/config.php';
$actor = require_auth(['admin','sales','rj']);

$input = json_decode(file_get_contents('php://input'), true) ?: [];

$name = trim((string)($input['name'] ?? ''));
$phone_cc = trim((string)($input['phone_cc'] ?? '+973'));
$phone_local = trim((string)($input['phone_local'] ?? ''));
$contest_type = trim((string)($input['contest_type'] ?? 'On-Air'));
$win_date = trim((string)($input['win_date'] ?? date('Y-m-d')));
$show_id = trim((string)($input['show_id'] ?? ''));
$batch_id = trim((string)($input['batch_id'] ?? ''));
$seq = isset($input['seq']) ? (int)$input['seq'] : null;

if ($name === '' || $phone_local === '') json_err('Name and phone are required', 422);

$phone_e164 = normalize_phone($phone_cc, $phone_local);

// Cooldown days
$COOLDOWN_DAYS = 90;

// Check cooldown
$chk = $pdo->prepare('SELECT COUNT(*) AS c FROM winners WHERE phone_e164 = ? AND win_date >= DATE_SUB(?, INTERVAL ? DAY)');
$chk->execute([$phone_e164, $win_date, $COOLDOWN_DAYS]);
if ((int)$chk->fetch()['c'] > 0) {
  json_err('This winner has already won within the last '.$COOLDOWN_DAYS.' days', 409);
}

// Generate WIN-XXXX id
$pdo->exec('INSERT INTO id_seq (stub) VALUES (NULL)');
$seq_id = (int)$pdo->lastInsertId();
$winner_id = sprintf('WIN-%04d', $seq_id);

$stmt = $pdo->prepare('INSERT INTO winners (winner_id, name, phone_cc, phone_local, phone_e164, contest_type, win_date, batch_id, seq, show_id, created_by_email) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
$stmt->execute([$winner_id, $name, $phone_cc, $phone_local, $phone_e164, $contest_type, $win_date, $batch_id ?: null, $seq, $show_id ?: null, $actor['email']]);

json_ok(['winner_id'=>$winner_id]);
