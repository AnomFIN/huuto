<?php
require_once dirname(__DIR__) . '/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

const IMPORT_IMAGE_MAX_SIZE = 10485760; // 10MB

function json_error(string $message, int $statusCode = 400): void
{
    http_response_code($statusCode);
    echo json_encode(['ok' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

function is_allowed_image_host(string $url): bool
{
    $host = strtolower((string)parse_url($url, PHP_URL_HOST));
    $allowedHosts = [
        'upload.wikimedia.org',
        'commons.wikimedia.org',
    ];

    return in_array($host, $allowedHosts, true);
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_error('Virheellinen HTTP-metodi.', 405);
    }

    $rawPayload = file_get_contents('php://input') ?: '';
    $decoded = json_decode($rawPayload, true);
    $payload = is_array($decoded) ? $decoded : $_POST;

    $auctionId = isset($payload['auction_id']) ? (int)$payload['auction_id'] : 0;
    $imageUrl = trim((string)($payload['image_url'] ?? ''));
    $title = trim((string)($payload['title'] ?? ''));

    if ($auctionId <= 0 || $imageUrl === '') {
        json_error('Virheellinen pyyntö.');
    }

    if (!preg_match('#^https?://#i', $imageUrl) || !is_allowed_image_host($imageUrl)) {
        json_error('Vain Wikimedia Commons -kuvalinkit ovat sallittuja.');
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

    $stmt = $db->prepare('SELECT COUNT(*) FROM auction_images WHERE auction_id = ?');
    $stmt->execute([$auctionId]);
    $imageCount = (int)$stmt->fetchColumn();

    if ($imageCount >= 8) {
        json_error('Kohteessa voi olla enintään 8 kuvaa.');
    }

    $ch = curl_init($imageUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => 'huuto247-image-import/1.0',
        CURLOPT_HEADER => false,
    ]);

    $imageBinary = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = (string)curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($imageBinary === false || $statusCode >= 400) {
        throw new RuntimeException('Kuvan lataus epäonnistui: ' . ($curlError ?: ('HTTP ' . $statusCode)));
    }

    if (strlen($imageBinary) === 0 || strlen($imageBinary) > IMPORT_IMAGE_MAX_SIZE) {
        json_error('Kuvan koko on virheellinen (max 10MB).');
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    if ($finfo === false) {
        throw new RuntimeException('MIME-tunnistus ei ole käytettävissä.');
    }

    $mimeType = finfo_buffer($finfo, $imageBinary) ?: $contentType;
    finfo_close($finfo);

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    if (!isset($allowedTypes[$mimeType])) {
        json_error('Vain JPG, PNG, WEBP ja GIF kuvat ovat sallittuja.');
    }

    $extension = $allowedTypes[$mimeType];
    $baseUploadPath = BASE_PATH . '/uploads/auctions/' . $auctionId;
    if (!is_dir($baseUploadPath) && !mkdir($baseUploadPath, 0755, true) && !is_dir($baseUploadPath)) {
        throw new RuntimeException('Upload-kansion luonti epäonnistui.');
    }

    $baseName = sprintf('import_%s_%s', time(), bin2hex(random_bytes(6)));
    $originalFileName = $baseName . '_orig.' . $extension;
    $watermarkedFileName = $baseName . '_wm.' . $extension;
    $minFileName = $baseName . '_min.' . $extension;
    $targetPath = $baseUploadPath . '/' . $originalFileName;
    $watermarkedPath = $baseUploadPath . '/' . $watermarkedFileName;
    $minPath = $baseUploadPath . '/' . $minFileName;
    $publicPath = '/uploads/auctions/' . $auctionId . '/' . $watermarkedFileName;

    if (file_put_contents($targetPath, $imageBinary) === false) {
        throw new RuntimeException('Kuvan tallennus palvelimelle epäonnistui.');
    }

    if (!create_watermarked_variant($targetPath, $watermarkedPath)) {
        throw new RuntimeException('Vesileimakuvan luonti epäonnistui.');
    }

    if (!create_listing_thumbnail($targetPath, $minPath)) {
        throw new RuntimeException('Listauskuvan luonti epäonnistui.');
    }

    $stmt = $db->prepare('SELECT COALESCE(MAX(sort_order), 0) FROM auction_images WHERE auction_id = ?');
    $stmt->execute([$auctionId]);
    $sortOrder = (int)$stmt->fetchColumn() + 1;

    $isPrimary = $imageCount === 0 ? 1 : 0;
    $caption = $title !== '' ? mb_substr($title, 0, 255) : null;

    $insertStmt = $db->prepare('INSERT INTO auction_images (auction_id, image_path, caption, is_primary, sort_order, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
    $insertStmt->execute([$auctionId, $publicPath, $caption, $isPrimary, $sortOrder]);

    echo json_encode([
        'ok' => true,
        'image' => [
            'id' => (int)$db->lastInsertId(),
            'url' => $publicPath,
            'is_primary' => $isPrimary,
            'caption' => $caption,
        ],
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $exception) {
    error_log(json_encode([
        'event' => 'import_free_image_failed',
        'error' => $exception->getMessage(),
    ], JSON_UNESCAPED_UNICODE));

    json_error('Kuvan tuonti epäonnistui.');
}
