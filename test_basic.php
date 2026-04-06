<?php
/**
 * Simple test to check if basic PHP works without bootstrap
 */
echo "<!DOCTYPE html><html><head><title>PHP Test</title></head><body>";
echo "<h1>PHP Test</h1>";
echo "<p>✅ PHP version: " . phpversion() . "</p>";
echo "<p>✅ Current time: " . date('Y-m-d H:i:s') . "</p>";

// Test database constants from .env
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    echo "<p>✅ .env file exists</p>";
    
    // Load .env manually for this test
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $envVars = [];
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, '"\' ');
        $envVars[$key] = $value;
    }
    
    if (isset($envVars['DB_HOST'], $envVars['DB_NAME'])) {
        echo "<p>✅ Database config found in .env:</p>";
        echo "<ul>";
        echo "<li>Host: " . htmlspecialchars($envVars['DB_HOST']) . "</li>";
        echo "<li>Database: " . htmlspecialchars($envVars['DB_NAME']) . "</li>";
        echo "<li>User: " . htmlspecialchars($envVars['DB_USER']) . "</li>";
        echo "</ul>";
    } else {
        echo "<p>❌ Database config missing from .env</p>";
    }
} else {
    echo "<p>❌ .env file missing</p>";
}

echo "<hr>";
echo "<p><a href='debug_minimal.php'>Test minimal debug</a></p>";
echo "<p><a href='index.php'>Test main index</a></p>";
echo "</body></html>";
?>