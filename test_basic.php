<?php
// Simple test to check if basic PHP works
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "✅ Basic PHP is working!<br>";
echo "Server: " . ($_SERVER['SERVER_NAME'] ?? 'Unknown') . "<br>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Date: " . date('Y-m-d H:i:s') . "<br>";

// Test if we can start a session
try {
    session_start();
    echo "✅ Session started successfully<br>";
} catch (Exception $e) {
    echo "❌ Session error: " . $e->getMessage() . "<br>";
}

// Test database connection with minimal code
try {
    $pdo = new PDO(
        "mysql:host=139.59.124.208;dbname=acqmgdrqzp;charset=utf8mb4",
        "acqmgdrqzp",
        "nC3gUCnutG"
    );
    echo "✅ Database connection works<br>";
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<br><a href='cloudways_diagnostic.php'>📋 Run Full Diagnostics</a>";
?>