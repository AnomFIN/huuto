<?php
require_once dirname(__DIR__) . '/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Virheellinen HTTP-metodi.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Kirjautuminen vaaditaan.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
$auctionId = isset($payload['auction_id']) ? (int)$payload['auction_id'] : 0;

if ($auctionId <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Virheellinen kohde.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();

    $db->exec("CREATE TABLE IF NOT EXISTS user_favourites (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        auction_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uq_user_auction (user_id, auction_id),
        INDEX idx_user (user_id),
        INDEX idx_auction (auction_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $userId = (int)current_user_id();

    $checkStmt = $db->prepare('SELECT id FROM user_favourites WHERE user_id = ? AND auction_id = ? LIMIT 1');
    $checkStmt->execute([$userId, $auctionId]);
    $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $deleteStmt = $db->prepare('DELETE FROM user_favourites WHERE id = ? LIMIT 1');
        $deleteStmt->execute([(int)$existing['id']]);

        echo json_encode(['ok' => true, 'favorited' => false], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $auctionStmt = $db->prepare('SELECT id FROM auctions WHERE id = ? LIMIT 1');
    $auctionStmt->execute([$auctionId]);
    $auction = $auctionStmt->fetch(PDO::FETCH_ASSOC);
    if (!$auction) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'error' => 'Kohdetta ei löytynyt.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $insertStmt = $db->prepare('INSERT INTO user_favourites (user_id, auction_id, created_at) VALUES (?, ?, NOW())');
    $insertStmt->execute([$userId, $auctionId]);

    echo json_encode(['ok' => true, 'favorited' => true], JSON_UNESCAPED_UNICODE);
} catch (Throwable $exception) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Suosikin tallennus epäonnistui.'], JSON_UNESCAPED_UNICODE);
}
