<?php
/**
 * Minimal debug script - tests step by step what fails
 */

// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<!DOCTYPE html><html><head><title>Minimal Debug</title></head><body>";
echo "<h1>Minimal Debug Test</h1>";

// Step 1: Test basic PHP
echo "<p>✅ PHP is working</p>";

// Step 2: Test file paths
echo "<p>Current directory: " . __DIR__ . "</p>";
echo "<p>Bootstrap path: " . __DIR__ . '/bootstrap.php</p>";

if (!file_exists(__DIR__ . '/bootstrap.php')) {
    echo "<p>❌ bootstrap.php not found!</p>";
    exit;
}
echo "<p>✅ bootstrap.php exists</p>";

// Step 3: Test constants definition
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
    echo "<p>✅ BASE_PATH defined: " . BASE_PATH . "</p>";
}

// Step 4: Test .env file
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    echo "<p>✅ .env file exists</p>";
} else {
    echo "<p>❌ .env file missing</p>";
}

// Step 5: Test config file loading
$configFile = __DIR__ . '/app/config.php';
if (file_exists($configFile)) {
    echo "<p>✅ config.php exists</p>";
    
    // Try to load it
    try {
        require_once $configFile;
        echo "<p>✅ config.php loaded</p>";
        echo "<p>DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NOT DEFINED') . "</p>";
        echo "<p>DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NOT DEFINED') . "</p>";
        echo "<p>DB_USER: " . (defined('DB_USER') ? DB_USER : 'NOT DEFINED') . "</p>";
    } catch (Exception $e) {
        echo "<p>❌ config.php error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>❌ config.php missing</p>";
}

echo "</body></html>";
?>