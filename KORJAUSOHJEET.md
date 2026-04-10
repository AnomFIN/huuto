# Diagnoosi ja korjaus: Huutokohteet eivät näy etusivulla

## Ongelma
Sivustolla on aktiivisia kohteita tietokannassa (30+ kpl), mutta etusivu näyttää tyhjää.

## Todennäköiset syyt ja korjaukset

### 1. "Sulkeutuu pian" -kohteet: Liian tiukka aikarajaus

**Ongelma:** `getClosingSoonAuctions()` näyttää vain kohteet, jotka päättyvät seuraavan 24 tunnin sisällä.

**Korjaus:** Muuta `/src/models/Auction.php` tiedostossa riviä ~133:

```php
// Vanha (liian tiukka)
AND a.end_time <= DATE_ADD(NOW(), INTERVAL 24 HOUR)

// Uusi (7 päivää)
AND a.end_time <= DATE_ADD(NOW(), INTERVAL 7 DAY)
```

### 2. Aikavyöhyke-ongelma MySQL vs PHP

**Ongelma:** MySQL NOW() ja PHP time() voivat olla eri aikavyöhykkeillä.

**Korjaus:** Lisää tietokantayhteyden pienemään `/app/config.php` tiedostoon:

```php
// Aseta aikavyöhyke MySQL:lle
date_default_timezone_set('Europe/Helsinki');
```

Ja `/src/models/Database.php` tiedostoon konstruktoriin:

```php
// Aseta MySQL aikavyöhyke yhteyden jälkeen
$this->pdo->exec("SET time_zone = '+03:00'");
```

### 3. Puuttuvat featured-kohteet

**Ongelma:** `getFeaturedAuctions()` vaatii `featured = 1`, mutta kaikki kohteet ovat `featured = 0`.

**Korjaus:** Aseta joitain kohteita featured-kohteiksi:

```sql
UPDATE auctions SET featured = 1 WHERE id IN (1, 7, 22, 28, 29) AND status = 'active';
```

### 4. Puuttuvat taulut tai tiedot

**Tarkista seuraavat taulut:**

```sql
-- Kategoriat
SELECT COUNT(*) FROM categories;

-- Käyttäjät  
SELECT COUNT(*) FROM users;

-- Tarjoukset (vaikuttaa "suosituihin")
SELECT COUNT(*) FROM bids;

-- Kuvat
SELECT COUNT(*) FROM auction_images;
```

### 5. Nopea korjaus: Lisää testimateriaalia

Suorita seuraavat SQL-komennot:

```sql
-- Aseta joitain kohteita featured-kohteiksi
UPDATE auctions SET featured = 1 WHERE id IN (1, 7, 22, 28, 29) AND status = 'active' AND end_time > NOW();

-- Lisää testimarjouksia "suosituille" kohteille
INSERT IGNORE INTO bids (auction_id, user_id, amount, created_at) VALUES
(1, 2, 25000, NOW()),
(1, 2, 28000, NOW()),
(1, 2, 29000, NOW()),
(7, 2, 300, NOW()),
(22, 2, 450, NOW()),
(28, 2, 220, NOW());

-- Varmista että kategoriataulut on olemassa
INSERT IGNORE INTO categories (id, name, slug) VALUES
(1, 'Vapaa-aika', 'vapaa-aika'),
(2, 'Rakentaminen', 'rakentaminen'),
(3, 'Sisustus', 'sisustus'),
(11, 'Työkoneet', 'tyokoneet'),
(8, 'Rakentaminen', 'rakentaminen');

-- Varmista että käyttäjätaulut on olemassa
INSERT IGNORE INTO users (id, username, email) VALUES
(2, 'testikayttaja', 'testi@example.com');
```

### 6. Debug-tiedosto

Luo tiedosto `/debug_homepage.php`:

```php
<?php
require_once __DIR__ . '/bootstrap.php';

echo "<h1>Homepage Debug</h1>";

try {
    $auctionModel = new Auction();
    
    echo "<h2>Database Test</h2>";
    $db = Database::getInstance()->getConnection();
    echo "Database connection: OK<br>";
    
    $totalActive = $db->query("SELECT COUNT(*) as count FROM auctions WHERE status = 'active'")->fetch()['count'];
    echo "Total active auctions: {$totalActive}<br>";
    
    $futureAuctions = $db->query("SELECT COUNT(*) as count FROM auctions WHERE status = 'active' AND end_time > NOW()")->fetch()['count'];
    echo "Future auctions: {$futureAuctions}<br>";
    
    echo "<h2>Method Results</h2>";
    
    $popular = $auctionModel->getPopularAuctions(5);
    echo "Popular auctions: " . count($popular) . "<br>";
    
    $closing = $auctionModel->getClosingSoonAuctions(5);
    echo "Closing soon: " . count($closing) . "<br>";
    
    $featured = $auctionModel->getFeaturedAuctions(5);
    echo "Featured auctions: " . count($featured) . "<br>";
    
    if (!empty($popular)) {
        echo "<h3>Sample Popular Auction:</h3>";
        $sample = $popular[0];
        echo "ID: {$sample['id']}<br>";
        echo "Title: {$sample['title']}<br>";
        echo "End time: {$sample['end_time']}<br>";
        echo "Status: {$sample['status']}<br>";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
?>
```

Käynnistä: `https://yourdomain.com/debug_homepage.php`

## Nopein ratkaisu

Jos haluad nopean korjauksen, suorita nämä SQL-komennot:

```sql
-- 1. Aseta kohteita featured-kohteiksi
UPDATE auctions SET featured = 1 WHERE id <= 5 AND status = 'active' AND end_time > NOW();

-- 2. Muuta "sulkeutuu pian" ajankohta 7 päivään
-- Muokkaa /src/models/Auction.php rivi ~133 muotoon:
-- AND a.end_time <= DATE_ADD(NOW(), INTERVAL 7 DAY)
```

Tämän jälkeen sivuston pitäisi näyttää kohteita normaalisti.