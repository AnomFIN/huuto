<?php
// Simple debug script to check database status
require_once __DIR__ . '/bootstrap.php';

echo "<h1>Database Debug</h1>";
echo "<h2>1. Config Check</h2>";

try {
    echo "✅ Config loaded<br>";
    echo "SITE_NAME: " . (defined('SITE_NAME') ? SITE_NAME : 'NOT DEFINED') . "<br>";
    echo "BASE_URL: " . (defined('BASE_URL') ? BASE_URL : 'NOT DEFINED') . "<br>";
} catch (Exception $e) {
    echo "❌ Config error: " . $e->getMessage() . "<br>";
}

echo "<h2>2. Database Configuration</h2>";
echo "DB_HOST: " . DB_HOST . "<br>";
echo "DB_NAME: " . DB_NAME . "<br>";
echo "DB_USER: " . DB_USER . "<br>";
echo "DB_PASS: " . (empty(DB_PASS) ? '(empty)' : '(set)') . "<br>";
echo "DB_CHARSET: " . DB_CHARSET . "<br><br>";

echo "<h2>3. Database Connection Test</h2>";
try {
    // First try to connect without selecting database
    $dsn_no_db = sprintf('mysql:host=%s;charset=%s', DB_HOST, DB_CHARSET);
    $pdo_test = new PDO($dsn_no_db, DB_USER, DB_PASS);
    echo "✅ MySQL server connection successful<br>";
    
    // Check if database exists
    $stmt = $pdo_test->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?");
    $stmt->execute([DB_NAME]);
    $db_exists = $stmt->fetch();
    
    if ($db_exists) {
        echo "✅ Database '" . DB_NAME . "' exists<br>";
        
        // Now try full connection with database
        require_once __DIR__ . '/app/db.php';
        $db = db();
        echo "✅ Database connection successful<br>";
        
        // Test basic query
        $result = $db->query("SELECT 1 as test")->fetch();
        echo "✅ Database query test: " . $result['test'] . "<br>";
    } else {
        echo "❌ Database '" . DB_NAME . "' does not exist!<br>";
        echo "💡 <strong>Solution:</strong> Create database by running:<br>";
        echo "<code>CREATE DATABASE " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;</code><br>";
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
    echo "💡 Check your MySQL server is running and credentials are correct<br>";
}

echo "<h2>4. Table Check</h2>";
try {
    if (!isset($db)) {
        echo "❌ Database connection not available - skipping table check<br>";
    } else {
        $tables = ['users', 'categories', 'auctions', 'email_tokens', 'login_attempts', 'password_resets'];
        
        foreach ($tables as $table) {
            try {
                $result = $db->query("SELECT COUNT(*) as count FROM $table")->fetch();
                echo "✅ $table: {$result['count']} rows<br>";
            } catch (Exception $e) {
                echo "❌ $table: " . $e->getMessage() . "<br>";
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Table check failed: " . $e->getMessage() . "<br>";
}

echo "<h2>5. Model Check</h2>";
try {
    require_once __DIR__ . '/src/models/Database.php';
    require_once __DIR__ . '/src/models/Auction.php';
    require_once __DIR__ . '/src/models/Category.php';
    
    $auctionModel = new Auction();
    $categoryModel = new Category();
    
    echo "✅ Models loaded successfully<br>";
    
    // Test methods
    $categories = $categoryModel->getAllCategories();
    echo "✅ Categories loaded: " . count($categories) . " items<br>";
    
} catch (Exception $e) {
    echo "❌ Model error: " . $e->getMessage() . "<br>";
}

echo "<h2>6. Auth Check</h2>";
try {
    require_once __DIR__ . '/app/auth.php';
    require_once __DIR__ . '/app/helpers.php';
    
    echo "✅ Auth system loaded<br>";
    echo "is_logged_in function: " . (function_exists('is_logged_in') ? '✅ exists' : '❌ missing') . "<br>";
    
} catch (Exception $e) {
    echo "❌ Auth error: " . $e->getMessage() . "<br>";
}

echo "<hr><h2>Quick Database Setup</h2>";
echo "<p>If database doesn't exist, run these MySQL commands:</p>";
echo "<pre>";
echo "CREATE DATABASE " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
echo "USE " . DB_NAME . ";\n";
echo "-- Then run the SQL from schema.sql and auth_tables.sql\n";
echo "</pre>";
echo "<p>Or use <a href='setup_database.php'>setup_database.php</a> after creating the database.</p>";
echo "<hr><p>Debug completed. If you see errors above, fix them before trying index.php</p>";
?>