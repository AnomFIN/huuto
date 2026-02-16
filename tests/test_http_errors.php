<?php
/**
 * Test script to check if pages load without HTTP 500 errors
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>HTTP Error Test</h1>";
echo "<p>Testing if key pages can be included without fatal errors...</p>";

$testPages = [
    'app/config.php',
    'app/db.php', 
    'app/helpers.php',
    'app/auth.php',
    'app/email.php',
    'src/models/Database.php',
];

$errors = [];
$success = [];

foreach ($testPages as $page) {
    echo "<hr>";
    echo "<h3>Testing: $page</h3>";
    
    try {
        // Capture output in case there are warnings
        ob_start();
        require_once __DIR__ . '/../' . $page;
        $output = ob_get_clean();
        
        if ($output) {
            echo "<div style='background: #fff3cd; padding: 10px; margin: 10px 0;'>";
            echo "<strong>Output:</strong><br>";
            echo nl2br(htmlspecialchars($output));
            echo "</div>";
        }
        
        echo "✅ <strong style='color: green;'>OK</strong> - File loaded successfully<br>";
        $success[] = $page;
    } catch (Throwable $e) {
        echo "❌ <strong style='color: red;'>ERROR</strong><br>";
        echo "<div style='background: #f8d7da; padding: 10px; margin: 10px 0;'>";
        echo "<strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
        echo "<strong>File:</strong> " . htmlspecialchars($e->getFile()) . "<br>";
        echo "<strong>Line:</strong> " . $e->getLine() . "<br>";
        echo "</div>";
        $errors[] = $page;
    }
}

echo "<hr>";
echo "<h2>Summary</h2>";
echo "<p><strong style='color: green;'>Successful:</strong> " . count($success) . "/" . count($testPages) . "</p>";
if (!empty($errors)) {
    echo "<p><strong style='color: red;'>Failed:</strong> " . count($errors) . "</p>";
    echo "<ul>";
    foreach ($errors as $err) {
        echo "<li>" . htmlspecialchars($err) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: green; font-weight: bold;'>✅ All tests passed! No HTTP 500 errors expected.</p>";
}

// Test if constants are defined
echo "<hr>";
echo "<h2>Configuration Check</h2>";
$constants = ['SITE_NAME', 'BASE_URL', 'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_CHARSET'];
foreach ($constants as $const) {
    if (defined($const)) {
        echo "✅ $const = " . htmlspecialchars(constant($const)) . "<br>";
    } else {
        echo "❌ $const is NOT defined<br>";
    }
}

// Test if functions exist
echo "<hr>";
echo "<h2>Function Check</h2>";
$functions = ['db', 'auth', 'email', 'is_logged_in', 'current_user', 'validate_email', 'csrf_token'];
foreach ($functions as $func) {
    if (function_exists($func)) {
        echo "✅ $func() exists<br>";
    } else {
        echo "❌ $func() does NOT exist<br>";
    }
}

// Test if session is started
echo "<hr>";
echo "<h2>Session Check</h2>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✅ Session is active<br>";
    echo "Session ID: " . session_id() . "<br>";
} else {
    echo "❌ Session is NOT active<br>";
}
