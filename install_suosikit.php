<?php
require_once __DIR__ . '/bootstrap.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $db = Database::getInstance()->getConnection();

    $db->exec("CREATE TABLE IF NOT EXISTS user_favourites (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        auction_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uq_user_auction (user_id, auction_id),
        INDEX idx_user (user_id),
        INDEX idx_auction (auction_id),
        CONSTRAINT fk_user_favourites_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        CONSTRAINT fk_user_favourites_auction FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    echo "OK: user_favourites taulu on luotu tai on jo olemassa.\n";
    echo "Taulun nimi: user_favourites\n";
    echo "Avaimet: UNIQUE(user_id, auction_id), idx_user, idx_auction\n";
} catch (Throwable $exception) {
    http_response_code(500);
    echo "Virhe: suosikkien asennus epäonnistui.\n";
    echo $exception->getMessage() . "\n";
}
