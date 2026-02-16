<?php
// Database setup script - run this once to create missing tables
require_once __DIR__ . '/bootstrap.php';

echo "<h1>Database Setup</h1>";

try {
    $pdo = db();
    echo "✅ Database connected<br><br>";
    
    // Read and execute the SQL file
    $sqlFile = __DIR__ . '/auth_tables.sql';
    if (!file_exists($sqlFile)) {
        die("❌ auth_tables.sql file not found!");
    }
    
    $sql = file_get_contents($sqlFile);
    echo "<h2>Executing SQL Migration...</h2>";
    
    // Split SQL into individual queries
    $queries = explode(';', $sql);
    $success = 0;
    $errors = 0;
    
    foreach ($queries as $query) {
        $query = trim($query);
        if (empty($query)) continue;
        
        try {
            $pdo->exec($query);
            $success++;
            echo "✅ Query executed successfully<br>";
        } catch (Exception $e) {
            $errors++;
            echo "❌ Query failed: " . $e->getMessage() . "<br>";
            echo "Query: " . substr($query, 0, 100) . "...<br>";
        }
    }
    
    echo "<br><h2>Migration Summary</h2>";
    echo "✅ Successful queries: $success<br>";
    echo ($errors > 0 ? "❌" : "✅") . " Failed queries: $errors<br>";
    
    // Verify tables exist
    echo "<br><h2>Table Verification</h2>";
    $tables = ['users', 'email_tokens', 'password_resets', 'login_attempts', 'audit_logs'];
    
    foreach ($tables as $table) {
        try {
            $result = $pdo->query("SELECT COUNT(*) as count FROM $table")->fetch();
            echo "✅ $table: {$result['count']} rows<br>";
        } catch (Exception $e) {
            echo "❌ $table: Table not found or error<br>";
        }
    }
    
    echo "<br><h2>User Table Columns Check</h2>";
    try {
        $stmt = $pdo->query("DESCRIBE users");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $requiredColumns = ['email', 'email_verified', 'status', 'google_id', 'avatar_url', 'last_login_at'];
        foreach ($requiredColumns as $col) {
            $found = false;
            foreach ($columns as $column) {
                if ($column['Field'] === $col) {
                    $found = true;
                    break;
                }
            }
            echo ($found ? "✅" : "❌") . " $col column " . ($found ? "exists" : "MISSING") . "<br>";
        }
    } catch (Exception $e) {
        echo "❌ Could not check user table columns: " . $e->getMessage() . "<br>";
    }
    
    echo "<br><p><strong>If all checks show ✅, the database is ready!</strong></p>";
    echo "<p>You can now test <a href='index.php'>index.php</a></p>";
    
} catch (Exception $e) {
    echo "❌ Setup failed: " . $e->getMessage() . "<br>";
}
?>