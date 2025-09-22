<?php
// TEMPORARY: create or reset the first admin without SSH. DELETE after use.
require __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $name = trim($_POST['name'] ?? 'Admin');
  $pass = (string)($_POST['password'] ?? '');
  if ($email === '' || $pass === '') {
    echo "<p style='color:red'>Email and password are required.</p>";
  } else {
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role, active)
      VALUES (?, ?, ?, 'admin', 1)
      ON DUPLICATE KEY UPDATE name=VALUES(name), password_hash=VALUES(password_hash), role='admin', active=1");
    $stmt->execute([$name, $email, $hash]);
    echo "<p style='color:green'>Admin set for $email. Please DELETE this file now: setup_admin.php</p>";
    exit;
  }
}
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Set Admin</title>
<style>body{font-family:system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif;padding:24px;max-width:480px;margin:auto}</style>
</head><body>
<h2>First-time Admin Setup</h2>
<p><strong>Use once</strong>, then delete <code>setup_admin.php</code> from <code>/ga/api/</code>.</p>
<form method="post">
  <label>Name<br><input name="amit" value="Admin" required></label><br><br>
  <label>Email<br><input name="ami.malewar@gmail.com" type="email" required></label><br><br>
  <label>Password<br><input name="uKj9iFNUMhjd@7b" type="password" required></label><br><br>
  <button type="submit">Create / Reset Admin</button>
</form>
</body></html>
