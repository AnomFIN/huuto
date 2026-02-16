<?php
// Quick test for Auction class
require_once __DIR__ . '/bootstrap.php';

echo "Testing Auction class...<br>";

try {
    $auction = new Auction();
    echo "✓ Auction class created<br>";
    
    // Test if the method exists
    if (method_exists($auction, 'getAllAuctions')) {
        echo "✓ getAllAuctions method exists<br>";
        
        $auctions = $auction->getAllAuctions();
        echo "✓ getAllAuctions executed, found " . count($auctions) . " auctions<br>";
    } else {
        echo "✗ getAllAuctions method NOT found<br>";
        
        // Show all available methods
        $methods = get_class_methods($auction);
        echo "Available methods: " . implode(', ', $methods) . "<br>";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
}

echo "Test complete.";
?>