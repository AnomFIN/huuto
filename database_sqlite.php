<?php
/**
 * Väliaikainen SQLite-tietokanta testikäyttöön
 */

// Create SQLite database with sample data for testing
try {
    $pdo = new PDO('sqlite:' . __DIR__ . '/test.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create tables
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY,
            name TEXT NOT NULL,
            parent_id INTEGER DEFAULT NULL
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS auctions (
            id INTEGER PRIMARY KEY,
            title TEXT NOT NULL,
            location TEXT NOT NULL,
            category_id INTEGER,
            end_time DATETIME,
            current_price DECIMAL(10,2),
            starting_price DECIMAL(10,2),
            buy_now_price DECIMAL(10,2),
            bid_count INTEGER DEFAULT 0,
            primary_image TEXT,
            is_featured INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Insert sample categories
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO categories (id, name) VALUES (?, ?)");
    $categories = [
        [1, 'Antiikki'],
        [2, 'Huonekalut'], 
        [3, 'Urheilu'],
        [4, 'Kodinkoneet'],
        [5, 'Pelit'],
        [6, 'Taide'],
        [7, 'Keräily'],
        [8, 'Elektroniikka'],
        [9, 'Vaatteet'],
        [10, 'Autot']
    ];
    
    foreach ($categories as $cat) {
        $stmt->execute($cat);
    }
    
    // Insert sample auctions
    $currentTime = time();
    $auctions = [
        [1, 'Vintage kello 1970-luvulta', 'Helsinki', 1, date('Y-m-d H:i:s', $currentTime + 3600), 45.50, 15.00, null, 8, '/uploads/sample1.jpg', 0],
        [2, 'Sohva 3-paikkainen, hyvä kunto', 'Tampere', 2, date('Y-m-d H:i:s', $currentTime + 7200), 125.00, 80.00, 200.00, 3, '/uploads/sample2.jpg', 0],
        [3, 'Polkupyörä 26" miesten malli', 'Turku', 3, date('Y-m-d H:i:s', $currentTime + 5400), 65.00, 30.00, 120.00, 12, '/uploads/sample3.jpg', 0],
        [4, 'Kahvinkeitin Moccamaster', 'Espoo', 4, date('Y-m-d H:i:s', $currentTime + 1800), 89.50, 40.00, 150.00, 15, '/uploads/sample4.jpg', 0],
        [5, 'Pelikonsolipaketti + pelejä', 'Oulu', 5, date('Y-m-d H:i:s', $currentTime + 2700), 156.00, 75.00, 300.00, 23, '/uploads/sample5.jpg', 0],
        [6, 'Taulumaalaus 1800-luku', 'Helsinki', 6, date('Y-m-d H:i:s', $currentTime + 9000), 450.00, 200.00, 800.00, 7, '/uploads/sample6.jpg', 1],
        [7, 'Keräilypostikortteja 1950-60', 'Lahti', 7, date('Y-m-d H:i:s', $currentTime + 12600), 23.50, 10.00, 50.00, 5, '/uploads/sample7.jpg', 1]
    ];
    
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO auctions (id, title, location, category_id, end_time, current_price, starting_price, buy_now_price, bid_count, primary_image, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($auctions as $auction) {
        $stmt->execute($auction);
    }
    
    echo "SQLite test database created successfully!\n";
    echo "Database file: " . __DIR__ . '/test.db' . "\n";
    
} catch (Exception $e) {
    echo "Error creating SQLite database: " . $e->getMessage() . "\n";
}