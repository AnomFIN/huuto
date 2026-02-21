<?php
// Fix missing categories and users for auctions
require_once __DIR__ . '/bootstrap.php';

try {
    $db = db();
    
    echo "=== Fixing missing data ===\n";
    
    // First check if user id=2 exists
    $stmt = $db->prepare("SELECT id FROM users WHERE id = 2");
    $stmt->execute();
    $userExists = $stmt->fetch();
    
    if (!$userExists) {
        echo "Creating user id=2...\n";
        $stmt = $db->prepare("INSERT INTO users (id, username, email, password_hash, full_name) VALUES (2, ?, ?, ?, ?)");
        $stmt->execute([
            'demolisher',  
            'demo@huuto247.fi',
            password_hash('demo123', PASSWORD_DEFAULT),
            'Demo Myyjä'
        ]);
        echo "✓ User created\n";
    } else {
        echo "✓ User id=2 already exists\n";
    }
    
    // Get max category_id from auctions
    $stmt = $db->query("SELECT MAX(category_id) as max_cat FROM auctions");
    $maxCategoryId = $stmt->fetch()['max_cat'];
    echo "Max category_id in auctions: $maxCategoryId\n";
    
    // Create categories 1-29 if they don't exist
    $categories = [
        1 => 'Keräily ja antiikki',
        2 => 'Ajoneuvot',
        3 => 'Kodin tavarat',
        4 => 'Vaatteet ja asusteet', 
        5 => 'Elektroniikka',
        6 => 'Urheilu ja kunto',
        7 => 'Kauneus ja terveys',
        8 => 'Autotarvikkeet',
        9 => 'Kotieläinten tavarat',
        10 => 'Työkalut',
        11 => 'Työkoneet',
        12 => 'Kiinteistöt',
        13 => 'Rakentaminen',
        14 => 'Puutarha',
        15 => 'Käsityöt',
        16 => 'Musiikki',
        17 => 'Taide',
        18 => 'Kirjat',
        19 => 'Lelut ja pelit',
        20 => 'Matkailu',
        21 => 'Kulttuuri',
        22 => 'Ruoka ja juoma',
        23 => 'Tekstiilit',
        24 => 'Veneet ja moottoriajoneuvot',
        25 => 'Maatalous',
        26 => 'Ammatilliset tuotteet',
        27 => 'Yrityksen tavarat',
        28 => 'Erikoiskalusto',
        29 => 'Muut'
    ];
    
    for ($i = 1; $i <= $maxCategoryId; $i++) {
        $stmt = $db->prepare("SELECT id FROM categories WHERE id = ?");
        $stmt->execute([$i]);
        $categoryExists = $stmt->fetch();
        
        if (!$categoryExists) {
            $categoryName = $categories[$i] ?? "Kategoria $i";
            $slug = strtolower(str_replace([' ', 'ä', 'ö', 'å'], ['-', 'a', 'o', 'a'], $categoryName));
            
            $stmt = $db->prepare("INSERT INTO categories (id, name, slug) VALUES (?, ?, ?)");
            $stmt->execute([$i, $categoryName, $slug]);
            echo "✓ Created category $i: $categoryName\n";
        }
    }
    
    // Test that queries now work
    echo "\n=== Testing queries ===\n";
    
    $auctionModel = new Auction();
    $popular = $auctionModel->getPopularAuctions(5);
    echo "Popular auctions found: " . count($popular) . "\n";
    
    $closing = $auctionModel->getClosingSoonAuctions(5);  
    echo "Closing soon auctions found: " . count($closing) . "\n";
    
    if (count($popular) > 0) {
        echo "First auction: " . $popular[0]['title'] . " - €" . $popular[0]['current_price'] . "\n";
    }
    
    echo "\n✅ Fix completed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>