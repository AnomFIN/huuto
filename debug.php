<?php
/**
 * DEBUG.PHP - Vianmääritystyökalu huuto247.fi
 * 
 * Käytä tätä sivua tarkistaaksesi:
 * - Tietokantayhteys toimii
 * - MySQL/SQLite versio
 * - Auktioiden latautuminen
 * - Konfigurointi-ongelmat
 * 
 * Poista käytöstä tuotannossa turvallisuussyistä!
 */

// Lataa bootstrap
require_once __DIR__ . '/bootstrap.php';

// Aloita HTML output
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Huuto247 Debug - Vianmääritys</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f5f5f5; }
        .debug-section { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #007cba; }
        .success { border-left-color: #28a745; }
        .error { border-left-color: #dc3545; }
        .warning { border-left-color: #ffc107; }
        pre { background: #f8f9fa; padding: 10px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 8px; border: 1px solid #ddd; text-align: left; }
        th { background: #f8f9fa; }
    </style>
</head>
<body>

<h1>🔍 Huuto247.fi Debug & Vianmääritys</h1>
<p><strong>Aika:</strong> <?php echo date('Y-m-d H:i:s'); ?> | <strong>Palvelin:</strong> <?php echo php_uname('n'); ?></p>

<?php

echo '<div class="debug-section">';
echo '<h2>📊 Ympäristötiedot</h2>';
echo '<table>';
echo '<tr><th>Muuttuja</th><th>Arvo</th></tr>';
echo '<tr><td>PHP Versio</td><td>' . PHP_VERSION . '</td></tr>';
echo '<tr><td>Palvelin</td><td>' . $_SERVER['SERVER_SOFTWARE'] ?? 'Tuntematon' . '</td></tr>';
echo '<tr><td>Dokumentti Root</td><td>' . $_SERVER['DOCUMENT_ROOT'] ?? 'Ei määritelty' . '</td></tr>';
echo '<tr><td>BASE_PATH</td><td>' . BASE_PATH . '</td></tr>';
echo '<tr><td>APP_PATH</td><td>' . APP_PATH . '</td></tr>';
echo '</table>';
echo '</div>';

// Tietokanta-asetukset
echo '<div class="debug-section">';
echo '<h2>🛢️ Tietokanta-asetukset</h2>';
echo '<table>';
echo '<tr><th>Asetus</th><th>Arvo</th></tr>';
echo '<tr><td>DB_HOST</td><td>' . (defined('DB_HOST') ? DB_HOST : '❌ EI MÄÄRITELTY') . '</td></tr>';
echo '<tr><td>DB_NAME</td><td>' . (defined('DB_NAME') ? DB_NAME : '❌ EI MÄÄRITELTY') . '</td></tr>';
echo '<tr><td>DB_USER</td><td>' . (defined('DB_USER') ? DB_USER : '❌ EI MÄÄRITELTY') . '</td></tr>';
echo '<tr><td>DB_PASS</td><td>' . (defined('DB_PASS') ? (DB_PASS ? '✅ ASETETTU' : '⚠️ TYHJÄ') : '❌ EI MÄÄRITELTY') . '</td></tr>';
echo '<tr><td>DB_CHARSET</td><td>' . (defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4 (oletus)') . '</td></tr>';
echo '</table>';
echo '</div>';

// Tietokantayhteys testi
echo '<div class="debug-section">';
echo '<h2>🔌 Tietokantayhteys</h2>';

try {
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    
    echo '<p class="success">✅ <strong>Yhteys onnistui!</strong></p>';
    
    // Selvitä tietokantajärjestelmä
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    $version = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
    
    echo '<table>';
    echo '<tr><th>Tietokantajärjestelmä</th><td>' . strtoupper($driver) . '</td></tr>';
    echo '<tr><td>Versio</td><td>' . $version . '</td></tr>';
    echo '</table>';
    
    // Testaa yksinkertainen kysely
    try {
        if ($driver === 'mysql') {
            $testQuery = $pdo->query("SELECT NOW() as server_time");
            $result = $testQuery->fetch();
            echo '<p>🕐 <strong>MySQL Aika:</strong> ' . $result['server_time'] . '</p>';
        } else {
            $testQuery = $pdo->query("SELECT datetime('now') as server_time");
            $result = $testQuery->fetch();
            echo '<p>🕐 <strong>SQLite Aika:</strong> ' . $result['server_time'] . '</p>';
        }
    } catch (Exception $timeError) {
        echo '<p>⚠️ <strong>Aika-kysely epäonnistui:</strong> ' . $timeError->getMessage() . '</p>';
    }
    
} catch (Exception $e) {
    echo '<div class="error">';
    echo '<p>❌ <strong>Tietokantayhteys epäonnistui!</strong></p>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    echo '</div>';
}
echo '</div>';

// Taulujen tarkistus
if (isset($pdo)) {
    echo '<div class="debug-section">';
    echo '<h2>📋 Tietokantataulut</h2>';
    
    try {
        if ($driver === 'mysql') {
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        } else {
            $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
        }
        
        echo '<p>✅ <strong>Löydetyt taulut (' . count($tables) . '):</strong></p>';
        echo '<ul>';
        foreach ($tables as $table) {
            echo '<li>' . htmlspecialchars($table) . '</li>';
        }
        echo '</ul>';
        
        // Tarkista tärkeimmät taulut
        $requiredTables = ['categories', 'users', 'auctions', 'bids', 'auction_images'];
        $missingTables = array_diff($requiredTables, $tables);
        
        if (empty($missingTables)) {
            echo '<p class="success">✅ Kaikki vaaditut taulut löytyvät!</p>';
        } else {
            echo '<div class="error">';
            echo '<p>❌ <strong>Puuttuvat taulut:</strong> ' . implode(', ', $missingTables) . '</p>';
            echo '<p>Aja mysql_production_schema.sql phpMyAdmin:issa!</p>';
            echo '</div>';
        }
        
    } catch (Exception $e) {
        echo '<div class="error">';
        echo '<p>❌ Virhe taulujen haussa: ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '</div>';
    }
    echo '</div>';
}

// Auction-mallin testaus
echo '<div class="debug-section">';
echo '<h2>🏠 Auction-mallin testaus</h2>';

try {
    $auction = new Auction();
    echo '<p>✅ <strong>Auction-malli ladattu onnistuneesti</strong></p>';
    
    // Testaa auktioiden haku
    echo '<h3>Auktioiden määrät:</h3>';
    echo '<table>';
    
    // Featured auctions
    try {
        $featured = $auction->getFeaturedAuctions(10);
        echo '<tr><td>Featured Auctions</td><td>' . count($featured) . ' kpl</td></tr>';
    } catch (Exception $e) {
        echo '<tr class="error"><td>Featured Auctions</td><td>❌ ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
    }
    
    // Popular auctions  
    try {
        $popular = $auction->getPopularAuctions(10);
        echo '<tr><td>Popular Auctions</td><td>' . count($popular) . ' kpl</td></tr>';
    } catch (Exception $e) {
        echo '<tr class="error"><td>Popular Auctions</td><td>❌ ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
    }
    
    // Closing soon
    try {
        $closing = $auction->getClosingSoonAuctions(10);
        echo '<tr><td>Closing Soon</td><td>' . count($closing) . ' kpl</td></tr>';
    } catch (Exception $e) {
        echo '<tr class="error"><td>Closing Soon</td><td>❌ ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
    }
    
    echo '</table>';
    
    // Näytä ensimmäinen auktio jos löytyy
    if (!empty($featured)) {
        echo '<h3>Ensimmäinen featured-auktio:</h3>';
        echo '<pre>' . htmlspecialchars(print_r($featured[0], true)) . '</pre>';
    } elseif (!empty($popular)) {
        echo '<h3>Ensimmäinen popular-auktio:</h3>';
        echo '<pre>' . htmlspecialchars(print_r($popular[0], true)) . '</pre>';
    } else {
        echo '<div class="warning">';
        echo '<p>⚠️ <strong>Ei auktioita tietokannassa</strong></p>';
        echo '<p>Lisää testimateriaalia tai tarkista tietokannan sisältö phpMyAdmin:issa</p>';
        echo '</div>';
    }
    
} catch (Exception $e) {
    echo '<div class="error">';
    echo '<p>❌ <strong>Auction-mallin virhe:</strong></p>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    echo '<p><strong>Stack trace:</strong></p>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</div>';
}
echo '</div>';

// Kategorioiden testaus
if (isset($pdo)) {
    echo '<div class="debug-section">';
    echo '<h2>📂 Kategoriat</h2>';
    
    try {
        $categoryQuery = $pdo->query("SELECT * FROM categories ORDER BY name");
        $categories = $categoryQuery->fetchAll();
        
        if (!empty($categories)) {
            echo '<p>✅ <strong>Löytyi ' . count($categories) . ' kategoriaa:</strong></p>';
            echo '<table>';
            echo '<tr><th>ID</th><th>Nimi</th><th>Slug</th><th>Kuvaus</th></tr>';
            foreach ($categories as $cat) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($cat['id']) . '</td>';
                echo '<td>' . htmlspecialchars($cat['name']) . '</td>';
                echo '<td>' . htmlspecialchars($cat['slug']) . '</td>';
                echo '<td>' . htmlspecialchars(substr($cat['description'] ?? '', 0, 50)) . '...</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<div class="warning">';
            echo '<p>⚠️ <strong>Ei kategorioita tietokannassa</strong></p>';
            echo '</div>';
        }
        
    } catch (Exception $e) {
        echo '<div class="error">';
        echo '<p>❌ Virhe kategorioiden haussa: ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '</div>';
    }
    echo '</div>';
}

// Virhelog tarkistus
echo '<div class="debug-section">';
echo '<h2>📜 Virhelokit</h2>';

$logFile = BASE_PATH . '/logs/php_errors.log';
if (file_exists($logFile) && is_readable($logFile)) {
    $logSize = filesize($logFile);
    echo '<p>📄 <strong>Löytyi virheloki:</strong> ' . $logFile . ' (' . number_format($logSize/1024, 1) . ' KB)</p>';
    
    if ($logSize > 0) {
        $logContent = file_get_contents($logFile);
        $lines = explode("\n", $logContent);
        $recentLines = array_slice($lines, -10); // 10 viimeisintä riviä
        
        echo '<p><strong>Viimeisimmät virheet:</strong></p>';
        echo '<pre style="max-height: 300px; overflow-y: auto;">';
        foreach ($recentLines as $line) {
            if (!empty(trim($line))) {
                echo htmlspecialchars($line) . "\n";
            }
        }
        echo '</pre>';
    } else {
        echo '<p class="success">✅ Virheloki on tyhjä - ei virheitä!</p>';
    }
} else {
    echo '<p>ℹ️ Virhelokia ei löydy tai sitä ei voida lukea</p>';
    echo '<p><code>' . $logFile . '</code></p>';
}
echo '</div>';

// Suorituskyvyn mittaus
$endTime = microtime(true);
$executionTime = $endTime - ($_SERVER['REQUEST_TIME_FLOAT'] ?? $endTime);

echo '<div class="debug-section">';
echo '<h2>⚡ Suorituskyky</h2>';
echo '<p><strong>Sivun latausaika:</strong> ' . number_format($executionTime * 1000, 2) . ' ms</p>';
echo '<p><strong>Muistin käyttö:</strong> ' . number_format(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB</p>';
echo '</div>';

?>

<div class="debug-section">
    <h2>🔗 Hyödylliset linkit</h2>
    <ul>
        <li><a href="index.php">🏠 Etusivu (index.php)</a></li>
        <li><a href="category.php">📂 Kategoriat (category.php)</a></li>
        <li><a href="auction.php?id=1">🏷️ Testauktio (auction.php?id=1)</a></li>
    </ul>
</div>

<div class="debug-section warning">
    <h2>⚠️ Turvallisuusvaroitus</h2>
    <p><strong>Poista debug.php käytöstä tuotantopalvelimella!</strong></p>
    <p>Tämä sivu paljastaa arkaluonteisia tietoja järjestelmästä.</p>
</div>

</body>
</html>