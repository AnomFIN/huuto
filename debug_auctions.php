<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Huutokohteet Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .auction { border: 1px solid #ddd; margin: 10px 0; padding: 15px; border-radius: 8px; }
        .price { font-weight: bold; color: #2563eb; font-size: 1.2em; }
        .category { color: #666; font-size: 0.9em; }
        .status { padding: 4px 8px; border-radius: 4px; font-size: 0.8em; }
        .active { background: #dcfce7; color: #166534; }
        h2 { color: #333; border-bottom: 2px solid #2563eb; padding-bottom: 5px; }
        .error { background: #fee2e2; color: #7f1d1d; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .success { background: #dcfce7; color: #166534; padding: 10px; border-radius: 4px; margin: 10px 0; }
    </style>
</head>
<body>
<h1>🔧 Huutokohteet Debug</h1>

<?php
require_once __DIR__ . '/bootstrap.php';

try {
    echo "<div class='success'>✅ Bootstrap onnistui</div>";
    
    $db = db();
    echo "<div class='success'>✅ Database yhdistetty</div>";
    
    // Test basic auction query without joins
    echo "<h2>1. Basic Auction Query (no joins)</h2>";
    $stmt = $db->query("SELECT id, title, current_price, category_id, user_id, status, end_time FROM auctions WHERE status = 'active' ORDER BY id LIMIT 10");
    $basicAuctions = $stmt->fetchAll();
    
    if (empty($basicAuctions)) {
        echo "<div class='error'>❌ Ei aktiivisia huutokohteita löytynyt</div>";
    } else {
        echo "<div class='success'>✅ Löytyi " . count($basicAuctions) . " aktiivista kohde</div>";
        
        foreach($basicAuctions as $auction) {
            echo "<div class='auction'>";
            echo "<h3>" . htmlspecialchars($auction['title']) . "</h3>";
            echo "<div class='price'>€" . number_format($auction['current_price'], 2) . "</div>";
            echo "<div class='category'>Kategoria ID: " . $auction['category_id'] . " | Myyjä ID: " . $auction['user_id'] . "</div>";
            echo "<span class='status active'>Aktiivinen</span>";
            echo "</div>";
        }
    }
    
    // Test categories table
    echo "<h2>2. Categories Table Check</h2>";
    try {
        $stmt = $db->query("SELECT COUNT(*) as count FROM categories");
        $categoryCount = $stmt->fetch()['count'];
        
        if ($categoryCount == 0) {
            echo "<div class='error'>❌ Categories-taulu on tyhjä. Suorita fix_data.sql!</div>";
        } else {
            echo "<div class='success'>✅ Categories-taulussa on $categoryCount kategoriaa</div>";
            
            // Show sample categories
            $stmt = $db->query("SELECT * FROM categories LIMIT 5");
            while($cat = $stmt->fetch()) {
                echo "<div>ID: {$cat['id']} - {$cat['name']}</div>";
            }
        }
    } catch (Exception $e) {
        echo "<div class='error'>❌ Categories-taulu ei ole olemassa: " . $e->getMessage() . "</div>";
    }
    
    // Test users table
    echo "<h2>3. Users Table Check</h2>";
    try {
        $stmt = $db->query("SELECT COUNT(*) as count FROM users");
        $userCount = $stmt->fetch()['count'];
        
        if ($userCount == 0) {
            echo "<div class='error'>❌ Users-taulu on tyhjä. Suorita fix_data.sql!</div>";
        } else {
            echo "<div class='success'>✅ Users-taulussa on $userCount käyttäjää</div>";
            
            // Check if user id=2 exists (used in auctions.sql)
            $stmt = $db->query("SELECT * FROM users WHERE id = 2");
            $user2 = $stmt->fetch();
            if ($user2) {
                echo "<div class='success'>✅ Käyttäjä ID=2 löytyy: {$user2['username']}</div>";
            } else {
                echo "<div class='error'>❌ Käyttäjä ID=2 puuttuu (auctions.sql viittaa tähän)</div>";
            }
        }
    } catch (Exception $e) {
        echo "<div class='error'>❌ Users-taulu ei ole olemassa: " . $e->getMessage() . "</div>";
    }
    
    // Test Auction model queries  
    echo "<h2>4. Auction Model Test</h2>";
    try {
        $auctionModel = new Auction();
        
        echo "<h3>Popular Auctions:</h3>";
        $popularAuctions = $auctionModel->getPopularAuctions(5);
        
        if (empty($popularAuctions)) {
            echo "<div class='error'>❌ Ei suosittuja kohteita löytynyt</div>";
        } else {
            echo "<div class='success'>✅ Löytyi " . count($popularAuctions) . " suosittua kohdetta</div>";
            
            foreach($popularAuctions as $auction) {
                echo "<div class='auction'>";
                echo "<h4>" . htmlspecialchars($auction['title']) . "</h4>";
                echo "<div class='price'>€" . number_format($auction['current_price'], 2) . "</div>";
                echo "<div class='category'>Kategoria: " . htmlspecialchars($auction['category_name']) . " | Myyjä: " . htmlspecialchars($auction['seller_username']) . "</div>";
                echo "<div>Tarjoulat: " . ($auction['bid_count'] ?? 0) . " | Katselut: " . ($auction['views'] ?? 0) . "</div>";
                echo "</div>";
            }
        }
        
        echo "<h3>Closing Soon Auctions:</h3>";
        $closingAuctions = $auctionModel->getClosingSoonAuctions(5);
        
        if (empty($closingAuctions)) {
            echo "<div class='info' style='background: #fef3c7; color: #92400e; padding: 10px; border-radius: 4px;'>ℹ️ Ei kohteita päättymässä seuraavaan 24h</div>";
        } else {
            echo "<div class='success'>✅ Löytyi " . count($closingAuctions) . " pian päättyvää kohdetta</div>";
            
            foreach($closingAuctions as $auction) {
                echo "<div class='auction'>";
                echo "<h4>" . htmlspecialchars($auction['title']) . "</h4>";
                echo "<div class='price'>€" . number_format($auction['current_price'], 2) . "</div>";
                echo "<div>Päättyy: " . date('d.m.Y H:i', strtotime($auction['end_time'])) . "</div>";
                echo "</div>";
            }
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Auction model virhe: " . $e->getMessage() . "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Kriittinen virhe: " . $e->getMessage() . "</div>";
}
?>

<hr>
<h2>🛠️ Korjausohjeet</h2>
<p>Jos huutokohteet eivät näy, suorita nämä toimet:</p>
<ol>
    <li><strong>Suorita SQL-korjaus:</strong> Aja <code>fix_data.sql</code> tiedosto tietokannassa</li>
    <li><strong>Tai PHP-korjaus:</strong> <code>php fix_missing_data.php</code></li>
    <li><strong>Tarkista tietokantayhteys:</strong> config/database.php asetukset</li>
    <li><strong>Päivitä sivu:</strong> <a href="">Päivitä tämä sivu</a> korjausten jälkeen</li>
</ol>

</body>
</html>