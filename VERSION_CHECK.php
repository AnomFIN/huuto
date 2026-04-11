<?php
/**
 * Version check script - näyttää mitkä tiedostot tarvitsevat päivityksen FTP:ään
 * Aja tämä FTP:ssä nähdäksesi mitkä tiedostot ovat vanhentuneita
 */
echo "=== HUUTO247 VERSION CHECK ===\n\n";

// Tarkista Database.php
echo "1. Database.php versio:\n";
$dbFile = __DIR__ . '/src/models/Database.php';
if (file_exists($dbFile)) {
    $dbContent = file_get_contents($dbFile);
    if (strpos($dbContent, 'PDO::MYSQL_ATTR_INIT_COMMAND') !== false) {
        echo "❌ VANHA VERSIO - Sisältää PDO::MYSQL_ATTR_INIT_COMMAND\n";
        echo "   → Päivitä Database.php FTP:ään\n";
    } else {
        echo "✅ Uusi versio - MySQL yhteensopiva\n";
    }
} else {
    echo "❌ Tiedosto puuttuu\n";
}

echo "\n2. Auction.php ORDER BY tarkistus:\n";
$auctionFile = __DIR__ . '/src/models/Auction.php';
if (file_exists($auctionFile)) {
    $auctionContent = file_get_contents($auctionFile);
    if (strpos($auctionContent, 'ORDER BY a.bid_count') !== false) {
        echo "❌ VANHA VERSIO - Sisältää 'ORDER BY a.bid_count'\n";
        echo "   → Päivitä Auction.php FTP:ään\n";
    } else if (strpos($auctionContent, 'ORDER BY bid_count') !== false) {
        echo "✅ Uusi versio - Korjattu ORDER BY bid_count\n";
    } else {
        echo "❓ Ei löydy ORDER BY bid_count - tarkista manuaalisesti\n";
    }
} else {
    echo "❌ Tiedosto puuttuu\n";
}

echo "\n3. Index.php debug kommentit:\n";
$indexFile = __DIR__ . '/index.php';
if (file_exists($indexFile)) {
    $indexContent = file_get_contents($indexFile);
    if (strpos($indexContent, '<!-- DEBUG: Creating Auction model -->') !== false) {
        echo "✅ Uusi versio - Debug kommentit lisätty\n";
    } else {
        echo "❌ VANHA VERSIO - Ei debug kommentteja\n";
        echo "   → Päivitä index.php FTP:ään\n";
    }
} else {
    echo "❌ Tiedosto puuttuu\n";
}

echo "\n=== YHTEENVETO ===\n";
echo "Jos näet ❌ merkkejä, päivitä kyseiset tiedostot GitHubista FTP:ään.\n";
echo "Commit: ec4e45c (11.4.2026)\n";
echo "Tarkista: https://github.com/AnomFIN/huuto\n";
?>