<?php
// Minimal test to find the actual error
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== TESTING index.php COMPONENTS ===\n";

echo "1. Loading config...\n";
try {
    require_once __DIR__ . '/app/config.php';
    echo "✅ Config OK\n";
} catch (Exception $e) {
    echo "❌ Config FAILED: " . $e->getMessage() . "\n";
    exit;
}

echo "2. Loading db...\n";
try {
    require_once __DIR__ . '/app/db.php';
    echo "✅ DB OK\n";
} catch (Exception $e) {
    echo "❌ DB FAILED: " . $e->getMessage() . "\n";
    exit;
}

echo "3. Testing database connection...\n";
try {
    $db = db();
    echo "✅ DB Connection OK\n";
} catch (Exception $e) {
    echo "❌ DB Connection FAILED: " . $e->getMessage() . "\n";
    exit;
}

echo "4. Loading Database model...\n";
try {
    require_once __DIR__ . '/src/models/Database.php';
    echo "✅ Database model OK\n";
} catch (Exception $e) {
    echo "❌ Database model FAILED: " . $e->getMessage() . "\n";
    exit;
}

echo "5. Testing Database model instance...\n";
try {
    $dbModel = Database::getInstance();
    echo "✅ Database model instance OK\n";
} catch (Exception $e) {
    echo "❌ Database model instance FAILED: " . $e->getMessage() . "\n";
    exit;
}

echo "6. Loading Auction model...\n";
try {
    require_once __DIR__ . '/src/models/Auction.php';
    echo "✅ Auction model loaded OK\n";
} catch (Exception $e) {
    echo "❌ Auction model load FAILED: " . $e->getMessage() . "\n";
    echo "Error details: " . $e->getFile() . " line " . $e->getLine() . "\n";
    exit;
} catch (ParseError $e) {
    echo "❌ Auction model SYNTAX ERROR: " . $e->getMessage() . "\n";
    echo "Error details: " . $e->getFile() . " line " . $e->getLine() . "\n";
    exit;
} catch (Error $e) {
    echo "❌ Auction model FATAL ERROR: " . $e->getMessage() . "\n";
    echo "Error details: " . $e->getFile() . " line " . $e->getLine() . "\n";
    exit;
}

echo "7. Creating Auction instance...\n";
try {
    $auctionModel = new Auction();
    echo "✅ Auction instance OK\n";
} catch (Exception $e) {
    echo "❌ Auction instance FAILED: " . $e->getMessage() . "\n";
    exit;
}

echo "8. Testing getPopularAuctions method...\n";
try {
    $popular = $auctionModel->getPopularAuctions(5);
    echo "✅ getPopularAuctions OK - found " . count($popular) . " auctions\n";
} catch (Exception $e) {
    echo "❌ getPopularAuctions FAILED: " . $e->getMessage() . "\n";
    exit;
}

echo "9. Loading Category model...\n";
try {
    require_once __DIR__ . '/src/models/Category.php';
    echo "✅ Category model OK\n";
} catch (Exception $e) {
    echo "❌ Category model FAILED: " . $e->getMessage() . "\n";
    exit;
}

echo "\n=== ALL TESTS PASSED ===\n";
echo "index.php should work now. If it doesn't, the error is in the HTML/template part.\n";
?>