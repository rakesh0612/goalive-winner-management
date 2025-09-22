#!/usr/bin/env php
<?php
// Start script for PHP built-in server
// Usage: php start_server.php [port]

$port = $argv[1] ?? '8080';
$host = '127.0.0.1';

echo "🚀 Starting GO ALIVE MEDIA Winner Management Server\n";
echo "📍 Server: http://{$host}:{$port}\n";
echo "📝 Login page: http://{$host}:{$port}/login.html\n";
echo "🛡️  Admin dashboard: http://{$host}:{$port}/index.html\n";
echo "\n";
echo "⚠️  DEVELOPMENT MODE - Not for production use!\n";
echo "📋 Default admin setup at: http://{$host}:{$port}/simple_setup.php\n";
echo "\n";
echo "Press Ctrl+C to stop the server\n";
echo "----------------------------------------\n";

// Start PHP built-in server
$documentRoot = __DIR__;
$command = "php -S {$host}:{$port} -t {$documentRoot}";

echo "Running: {$command}\n\n";
passthru($command);
?>