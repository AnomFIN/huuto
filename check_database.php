<?php
// Simple migration checker and runner
require_once 'bootstrap.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<h2>Database Check & Migration</h2>";
    
    // Check if auction_metadata table exists
    $result = $db->query("SHOW TABLES LIKE 'auction_metadata'")->fetchAll();
    if (empty($result)) {
        echo "<p style='color: red;'>❌ auction_metadata table missing - creating...</p>";
        
        $migration = "
        CREATE TABLE IF NOT EXISTS auction_metadata (
            id INT AUTO_INCREMENT PRIMARY KEY,
            auction_id INT NOT NULL,
            field_name VARCHAR(100) NOT NULL,
            field_value TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE CASCADE,
            UNIQUE KEY unique_auction_field (auction_id, field_name),
            INDEX idx_auction (auction_id),
            INDEX idx_field (field_name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->exec($migration);
        echo "<p style='color: green;'>✅ auction_metadata table created</p>";
    } else {
        echo "<p style='color: green;'>✅ auction_metadata table exists</p>";
    }
    
    // Check for map_coordinates column
    $columns = $db->query("SHOW COLUMNS FROM auctions LIKE 'map_coordinates'")->fetchAll();
    if (empty($columns)) {
        echo "<p style='color: red;'>❌ map_coordinates column missing - adding...</p>";
        $db->exec("ALTER TABLE auctions ADD COLUMN map_coordinates VARCHAR(50) NULL");
        echo "<p style='color: green;'>✅ map_coordinates column added</p>";
    } else {
        echo "<p style='color: green;'>✅ map_coordinates column exists</p>";
    }
    
    // Check for seller_commitment column
    $columns = $db->query("SHOW COLUMNS FROM auctions LIKE 'seller_commitment'")->fetchAll();
    if (empty($columns)) {
        echo "<p style='color: red;'>❌ seller_commitment column missing - adding...</p>";
        $db->exec("ALTER TABLE auctions ADD COLUMN seller_commitment BOOLEAN DEFAULT FALSE");
        echo "<p style='color: green;'>✅ seller_commitment column added</p>";
    } else {
        echo "<p style='color: green;'>✅ seller_commitment column exists</p>";
    }
    
    // Check for caption column in auction_images
    $columns = $db->query("SHOW COLUMNS FROM auction_images LIKE 'caption'")->fetchAll();
    if (empty($columns)) {
        echo "<p style='color: red;'>❌ caption column missing - adding...</p>";
        $db->exec("ALTER TABLE auction_images ADD COLUMN caption VARCHAR(255) NULL");
        echo "<p style='color: green;'>✅ caption column added</p>";
    } else {
        echo "<p style='color: green;'>✅ caption column exists</p>";
    }
    
    echo "<br><h3>Database ready! 🎉</h3>";
    echo "<a href='add_product.php'>Go to Add Product</a>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>