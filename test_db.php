<?php
// Simple database connection test
$DB_HOST = '139.59.124.208';
$DB_NAME = 'acqmgdrqzp';
$DB_USER = 'acqmgdrqzp';
$DB_PASS = 'nC3gUCnutG';

echo "<h2>Database Connection Test</h2>";

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    echo "<p style='color:green;'>✅ Database connection successful!</p>";
    
    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green;'>✅ Users table exists</p>";
        
        // Count users
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        echo "<p>👥 Users in database: " . $result['count'] . "</p>";
    } else {
        echo "<p style='color:orange;'>⚠️ Users table does NOT exist - you need to run schema.sql</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}
?>

<h3>Next Steps:</h3>
<ul>
<li>If connection successful but no users table: Run your schema.sql file in phpMyAdmin</li>
<li>If connection failed: Check your database credentials in Cloudways</li>
<li>Delete this test file after use for security</li>
</ul>