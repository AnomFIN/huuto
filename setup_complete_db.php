<?php
// Complete database setup for Huuto247.fi
require_once __DIR__ . '/bootstrap.php';

try {
    $db = db();
    echo "<!DOCTYPE html><html><head><title>Database Setup</title><style>body{font-family:Arial,sans-serif;margin:20px}.success{background:#dcfce7;color:#166534;padding:10px;margin:5px 0;border-radius:4px}.error{background:#fee2e2;color:#7f1d1d;padding:10px;margin:5px 0;border-radius:4px}</style></head><body>";
    echo "<h1>🗄️ Huuto247.fi Database Setup</h1>";
    
    // Step 1: Create tables from schema
    echo "<h2>1. Creating database schema...</h2>";
    $schemaFile = __DIR__ . '/database/schema.sql';
    if (file_exists($schemaFile)) {
        $schema = file_get_contents($schemaFile);
        $schema = str_replace('USE huuto_db;', '', $schema); // Remove USE statement
        $statements = explode(';', $schema);
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                try {
                    $db->exec($statement);
                    if (strpos($statement, 'CREATE TABLE') !== false) {
                        preg_match('/CREATE TABLE.*?`?(\w+)`?/i', $statement, $matches);
                        if (!empty($matches[1])) {
                            echo "<div class='success'>✅ Created table: {$matches[1]}</div>";
                        }
                    }
                } catch (PDOException $e) {
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
                    }
                }
            }
        }
        echo "<div class='success'>✅ Schema loaded successfully</div>";
    } else {
        echo "<div class='error'>❌ Schema file not found: $schemaFile</div>";
    }
    
    // Step 2: Add missing categories
    echo "<h2>2. Adding categories...</h2>";
    $categories = [
        ['id' => 1, 'name' => 'Keräily ja antiikki', 'slug' => 'keraily-ja-antiikki'],
        ['id' => 2, 'name' => 'Ajoneuvot', 'slug' => 'ajoneuvot'],
        ['id' => 3, 'name' => 'Kodin tavarat', 'slug' => 'kodin-tavarat'],
        ['id' => 4, 'name' => 'Vaatteet ja asusteet', 'slug' => 'vaatteet-ja-asusteet'],
        ['id' => 5, 'name' => 'Elektroniikka', 'slug' => 'elektroniikka'],
        ['id' => 6, 'name' => 'Urheilu ja kunto', 'slug' => 'urheilu-ja-kunto'],
        ['id' => 7, 'name' => 'Kauneus ja terveys', 'slug' => 'kauneus-ja-terveys'],
        ['id' => 8, 'name' => 'Autotarvikkeet', 'slug' => 'autotarvikkeet'],
        ['id' => 9, 'name' => 'Kotieläinten tavarat', 'slug' => 'kotielainten-tavarat'],
        ['id' => 10, 'name' => 'Työkalut', 'slug' => 'tyokalut'],
        ['id' => 11, 'name' => 'Työkoneet', 'slug' => 'tyokoneet'],
        ['id' => 12, 'name' => 'Kiinteistöt', 'slug' => 'kiinteistot'],
        ['id' => 13, 'name' => 'Rakentaminen', 'slug' => 'rakentaminen'],
        ['id' => 14, 'name' => 'Puutarha', 'slug' => 'puutarha'],
        ['id' => 15, 'name' => 'Käsityöt', 'slug' => 'kasityot'],
        ['id' => 16, 'name' => 'Musiikki', 'slug' => 'musiikki'],
        ['id' => 17, 'name' => 'Taide', 'slug' => 'taide'],
        ['id' => 18, 'name' => 'Kirjat', 'slug' => 'kirjat'],
        ['id' => 19, 'name' => 'Lelut ja pelit', 'slug' => 'lelut-ja-pelit'],
        ['id' => 20, 'name' => 'Matkailu', 'slug' => 'matkailu'],
        ['id' => 21, 'name' => 'Kulttuuri', 'slug' => 'kulttuuri'],
        ['id' => 22, 'name' => 'Ruoka ja juoma', 'slug' => 'ruoka-ja-juoma'],
        ['id' => 23, 'name' => 'Tekstiilit', 'slug' => 'tekstiilit'],
        ['id' => 24, 'name' => 'Veneet ja vesikulkuneuvot', 'slug' => 'veneet-ja-vesikulkuneuvot'],
        ['id' => 25, 'name' => 'Maatalous', 'slug' => 'maatalous'],
        ['id' => 26, 'name' => 'Ammatilliset tuotteet', 'slug' => 'ammatilliset-tuotteet'],
        ['id' => 27, 'name' => 'Yrityksen tavarat', 'slug' => 'yrityksen-tavarat'],
        ['id' => 28, 'name' => 'Erikoiskalusto', 'slug' => 'erikoiskalusto'],
        ['id' => 29, 'name' => 'Muut', 'slug' => 'muut']
    ];
    
    foreach ($categories as $cat) {
        $stmt = $db->prepare("INSERT IGNORE INTO categories (id, name, slug) VALUES (?, ?, ?)");
        $result = $stmt->execute([$cat['id'], $cat['name'], $cat['slug']]);
        if ($stmt->rowCount() > 0) {
            echo "<div class='success'>✅ Added category: {$cat['name']}</div>";
        }
    }
    
    // Step 3: Add demo user
    echo "<h2>3. Adding demo user...</h2>";
    $stmt = $db->prepare("INSERT IGNORE INTO users (id, username, email, password_hash, full_name, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $result = $stmt->execute([
        2,
        'demolisher',
        'demo@huuto247.fi', 
        password_hash('demo123', PASSWORD_DEFAULT),
        'Demo Myyjä'
    ]);
    
    if ($stmt->rowCount() > 0) {
        echo "<div class='success'>✅ Added demo user (username: demolisher, password: demo123)</div>";
    } else {
        echo "<div class='success'>✅ Demo user already exists</div>";
    }
    
    // Step 4: Load auctions data if file exists
    echo "<h2>4. Loading auction data...</h2>";
    $auctionsFile = __DIR__ . '/auctions.sql';
    if (file_exists($auctionsFile)) {
        // Check if auctions already exist
        $stmt = $db->query("SELECT COUNT(*) as count FROM auctions");
        $auctionCount = $stmt->fetch()['count'];
        
        if ($auctionCount == 0) {
            $auctions = file_get_contents($auctionsFile);
            // Extract just the INSERT statements
            preg_match('/INSERT INTO `auctions`.*?VALUES\s*(.*?);/s', $auctions, $matches);
            
            if (!empty($matches[1])) {
                try {
                    $insertSQL = "INSERT INTO auctions (id, user_id, category_id, title, description, starting_price, current_price, reserve_price, buy_now_price, bid_increment, start_time, end_time, status, views, location, condition_description, created_at, updated_at) VALUES " . $matches[1];
                    $db->exec($insertSQL);
                    
                    $stmt = $db->query("SELECT COUNT(*) as count FROM auctions");
                    $newCount = $stmt->fetch()['count'];
                    echo "<div class='success'>✅ Loaded $newCount auction records</div>";
                } catch (PDOException $e) {
                    echo "<div class='error'>❌ Error loading auctions: " . $e->getMessage() . "</div>";
                }
            }
        } else {
            echo "<div class='success'>✅ $auctionCount auctions already loaded</div>";
        }
    } else {
        echo "<div class='error'>⚠️ Auctions.sql file not found - no auction data loaded</div>";
    }
    
    // Step 5: Test the setup
    echo "<h2>5. Testing setup...</h2>";
    try {
        $auctionModel = new Auction();
        $popularAuctions = $auctionModel->getPopularAuctions(5);
        $closingAuctions = $auctionModel->getClosingSoonAuctions(5);
        
        echo "<div class='success'>✅ Found " . count($popularAuctions) . " popular auctions</div>";
        echo "<div class='success'>✅ Found " . count($closingAuctions) . " closing soon auctions</div>";
        
        if (!empty($popularAuctions)) {
            echo "<div class='success'>✅ Sample auction: " . htmlspecialchars($popularAuctions[0]['title']) . " - €" . number_format($popularAuctions[0]['current_price'], 2) . "</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Test failed: " . $e->getMessage() . "</div>";
    }
    
    echo "<h2>✅ Setup Complete!</h2>";
    echo "<p>Your Huuto247.fi database is now ready to use.</p>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ul>";
    echo "<li><a href='/'>Go to homepage</a> to see auctions</li>";
    echo "<li><a href='/debug_auctions.php'>Debug auctions</a> if there are issues</li>";
    echo "<li><a href='/admin.php'>Admin panel</a> to manage auctions</li>";
    echo "</ul>";
    
    echo "</body></html>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Database setup failed: " . $e->getMessage() . "</div>";
    echo "</body></html>";
}
?>