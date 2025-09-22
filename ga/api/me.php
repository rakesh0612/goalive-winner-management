<?php
require __DIR__ . '/config.php';
if (!isset($_SESSION['uid']) && !isset($_SESSION['user_id'])) json_err('Not signed in', 401);

$user_id = $_SESSION['user_id'] ?? $_SESSION['uid'] ?? 0;
json_ok(['user'=>[
  'id' => $user_id,
  'email' => $_SESSION['email'] ?? '',
  'name' => $_SESSION['name'] ?? '',
  'role' => $_SESSION['role'] ?? ''
]]);
