<?php
require __DIR__ . '/config.php';

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$email = trim((string)($input['email'] ?? ''));
$password = (string)($input['password'] ?? '');

if ($email === '' || $password === '') json_err('Email and password required', 422);

$stmt = $pdo->prepare('SELECT id, name, email, password_hash, role, active FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !$user['active'] || !password_verify($password, $user['password_hash'])) {
  json_err('Invalid credentials', 401);
}

$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['uid'] = (int)$user['id']; // Backward compatibility
$_SESSION['email'] = $user['email'];
$_SESSION['name'] = $user['name'];
$_SESSION['role'] = $user['role'];

$pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = ?')->execute([ (int)$user['id'] ]);

json_ok(['user' => ['id'=>$user['id'], 'name'=>$user['name'], 'email'=>$user['email'], 'role'=>$user['role']]]);
