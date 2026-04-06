<?php
/**
 * Debug script to test database connection and basic functionality
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>Debug Test</title></head><body>";
echo "<h1>Huuto247 Debug Test</h1>";

// Test 1: Bootstrap loading
echo "<h2>1. Bootstrap Test</h2>";
try {
    require_once __DIR__ . '/bootstrap.php';
    echo "✅ Bootstrap loaded successfully<br>";
} catch (Exception $e) {
    echo "❌ Bootstrap error: " . $e->getMessage() . "<br>";
    exit;
}

// Test 2: Database connection
echo "<h2>2. Database Connection Test</h2>";
try {
    $db = Database::getInstance()->getConnection();
    echo "✅ Database connection successful<br>";
    
    // Test simple query
    $stmt = $db->query("SELECT COUNT(*) as count FROM auctions");
    $result = $stmt->fetch();
    echo "✅ Found {$result['count']} auctions in database<br>";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test 3: Category model
echo "<h2>3. Category Model Test</h2>";
try {
    $categoryModel = new Category();
    $categories = $categoryModel->getAllCategories();
    echo "✅ Found " . count($categories) . " categories<br>";
} catch (Exception $e) {
    echo "❌ Category model error: " . $e->getMessage() . "<br>";
}

// Test 4: Auction model
echo "<h2>4. Auction Model Test</h2>";
try {
    $auctionModel = new Auction();
    $popular = $auctionModel->getPopularAuctions(5);
    echo "✅ Found " . count($popular) . " popular auctions<br>";
    
    $featured = $auctionModel->getFeaturedAuctions(5);
    echo "✅ Found " . count($featured) . " featured auctions<br>";
} catch (Exception $e) {
    echo "❌ Auction model error: " . $e->getMessage() . "<br>";
}

// Test 5: Premium fields
echo "<h2>5. Premium Fields Test</h2>";
try {
    $stmt = $db->query("SHOW COLUMNS FROM auctions WHERE Field IN ('featured', 'short_summary', 'condition_grade')");
    $fields = $stmt->fetchAll();
    if (count($fields) >= 3) {
        echo "✅ Premium fields exist in database<br>";
        foreach ($fields as $field) {
            echo "- {$field['Field']}: {$field['Type']}<br>";
        }
    } else {
        echo "❌ Premium fields missing from database<br>";
    }
} catch (Exception $e) {
    echo "❌ Premium field check error: " . $e->getMessage() . "<br>";
}

echo "</body></html>";
?>