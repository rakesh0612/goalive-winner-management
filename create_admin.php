<?php
// Quick Admin User Creation Script
// Run this once on your server to create the first admin user

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Use the same database config
$DB_HOST = '139.59.124.208';
$DB_NAME = 'acqmgdrqzp';
$DB_USER = 'acqmgdrqzp';
$DB_PASS = 'nC3gUCnutG';

echo "<h2>🚀 GO ALIVE MEDIA - Admin User Setup</h2>";

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
    
    echo "✅ Database connected successfully<br><br>";
    
    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() == 0) {
        echo "❌ Users table doesn't exist. Creating table...<br>";
        
        // Create users table
        $createTable = "
        CREATE TABLE `users` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(100) NOT NULL,
            `email` varchar(100) NOT NULL,
            `password_hash` varchar(255) NOT NULL,
            `role` enum('admin','sales','rj','digital','collection') NOT NULL DEFAULT 'admin',
            `active` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `last_login` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
        
        $pdo->exec($createTable);
        echo "✅ Users table created<br><br>";
    } else {
        echo "✅ Users table exists<br><br>";
    }
    
    // Create default admin users
    $defaultUsers = [
        ['name' => 'Admin User', 'email' => 'admin@goalive.local', 'password' => 'admin123', 'role' => 'admin'],
        ['name' => 'Test Admin', 'email' => 'test@goalive.local', 'password' => 'test123', 'role' => 'admin'],
        ['name' => 'Sales Manager', 'email' => 'sales@goalive.local', 'password' => 'sales123', 'role' => 'sales'],
        ['name' => 'RJ User', 'email' => 'rj@goalive.local', 'password' => 'rj123', 'role' => 'rj'],
        ['name' => 'Digital Manager', 'email' => 'digital@goalive.local', 'password' => 'digital123', 'role' => 'digital']
    ];
    
    echo "<h3>Creating Default Users:</h3>";
    
    foreach ($defaultUsers as $user) {
        $hash = password_hash($user['password'], PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role, active) VALUES (?, ?, ?, ?, 1)");
            $stmt->execute([$user['name'], $user['email'], $hash, $user['role']]);
            echo "✅ Created: <strong>{$user['email']}</strong> / <strong>{$user['password']}</strong> ({$user['role']})<br>";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                echo "⚠️ User {$user['email']} already exists<br>";
            } else {
                echo "❌ Error creating {$user['email']}: " . $e->getMessage() . "<br>";
            }
        }
    }
    
    echo "<br><h3>🎉 Setup Complete!</h3>";
    echo "<p><strong>You can now login with any of these credentials:</strong></p>";
    echo "<ul>";
    foreach ($defaultUsers as $user) {
        echo "<li><strong>{$user['email']}</strong> / <strong>{$user['password']}</strong> ({$user['role']} role)</li>";
    }
    echo "</ul>";
    
    echo '<p><a href="login.html" style="background: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">🚀 Go to Login Page</a></p>';
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage();
    echo "<br><br>Please check your database credentials in ga/api/config.php";
}
?>

<style>
body { font-family: system-ui; max-width: 600px; margin: 50px auto; padding: 20px; line-height: 1.6; }
h2 { color: #4f46e5; }
h3 { color: #059669; margin-top: 30px; }
ul { background: #f0f9f0; padding: 20px; border-radius: 8px; margin: 20px 0; }
li { margin: 5px 0; }
</style>