<?php
/**
 * Cloudways Server Diagnostic Tool
 * Run this file to identify the cause of 500 Internal Server Error
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h1>🔧 Cloudways Server Diagnostics</h1>";
echo "<style>body{font-family:system-ui;margin:40px;line-height:1.6}h1,h2{color:#4f46e5}pre{background:#f5f5f5;padding:15px;border-radius:5px;overflow-x:auto}.success{color:#059669;font-weight:bold}.error{color:#dc2626;font-weight:bold}.warning{color:#f59e0b;font-weight:bold}</style>";

echo "<h2>📊 Server Environment Check</h2>";

// Check PHP version
echo "<strong>PHP Version:</strong> " . PHP_VERSION . "<br>";
if (version_compare(PHP_VERSION, '7.4.0') >= 0) {
    echo "<span class='success'>✅ PHP version is compatible</span><br><br>";
} else {
    echo "<span class='error'>❌ PHP version too old (need 7.4+)</span><br><br>";
}

// Check required PHP extensions
$requiredExtensions = ['pdo', 'pdo_mysql', 'json', 'session', 'curl'];
echo "<strong>PHP Extensions:</strong><br>";
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<span class='success'>✅ $ext</span><br>";
    } else {
        echo "<span class='error'>❌ $ext (MISSING)</span><br>";
    }
}

echo "<br><h2>🗄️ Database Connection Test</h2>";

// Database configuration from config.php
$DB_HOST = '139.59.124.208';
$DB_NAME = 'acqmgdrqzp';
$DB_USER = 'acqmgdrqzp';
$DB_PASS = 'nC3gUCnutG';

try {
    echo "Testing connection to: <strong>$DB_HOST</strong><br>";
    echo "Database: <strong>$DB_NAME</strong><br>";
    echo "Username: <strong>$DB_USER</strong><br><br>";
    
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    
    echo "<span class='success'>✅ Database connection successful!</span><br>";
    
    // Test query
    $stmt = $pdo->query("SELECT VERSION() as mysql_version");
    $version = $stmt->fetch();
    echo "MySQL Version: <strong>" . $version['mysql_version'] . "</strong><br>";
    
    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "<span class='success'>✅ Users table exists</span><br>";
        
        // Count users
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $count = $stmt->fetch()['count'];
        echo "Total users in database: <strong>$count</strong><br>";
    } else {
        echo "<span class='warning'>⚠️ Users table not found - run schema.sql</span><br>";
    }
    
} catch (PDOException $e) {
    echo "<span class='error'>❌ Database connection failed:</span><br>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}

echo "<br><h2>📁 File Permissions Check</h2>";

$filesToCheck = [
    'ga/api/config.php',
    'ga/api/login.php',
    'ga/api/me.php',
    'ga/api/logout.php',
    'index.html',
    'login.html'
];

foreach ($filesToCheck as $file) {
    if (file_exists($file)) {
        $perms = substr(sprintf('%o', fileperms($file)), -4);
        echo "<strong>$file:</strong> $perms ";
        if (is_readable($file)) {
            echo "<span class='success'>✅ Readable</span>";
        } else {
            echo "<span class='error'>❌ Not readable</span>";
        }
        echo "<br>";
    } else {
        echo "<strong>$file:</strong> <span class='error'>❌ File not found</span><br>";
    }
}

echo "<br><h2>🔍 PHP Configuration</h2>";

$importantSettings = [
    'memory_limit',
    'max_execution_time',
    'upload_max_filesize',
    'post_max_size',
    'session.save_handler',
    'session.save_path'
];

foreach ($importantSettings as $setting) {
    $value = ini_get($setting);
    echo "<strong>$setting:</strong> " . ($value ?: 'Not set') . "<br>";
}

echo "<br><h2>🧪 API Endpoint Test</h2>";

// Test if we can include config.php without errors
echo "Testing config.php inclusion...<br>";
try {
    if (file_exists('ga/api/config.php')) {
        // Capture any output
        ob_start();
        $error = null;
        
        try {
            include_once 'ga/api/config.php';
        } catch (Exception $e) {
            $error = $e;
        } catch (Error $e) {
            $error = $e;
        }
        
        $output = ob_get_clean();
        
        if ($error) {
            echo "<span class='error'>❌ Config.php has errors:</span><br>";
            echo "<pre>" . htmlspecialchars($error->getMessage()) . "</pre>";
        } else {
            echo "<span class='success'>✅ Config.php loads without errors</span><br>";
            if ($output) {
                echo "Output: <pre>" . htmlspecialchars($output) . "</pre>";
            }
        }
    } else {
        echo "<span class='error'>❌ ga/api/config.php not found</span><br>";
    }
} catch (Exception $e) {
    echo "<span class='error'>❌ Error testing config.php:</span><br>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}

echo "<br><h2>💡 Recommended Actions</h2>";

echo "<ol>";
echo "<li><strong>If database connection failed:</strong> Update credentials in ga/api/config.php</li>";
echo "<li><strong>If users table missing:</strong> Import schema.sql into your database</li>";
echo "<li><strong>If file permissions wrong:</strong> Set files to 644 and directories to 755</li>";
echo "<li><strong>If PHP extensions missing:</strong> Contact Cloudways support to enable them</li>";
echo "<li><strong>Check error logs:</strong> Look in Cloudways control panel > Server Management > Log</li>";
echo "</ol>";

echo "<br><h2>🚀 Next Steps</h2>";
echo "<p>1. Fix any issues shown above<br>";
echo "2. Try accessing: <a href='simple_setup.php'>simple_setup.php</a> to create admin user<br>";
echo "3. Test login at: <a href='login.html'>login.html</a><br>";
echo "4. If still having issues, check Cloudways error logs</p>";

echo "<br><small>Generated: " . date('Y-m-d H:i:s') . " | Server: " . ($_SERVER['SERVER_NAME'] ?? 'Unknown') . "</small>";
?>