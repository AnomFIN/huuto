<?php
/**
 * Create SQLite database with sample data for development
 */

try {
    $pdo = new PDO('sqlite:' . __DIR__ . '/test.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create tables
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY,
            name TEXT NOT NULL,
            slug TEXT,
            parent_id INTEGER DEFAULT NULL,
            sort_order INTEGER DEFAULT 0
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY,
            username TEXT NOT NULL UNIQUE,
            email TEXT,
            password_hash TEXT,
            full_name TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS bids (
            id INTEGER PRIMARY KEY,
            auction_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (auction_id) REFERENCES auctions(id),
            FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS watchlist (
            id INTEGER PRIMARY KEY,
            auction_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (auction_id) REFERENCES auctions(id),
            FOREIGN KEY (user_id) REFERENCES users(id),
            UNIQUE(auction_id, user_id)
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS auctions (
            id INTEGER PRIMARY KEY,
            title TEXT NOT NULL,
            description TEXT,
            location TEXT NOT NULL,
            category_id INTEGER,
            end_time DATETIME,
            current_price DECIMAL(10,2),
            starting_price DECIMAL(10,2),
            buy_now_price DECIMAL(10,2),
            views INTEGER DEFAULT 0,
            primary_image TEXT,
            is_featured INTEGER DEFAULT 0,
            featured INTEGER DEFAULT 0,
            status TEXT DEFAULT 'active',
            user_id INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Insert sample categories
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO categories (id, name, slug) VALUES (?, ?, ?)");
    $categories = [
        [1, 'Antiikki', 'antiikki'],
        [2, 'Huonekalut', 'huonekalut'], 
        [3, 'Urheilu', 'urheilu'],
        [4, 'Kodinkoneet', 'kodinkoneet'],
        [5, 'Pelit', 'pelit'],
        [6, 'Taide', 'taide'],
        [7, 'Keräily', 'keraily'],
        [8, 'Elektroniikka', 'elektroniikka'],
        [9, 'Vaatteet', 'vaatteet'],
        [10, 'Autot', 'autot']
    ];
    
    foreach ($categories as $cat) {
        $stmt->execute($cat);
    }
    
    // Insert sample users first
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO users (id, username, email, full_name) VALUES (?, ?, ?, ?)");
    $users = [
        [1, 'demo_user', 'demo@example.com', 'Demo User'],
        [2, 'seller1', 'seller1@example.com', 'Myyjä Yksi'],
        [3, 'collector', 'collector@example.com', 'Keräilijä']
    ];
    
    foreach ($users as $user) {
        $stmt->execute($user);
    }
    
    // Insert sample auctions (without bid_count)  
    $currentTime = time();
    $auctions = [
        [1, 'Vintage kello 1970-luvulta', 'Kaunis vintage kello hyväkuntoisena. Toimii normaalisti.', 'Helsinki', 1, date('Y-m-d H:i:s', $currentTime + 3600), 45.50, 15.00, null, 125, '/uploads/sample1.jpg', 0, 0, 'active', 1],
        [2, 'Sohva 3-paikkainen, hyvä kunto', 'Mukava sohva olohuoneeseen. Ei lampunpalamia.', 'Tampere', 2, date('Y-m-d H:i:s', $currentTime + 7200), 125.00, 80.00, 200.00, 89, '/uploads/sample2.jpg', 0, 0, 'active', 1],
        [3, 'Polkupyörä 26" miesten malli', 'Hyväkuntoinen polkupyörä, käytetty vähän.', 'Turku', 3, date('Y-m-d H:i:s', $currentTime + 5400), 65.00, 30.00, 120.00, 200, '/uploads/sample3.jpg', 0, 0, 'active', 2],
        [4, 'Kahvinkeitin Moccamaster', 'Toimiva kahvinkeitin, hyvässä kunnossa.', 'Espoo', 4, date('Y-m-d H:i:s', $currentTime + 1800), 89.50, 40.00, 150.00, 67, '/uploads/sample4.jpg', 0, 0, 'active', 2],
        [5, 'Pelikonsolipaketti + pelejä', 'PlayStation 4 + 10 peliä. Kaikki toimii.', 'Oulu', 5, date('Y-m-d H:i:s', $currentTime + 2700), 156.00, 75.00, 300.00, 340, '/uploads/sample5.jpg', 1, 1, 'active', 3],
        [6, 'Taulumaalaus 1800-luku', 'Harvinainen taulu tuntemattomalta taiteilijalta.', 'Helsinki', 6, date('Y-m-d H:i:s', $currentTime + 9000), 450.00, 200.00, 800.00, 89, '/uploads/sample6.jpg', 1, 1, 'active', 3],
        [7, 'Keräilypostikortteja 1950-60', 'Kaunis kokoelma vanhoja postikortteja.', 'Lahti', 7, date('Y-m-d H:i:s', $currentTime + 12600), 23.50, 10.00, 50.00, 45, '/uploads/sample7.jpg', 1, 1, 'active', 1]
    ];
    
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO auctions (id, title, description, location, category_id, end_time, current_price, starting_price, buy_now_price, views, primary_image, is_featured, featured, status, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($auctions as $auction) {
        $stmt->execute($auction);
    }
    
    // Insert sample bids
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO bids (id, auction_id, user_id, amount) VALUES (?, ?, ?, ?)");
    $bids = [
        [1, 1, 2, 20.00], [2, 1, 3, 35.00], [3, 1, 2, 45.50], // 3 bids for auction 1
        [4, 2, 1, 90.00], [5, 2, 3, 110.00], [6, 2, 1, 125.00], // 3 bids for auction 2
        [7, 3, 2, 35.00], [8, 3, 1, 50.00], [9, 3, 3, 65.00], // 3 bids for auction 3
        [10, 4, 3, 50.00], [11, 4, 1, 75.00], [12, 4, 2, 89.50], // 3 bids for auction 4
        [13, 5, 1, 80.00], [14, 5, 2, 120.00], [15, 5, 3, 156.00], // 3 bids for auction 5
        [16, 6, 2, 220.00], [17, 6, 1, 350.00], [18, 6, 3, 450.00], // 3 bids for auction 6
        [19, 7, 3, 15.00], [20, 7, 1, 23.50] // 2 bids for auction 7
    ];
    
    foreach ($bids as $bid) {
        $stmt->execute($bid);
    }
    
    // Insert sample watchlist items
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO watchlist (id, auction_id, user_id) VALUES (?, ?, ?)");
    $watchlist = [
        [1, 1, 2], [2, 1, 3], // 2 watchers for auction 1
        [3, 2, 3], // 1 watcher for auction 2
        [4, 3, 1], [5, 3, 2], [6, 3, 3], // 3 watchers for auction 3
        [7, 5, 1], [8, 5, 2], // 2 watchers for auction 5
        [9, 6, 1] // 1 watcher for auction 6
    ];
    
    foreach ($watchlist as $watch) {
        $stmt->execute($watch);
    }
    
    echo "SQLite development database created successfully!\n";
    echo "Database file: " . __DIR__ . '/test.db' . "\n";
    echo "Auctions created: " . count($auctions) . "\n";
    
} catch (Exception $e) {
    echo "Error creating SQLite database: " . $e->getMessage() . "\n";
}