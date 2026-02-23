<?php
require_once 'app/config.php';
require_once 'app/db.php';

try {
    $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $migration = file_get_contents('database/add_metadata_fields.sql');
    $statements = array_filter(array_map('trim', explode(';', $migration)));
    
    foreach ($statements as $sql) {
        if (!empty($sql)) {
            $db->exec($sql);
            echo 'Executed: ' . substr($sql, 0, 50) . '...' . PHP_EOL;
        }
    }
    
    echo 'Migration completed successfully!' . PHP_EOL;
    
} catch (Exception $e) {
    echo 'Migration failed: ' . $e->getMessage() . PHP_EOL;
}