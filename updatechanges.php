<?php
/**
 * Database Migration Script - updatechanges.php
 * 
 * This script safely upgrades legacy database installations to support the premium marketplace features.
 * Run this once manually after deployment to existing databases.
 * 
 * IMPORTANT: This script is idempotent - it can be run multiple times safely.
 */

// Bootstrap the application
require_once __DIR__ . '/bootstrap.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Huuto247 Database Upgrade Script</h1>\n";
echo "<p>Starting database migration to premium marketplace structure...</p>\n";
echo "<pre>\n";

try {
    $db = Database::getInstance()->getConnection();
    
    echo "[INFO] Connected to database successfully\n";
    echo "[INFO] Starting migration process...\n\n";

    // Track changes
    $changes = [];
    $warnings = [];
    $errors = [];

    // =====================================================
    // 1. EXPAND AUCTIONS TABLE WITH NEW PREMIUM FIELDS
    // =====================================================
    
    echo "=== STEP 1: Expanding auctions table ===\n";
    
    $auctionColumns = [
        'short_summary' => 'VARCHAR(255) NULL COMMENT "Brief auction description for listings"',
        'featured' => 'TINYINT(1) DEFAULT 0 COMMENT "Featured auction flag"',
        'condition_grade' => 'VARCHAR(50) NULL COMMENT "Overall condition grade"',
        'seller_notes' => 'TEXT NULL COMMENT "Additional seller information"',
        'pickup_info' => 'TEXT NULL COMMENT "Pickup instructions and details"',
        'shipping_info' => 'TEXT NULL COMMENT "Shipping options and costs"',
        'payment_info' => 'TEXT NULL COMMENT "Payment methods and deadlines"',
        'inspection_info' => 'TEXT NULL COMMENT "Inspection opportunities"',
        'included_items' => 'TEXT NULL COMMENT "What is included in the sale"',
        'defects' => 'TEXT NULL COMMENT "Known defects and issues"',
        'warranty_info' => 'VARCHAR(255) NULL COMMENT "Warranty status"',
        'model_reference' => 'VARCHAR(100) NULL COMMENT "Model number or reference"',
        'serial_number' => 'VARCHAR(100) NULL COMMENT "Serial number if applicable"',
        'delivery_available' => 'TINYINT(1) DEFAULT 0 COMMENT "Delivery option available"',
        'pickup_available' => 'TINYINT(1) DEFAULT 1 COMMENT "Pickup option available"',
        'payment_deadline_days' => 'INT DEFAULT 1 COMMENT "Payment deadline in days"',
        'storage_fee_info' => 'VARCHAR(255) NULL COMMENT "Storage fee details"'
    ];
    
    foreach ($auctionColumns as $columnName => $definition) {
        try {
            // Check if column already exists
            $checkQuery = "SHOW COLUMNS FROM auctions LIKE ?";
            $stmt = $db->prepare($checkQuery);
            $stmt->execute([$columnName]);
            $columnExists = $stmt->fetch();
            
            if (!$columnExists) {
                $alterQuery = "ALTER TABLE auctions ADD COLUMN $columnName $definition";
                $db->exec($alterQuery);
                echo "[✓] Added column 'auctions.$columnName'\n";
                $changes[] = "Added auctions.$columnName column";
            } else {
                echo "[SKIP] Column 'auctions.$columnName' already exists\n";
            }
        } catch (Exception $e) {
            $error = "Failed to add auctions.$columnName: " . $e->getMessage();
            echo "[ERROR] $error\n";
            $errors[] = $error;
        }
    }
    
    // =====================================================
    // 2. ENSURE AUCTION_IMAGES TABLE HAS CAPTION COLUMN  
    // =====================================================
    
    echo "\n=== STEP 2: Ensuring auction_images.caption column ===\n";
    
    try {
        $checkQuery = "SHOW COLUMNS FROM auction_images LIKE 'caption'";
        $stmt = $db->prepare($checkQuery);
        $stmt->execute();
        $captionExists = $stmt->fetch();
        
        if (!$captionExists) {
            $alterQuery = "ALTER TABLE auction_images ADD COLUMN caption VARCHAR(255) NULL AFTER image_path";
            $db->exec($alterQuery);
            echo "[✓] Added auction_images.caption column\n";
            $changes[] = "Added auction_images.caption column";
        } else {
            echo "[SKIP] auction_images.caption column already exists\n";
        }
    } catch (Exception $e) {
        $error = "Failed to add auction_images.caption: " . $e->getMessage();
        echo "[ERROR] $error\n";
        $errors[] = $error;
    }
    
    // =====================================================
    // 3. ENSURE AUCTION_METADATA TABLE EXISTS
    // =====================================================
    
    echo "\n=== STEP 3: Ensuring auction_metadata table ===\n";
    
    try {
        $checkQuery = "SHOW TABLES LIKE 'auction_metadata'";
        $stmt = $db->prepare($checkQuery);
        $stmt->execute();
        $tableExists = $stmt->fetch();
        
        if (!$tableExists) {
            $createQuery = "CREATE TABLE auction_metadata (
                id INT AUTO_INCREMENT PRIMARY KEY,
                auction_id INT NOT NULL,
                field_name VARCHAR(100) NOT NULL,
                field_value TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_auction_field (auction_id, field_name),
                INDEX idx_auction (auction_id),
                INDEX idx_field (field_name),
                FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $db->exec($createQuery);
            echo "[✓] Created auction_metadata table\n";
            $changes[] = "Created auction_metadata table";
        } else {
            echo "[SKIP] auction_metadata table already exists\n";
        }
    } catch (Exception $e) {
        $error = "Failed to create auction_metadata table: " . $e->getMessage();
        echo "[ERROR] $error\n";
        $errors[] = $error;
    }
    
    // =====================================================
    // 4. ADD PERFORMANCE INDEXES
    // =====================================================
    
    echo "\n=== STEP 4: Adding performance indexes ===\n";
    
    $indexes = [
        'auctions' => [
            'idx_status_end_time' => '(status, end_time)',
            'idx_featured' => '(featured)',
            'idx_category_status_end' => '(category_id, status, end_time)',
        ],
        'auction_images' => [
            'idx_auction_primary_sort' => '(auction_id, is_primary, sort_order)',
        ],
        'auction_metadata' => [
            'idx_auction_field' => '(auction_id, field_name)',
        ]
    ];
    
    foreach ($indexes as $tableName => $tableIndexes) {
        foreach ($tableIndexes as $indexName => $indexColumns) {
            try {
                // Check if index already exists
                $checkQuery = "SHOW INDEX FROM $tableName WHERE Key_name = ?";
                $stmt = $db->prepare($checkQuery);
                $stmt->execute([$indexName]);
                $indexExists = $stmt->fetch();
                
                if (!$indexExists) {
                    $createIndexQuery = "CREATE INDEX $indexName ON $tableName $indexColumns";
                    $db->exec($createIndexQuery);
                    echo "[✓] Added index '$tableName.$indexName'\n";
                    $changes[] = "Added index $tableName.$indexName";
                } else {
                    echo "[SKIP] Index '$tableName.$indexName' already exists\n";
                }
            } catch (Exception $e) {
                $warning = "Could not add index $tableName.$indexName: " . $e->getMessage();
                echo "[WARNING] $warning\n";
                $warnings[] = $warning;
            }
        }
    }
    
    // =====================================================
    // 5. CREATE SELLER_PROFILES SUPPORT TABLE (OPTIONAL)
    // =====================================================
    
    echo "\n=== STEP 5: Creating seller_profiles support table ===\n";
    
    try {
        $checkQuery = "SHOW TABLES LIKE 'seller_profiles'";
        $stmt = $db->prepare($checkQuery);
        $stmt->execute();
        $tableExists = $stmt->fetch();
        
        if (!$tableExists) {
            $createQuery = "CREATE TABLE seller_profiles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL UNIQUE,
                business_name VARCHAR(255) NULL,
                description TEXT NULL,
                logo_path VARCHAR(255) NULL,
                rating DECIMAL(3,2) DEFAULT 0.00,
                total_sales INT DEFAULT 0,
                active_auctions INT DEFAULT 0,
                response_rate DECIMAL(5,2) DEFAULT 100.00,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_rating (rating),
                INDEX idx_active (active_auctions)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $db->exec($createQuery);
            echo "[✓] Created seller_profiles table\n";
            $changes[] = "Created seller_profiles table";
        } else {
            echo "[SKIP] seller_profiles table already exists\n";
        }
    } catch (Exception $e) {
        $warning = "Could not create seller_profiles table: " . $e->getMessage();
        echo "[WARNING] $warning\n";
        $warnings[] = $warning;
    }
    
    // =====================================================
    // 6. UPDATE EXISTING DATA WITH SENSIBLE DEFAULTS
    // =====================================================
    
    echo "\n=== STEP 6: Setting sensible defaults for existing auctions ===\n";
    
    try {
        // Set pickup_available = 1 for all existing auctions
        $updateQuery = "UPDATE auctions SET pickup_available = 1 WHERE pickup_available IS NULL OR pickup_available = 0";
        $affectedRows = $db->exec($updateQuery);
        if ($affectedRows > 0) {
            echo "[✓] Updated $affectedRows auctions with pickup_available = 1\n";
            $changes[] = "Set pickup_available=1 for $affectedRows existing auctions";
        }
        
        // Set payment_deadline_days = 1 for auctions with null value
        $updateQuery = "UPDATE auctions SET payment_deadline_days = 1 WHERE payment_deadline_days IS NULL";
        $affectedRows = $db->exec($updateQuery);
        if ($affectedRows > 0) {
            echo "[✓] Updated $affectedRows auctions with payment_deadline_days = 1\n";
            $changes[] = "Set payment_deadline_days=1 for $affectedRows existing auctions";
        }
        
    } catch (Exception $e) {
        $warning = "Could not update existing auction defaults: " . $e->getMessage();
        echo "[WARNING] $warning\n";
        $warnings[] = $warning;
    }
    
    // =====================================================
    // MIGRATION COMPLETE - SHOW SUMMARY
    // =====================================================
    
    echo "\n=== MIGRATION COMPLETE ===\n";
    echo "Changes applied: " . count($changes) . "\n";
    echo "Warnings: " . count($warnings) . "\n";
    echo "Errors: " . count($errors) . "\n\n";
    
    if (!empty($changes)) {
        echo "CHANGES APPLIED:\n";
        foreach ($changes as $change) {
            echo "  • $change\n";
        }
        echo "\n";
    }
    
    if (!empty($warnings)) {
        echo "WARNINGS (non-critical):\n";
        foreach ($warnings as $warning) {
            echo "  • $warning\n";
        }
        echo "\n";
    }
    
    if (!empty($errors)) {
        echo "ERRORS (may affect functionality):\n";
        foreach ($errors as $error) {
            echo "  • $error\n";
        }
        echo "\n";
    }
    
    $status = empty($errors) ? 'SUCCESS' : 'COMPLETED WITH ERRORS';
    echo "Migration status: $status\n";
    
    if (empty($errors)) {
        echo "\n✅ Your database is now upgraded for the premium marketplace features!\n";
        echo "You can now use the enhanced auction system with rich metadata, improved images, and premium fields.\n";
    } else {
        echo "\n⚠️  Migration completed but with errors. Some features may not work correctly.\n";
        echo "Please check the errors above and contact support if needed.\n";
    }

} catch (Exception $e) {
    echo "\n[FATAL ERROR] Migration failed: " . $e->getMessage() . "\n";
    echo "Please check your database connection and permissions.\n";
    exit(1);
}

echo "</pre>\n";
echo "<p><strong>Migration process completed.</strong></p>\n";
echo "<p><a href='index.php'>← Back to homepage</a> | <a href='admin.php'>Go to admin</a></p>\n";