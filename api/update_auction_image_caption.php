<?php
require_once dirname(__DIR__) . '/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

function json_error(string $message, int $statusCode = 400): void
{
    http_response_code($statusCode);
    echo json_encode(['ok' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_error('Virheellinen HTTP-metodi.', 405);
    }

    $rawPayload = file_get_contents('php://input') ?: '';
    $decoded = json_decode($rawPayload, true);
    $payload = is_array($decoded) ? $decoded : $_POST;

    $auctionId = isset($payload['auction_id']) ? (int)$payload['auction_id'] : 0;
    $imageId = isset($payload['image_id']) ? (int)$payload['image_id'] : 0;
    $caption = isset($payload['caption']) ? trim((string)$payload['caption']) : '';

    if ($auctionId <= 0 || $imageId <= 0) {
        json_error('Virheellinen pyyntö.');
    }

    if (mb_strlen($caption) > 255) {
        json_error('Kuvateksti voi olla enintään 255 merkkiä.');
    }

    $db = Database::getInstance()->getConnection();
    $db->exec("ALTER TABLE auction_images ADD COLUMN IF NOT EXISTS caption VARCHAR(255) NULL AFTER image_path");

    $stmt = $db->prepare('SELECT id, user_id FROM auctions WHERE id = ? LIMIT 1');
    $stmt->execute([$auctionId]);
    $auction = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$auction) {
        json_error('Kohdetta ei löytynyt.', 404);
    }

    if (is_logged_in() && !is_admin() && (int)$auction['user_id'] !== (int)current_user_id()) {
        json_error('Ei oikeuksia muokata kohdetta.', 403);
    }

    $stmt = $db->prepare('SELECT id FROM auction_images WHERE id = ? AND auction_id = ? LIMIT 1');
    $stmt->execute([$imageId, $auctionId]);
    if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
        json_error('Kuvaa ei löytynyt.', 404);
    }

    $stmt = $db->prepare('UPDATE auction_images SET caption = ? WHERE id = ? AND auction_id = ?');
    $stmt->execute([$caption !== '' ? $caption : null, $imageId, $auctionId]);

    echo json_encode([
        'ok' => true,
        'image_id' => $imageId,
        'caption' => $caption,
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $exception) {
    error_log(json_encode([
        'event' => 'update_auction_image_caption_failed',
        'error' => $exception->getMessage(),
    ], JSON_UNESCAPED_UNICODE));

    json_error('Kuvatekstin tallennus epäonnistui.');
}
