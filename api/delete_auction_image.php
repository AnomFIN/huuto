<?php
// Less noise. More signal. AnomFIN.
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

    if ($auctionId <= 0 || $imageId <= 0) {
        json_error('Virheellinen pyyntö.');
    }

    $db = Database::getInstance()->getConnection();

    $stmt = $db->prepare('SELECT id, user_id FROM auctions WHERE id = ? LIMIT 1');
    $stmt->execute([$auctionId]);
    $auction = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$auction) {
        json_error('Kohdetta ei löytynyt.', 404);
    }

    if (!is_logged_in()) {
        json_error('Kirjaudu sisään jatkaaksesi.', 401);
    }

    if (!is_admin() && (int)$auction['user_id'] !== (int)current_user_id()) {
        json_error('Ei oikeuksia muokata kohdetta.', 403);
    }

    $stmt = $db->prepare('SELECT id, image_path, is_primary FROM auction_images WHERE id = ? AND auction_id = ? LIMIT 1');
    $stmt->execute([$imageId, $auctionId]);
    $image = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$image) {
        json_error('Kuvaa ei löytynyt.', 404);
    }

    $db->beginTransaction();

    $stmt = $db->prepare('DELETE FROM auction_images WHERE id = ? AND auction_id = ?');
    $stmt->execute([$imageId, $auctionId]);

    $newPrimaryId = null;
    if ((int)$image['is_primary'] === 1) {
        $stmt = $db->prepare('SELECT id FROM auction_images WHERE auction_id = ? ORDER BY sort_order ASC, id ASC LIMIT 1');
        $stmt->execute([$auctionId]);
        $newPrimary = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($newPrimary) {
            $newPrimaryId = (int)$newPrimary['id'];
            $stmt = $db->prepare('UPDATE auction_images SET is_primary = 0 WHERE auction_id = ?');
            $stmt->execute([$auctionId]);

            $stmt = $db->prepare('UPDATE auction_images SET is_primary = 1 WHERE id = ? AND auction_id = ?');
            $stmt->execute([$newPrimaryId, $auctionId]);
        }
    }

    $db->commit();

    $absolutePath = BASE_PATH . '/' . ltrim($image['image_path'], '/');
    if (is_file($absolutePath)) {
        @unlink($absolutePath);
    }

    echo json_encode([
        'ok' => true,
        'deleted_image_id' => $imageId,
        'primary_image_id' => $newPrimaryId,
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $exception) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }

    error_log(json_encode([
        'event' => 'delete_auction_image_failed',
        'error' => $exception->getMessage(),
    ], JSON_UNESCAPED_UNICODE));

    json_error('Kuvan poistaminen epäonnistui.');
}
