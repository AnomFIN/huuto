<?php
// Debug version of admin.php to identify 500 error
echo "Debug: Admin.php loading...<br>";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/tmp/php_errors.log');

echo "Debug: Error reporting enabled<br>";

// Check if installation is complete
$lockFile = __DIR__ . '/config/installed.lock';
echo "Debug: Checking lock file: $lockFile<br>";

if (!file_exists($lockFile)) {
    echo "Debug: Lock file not found, redirecting to asennus.php<br>";
    // Don't redirect for debug
    // header('Location: /asennus.php');
    // exit;
} else {
    echo "Debug: Lock file found<br>";
}

echo "Debug: Loading config.php<br>";
if (file_exists(__DIR__ . '/config/config.php')) {
    echo "Debug: config.php exists<br>";
    try {
        require_once __DIR__ . '/config/config.php';
        echo "Debug: config.php loaded successfully<br>";
    } catch (Exception $e) {
        echo "Debug: Error loading config.php: " . $e->getMessage() . "<br>";
        die();
    }
} else {
    echo "Debug: config.php NOT FOUND<br>";
    die();
}

echo "Debug: Loading Database.php<br>";
if (file_exists(__DIR__ . '/src/models/Database.php')) {
    echo "Debug: Database.php exists<br>";
    try {
        require_once __DIR__ . '/src/models/Database.php';
        echo "Debug: Database.php loaded successfully<br>";
    } catch (Exception $e) {
        echo "Debug: Error loading Database.php: " . $e->getMessage() . "<br>";
        die();
    }
} else {
    echo "Debug: Database.php NOT FOUND<br>";
    die();
}

echo "Debug: Loading Auction.php<br>";
if (file_exists(__DIR__ . '/src/models/Auction.php')) {
    echo "Debug: Auction.php exists<br>";
    try {
        require_once __DIR__ . '/src/models/Auction.php';
        echo "Debug: Auction.php loaded successfully<br>";
    } catch (Exception $e) {
        echo "Debug: Error loading Auction.php: " . $e->getMessage() . "<br>";
        die();
    }
} else {
    echo "Debug: Auction.php NOT FOUND<br>";
    die();
}

echo "Debug: Creating Auction model<br>";
try {
    $auctionModel = new Auction();
    echo "Debug: Auction model created successfully<br>";
} catch (Exception $e) {
    echo "Debug: Error creating Auction model: " . $e->getMessage() . "<br>";
    die();
}

echo "Debug: Getting all auctions<br>";
try {
    $auctions = $auctionModel->getAllAuctions();
    echo "Debug: Found " . count($auctions) . " auctions<br>";
} catch (Exception $e) {
    echo "Debug: Error getting auctions: " . $e->getMessage() . "<br>";
    die();
}

echo "Debug: All checks passed! The issue must be elsewhere.<br>";
echo "Debug: admin.php should work normally now.<br>";
?>