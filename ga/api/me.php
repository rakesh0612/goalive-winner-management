<?php
require __DIR__ . '/config.php';
if (!isset($_SESSION['uid'])) json_err('Not signed in', 401);
json_ok(['user'=>[
  'id' => $_SESSION['uid'],
  'email' => $_SESSION['email'] ?? '',
  'name' => $_SESSION['name'] ?? '',
  'role' => $_SESSION['role'] ?? ''
]]);
