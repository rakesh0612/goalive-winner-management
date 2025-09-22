<?php
// SIMPLIFIED ADMIN SETUP - No external dependencies
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database credentials (directly included)
$DB_HOST = '139.59.124.208';
$DB_NAME = 'acqmgdrqzp';
$DB_USER = 'acqmgdrqzp';
$DB_PASS = 'nC3gUCnutG';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $name = trim($_POST['name'] ?? 'Admin');
    $pass = (string)($_POST['password'] ?? '');
    
    if ($email === '' || $pass === '') {
        $error = "Email and password are required.";
    } else {
        try {
            // Connect to database
            $pdo = new PDO(
                "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
                $DB_USER,
                $DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
            
            // Check if users table exists
            $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
            if ($stmt->rowCount() == 0) {
                $error = "Users table does not exist. Please run schema.sql first.";
            } else {
                // Create/update admin user
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role, active)
                  VALUES (?, ?, ?, 'admin', 1)
                  ON DUPLICATE KEY UPDATE name=VALUES(name), password_hash=VALUES(password_hash), role='admin', active=1");
                $stmt->execute([$name, $email, $hash]);
                $success = "Admin user created/updated for $email. You can now login!";
            }
            
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Setup</title>
    <style>
        body { font-family: system-ui, sans-serif; padding: 24px; max-width: 480px; margin: auto; }
        .error { color: red; background: #ffe6e6; padding: 10px; border-radius: 4px; }
        .success { color: green; background: #e6ffe6; padding: 10px; border-radius: 4px; }
        input { width: 100%; padding: 8px; margin: 5px 0; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #005a87; }
    </style>
</head>
<body>
    <h2>🔧 Admin Setup</h2>
    
    <?php if (isset($error)): ?>
        <div class="error">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="success">✅ <?= htmlspecialchars($success) ?></div>
        <p><a href="/">← Go to main application</a></p>
    <?php else: ?>
        <form method="post">
            <label>Name:</label>
            <input name="name" value="Admin" required>
            
            <label>Email:</label>
            <input name="email" type="email" placeholder="admin@example.com" required>
            
            <label>Password:</label>
            <input name="password" type="password" required>
            
            <button type="submit">Create Admin User</button>
        </form>
        
        <hr>
        <h3>📋 Database Status</h3>
        <?php
        try {
            $pdo = new PDO(
                "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
                $DB_USER,
                $DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            echo "<p style='color:green'>✅ Database connection: OK</p>";
            
            $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
            if ($stmt->rowCount() > 0) {
                echo "<p style='color:green'>✅ Users table: EXISTS</p>";
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role='admin'");
                $result = $stmt->fetch();
                echo "<p>👤 Admin users: " . $result['count'] . "</p>";
            } else {
                echo "<p style='color:orange'>⚠️ Users table: MISSING (run schema.sql)</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color:red'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        ?>
    <?php endif; ?>
    
    <p><small>Delete this file after creating your admin user.</small></p>
</body>
</html>