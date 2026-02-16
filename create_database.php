<?php
/**
 * Database Creation Script
 * Creates the database and runs initial setup
 */

require_once __DIR__ . '/bootstrap.php';

echo "<h1>Database Setup Wizard</h1>";

try {
    // Connect to MySQL without database
    $dsn = sprintf('mysql:host=%s;charset=%s', DB_HOST, DB_CHARSET);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "✅ MySQL server connection successful<br><br>";
    
    // Check if database exists
    $stmt = $pdo->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?");
    $stmt->execute([DB_NAME]);
    $db_exists = $stmt->fetch();
    
    if (!$db_exists) {
        echo "<h2>Creating Database</h2>";
        $sql = "CREATE DATABASE " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        $pdo->exec($sql);
        echo "✅ Database '" . DB_NAME . "' created successfully<br><br>";
    } else {
        echo "✅ Database '" . DB_NAME . "' already exists<br><br>";
    }
    
    // Switch to the database
    $pdo->exec("USE " . DB_NAME);
    
    echo "<h2>Setting Up Tables...</h2>";
    
    // Run schema.sql first
    $schemaFile = __DIR__ . '/schema.sql';
    if (file_exists($schemaFile)) {
        echo "<h3>Running schema.sql</h3>";
        $sql = file_get_contents($schemaFile);
        $queries = explode(';', $sql);
        $success = 0;
        
        foreach ($queries as $query) {
            $query = trim($query);
            if (empty($query)) continue;
            
            try {
                $pdo->exec($query);
                $success++;
            } catch (Exception $e) {
                // Ignore "table already exists" errors
                if (!strpos($e->getMessage(), 'already exists')) {
                    echo "⚠️ Query warning: " . $e->getMessage() . "<br>";
                }
            }
        }
        echo "✅ Schema setup: $success queries executed<br>";
    } else {
        echo "⚠️ schema.sql not found<br>";
    }
    
    // Run auth_tables.sql
    $authFile = __DIR__ . '/auth_tables.sql';
    if (file_exists($authFile)) {
        echo "<h3>Running auth_tables.sql</h3>";
        $sql = file_get_contents($authFile);
        $queries = explode(';', $sql);
        $success = 0;
        
        foreach ($queries as $query) {
            $query = trim($query);
            if (empty($query)) continue;
            
            try {
                $pdo->exec($query);
                $success++;
            } catch (Exception $e) {
                // Ignore "table already exists" and "column already exists" errors
                if (!strpos($e->getMessage(), 'already exists')) {
                    echo "⚠️ Query warning: " . $e->getMessage() . "<br>";
                }
            }
        }
        echo "✅ Auth tables setup: $success queries executed<br>";
    } else {
        echo "⚠️ auth_tables.sql not found<br>";
    }
    
    echo "<br><h2>✅ Database Setup Complete!</h2>";
    echo "<p>You can now:</p>";
    echo "<ul>";
    echo "<li><a href='index.php'>Test the main site</a></li>";
    echo "<li><a href='debug.php'>Run debug checks</a></li>";
    echo "<li><a href='auth/register.php'>Register a new account</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h2>❌ Setup Failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check:</p>";
    echo "<ul>";
    echo "<li>MySQL server is running</li>";
    echo "<li>Database credentials in config.php are correct</li>";
    echo "<li>User has CREATE DATABASE privileges</li>";
    echo "</ul>";
}
?>