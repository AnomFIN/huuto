<?php
// AnomFIN — the neural network of innovation.
require_once dirname(__DIR__) . '/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

const AUCTION_IMAGE_MAX_SIZE = 10485760; // 10MB
const AUCTION_IMAGE_MAX_COUNT = 8;

function json_error(string $message, int $statusCode = 400): void
{
    http_response_code($statusCode);
    echo json_encode(['ok' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

function get_uploaded_files(array $files): array
{
    $normalized = [];
    if (!isset($files['name']) || !is_array($files['name'])) {
        return $normalized;
    }

    foreach ($files['name'] as $index => $name) {
        if (($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        $normalized[] = [
            'name' => $name,
            'type' => $files['type'][$index] ?? '',
            'tmp_name' => $files['tmp_name'][$index] ?? '',
            'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
            'size' => (int)($files['size'][$index] ?? 0),
        ];
    }

    return $normalized;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_error('Virheellinen HTTP-metodi.', 405);
    }

    $auctionId = isset($_POST['auction_id']) ? (int)$_POST['auction_id'] : 0;
    if ($auctionId <= 0) {
        json_error('Virheellinen kohde.');
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

    $files = get_uploaded_files($_FILES['images'] ?? []);
    if (empty($files)) {
        json_error('Valitse vähintään yksi kuva.');
    }

    $stmt = $db->prepare('SELECT COUNT(*) FROM auction_images WHERE auction_id = ?');
    $stmt->execute([$auctionId]);
    $currentCount = (int)$stmt->fetchColumn();

    if ($currentCount + count($files) > AUCTION_IMAGE_MAX_COUNT) {
        json_error('Kohteessa voi olla enintään 8 kuvaa.');
    }

    $allowedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    $baseUploadPath = BASE_PATH . '/uploads/auctions/' . $auctionId;
    if (!is_dir($baseUploadPath) && !mkdir($baseUploadPath, 0755, true) && !is_dir($baseUploadPath)) {
        json_error('Upload-kansiota ei voitu luoda.', 500);
    }

    if (!is_writable($baseUploadPath)) {
        json_error('Upload-kansioon ei voi kirjoittaa.', 500);
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    if ($finfo === false) {
        json_error('Tiedostotarkistus ei ole käytettävissä.', 500);
    }

    $stmt = $db->prepare('SELECT COALESCE(MAX(sort_order), 0) FROM auction_images WHERE auction_id = ?');
    $stmt->execute([$auctionId]);
    $sortOrder = (int)$stmt->fetchColumn();

    $db->beginTransaction();
    $createdImages = [];

    foreach ($files as $file) {
        if ((int)$file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Yhden tai useamman kuvan lataus epäonnistui.');
        }

        if ($file['size'] <= 0 || $file['size'] > AUCTION_IMAGE_MAX_SIZE) {
            throw new RuntimeException('Kuvan koko ylittää sallitun 10MB rajan.');
        }

        $mimeType = finfo_file($finfo, $file['tmp_name']);
        if (!isset($allowedMimeTypes[$mimeType])) {
            throw new RuntimeException('Vain JPG, PNG ja WEBP kuvat ovat sallittuja.');
        }

        $extension = $allowedMimeTypes[$mimeType];
        $baseName = sprintf('%s_%s', time(), bin2hex(random_bytes(8)));
        $originalName = $baseName . '_orig.' . $extension;
        $watermarkedName = $baseName . '_wm.' . $extension;
        $minName = $baseName . '_min.' . $extension;
        $originalPath = $baseUploadPath . '/' . $originalName;
        $watermarkedPath = $baseUploadPath . '/' . $watermarkedName;
        $minPath = $baseUploadPath . '/' . $minName;
        $publicPath = '/uploads/auctions/' . $auctionId . '/' . $watermarkedName;

        if (!move_uploaded_file($file['tmp_name'], $originalPath)) {
            throw new RuntimeException('Kuvan tallennus epäonnistui.');
        }

        if (!create_watermarked_variant($originalPath, $watermarkedPath)) {
            throw new RuntimeException('Vesileimakuvan luonti epäonnistui.');
        }

        if (!create_listing_thumbnail($originalPath, $minPath)) {
            throw new RuntimeException('Listauskuvan luonti epäonnistui.');
        }

        $isPrimary = $currentCount === 0 && empty($createdImages) ? 1 : 0;
        $sortOrder++;

        $insertStmt = $db->prepare(
            'INSERT INTO auction_images (auction_id, image_path, caption, is_primary, sort_order, created_at) VALUES (?, ?, ?, ?, ?, NOW())'
        );
        $insertStmt->execute([$auctionId, $publicPath, null, $isPrimary, $sortOrder]);

        $createdImages[] = [
            'id' => (int)$db->lastInsertId(),
            'url' => $publicPath,
            'is_primary' => $isPrimary,
        ];
    }

    finfo_close($finfo);
    $db->commit();

    echo json_encode([
        'ok' => true,
        'images' => $createdImages,
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $exception) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }

    error_log(json_encode([
        'event' => 'auction_image_upload_failed',
        'auction_id' => $auctionId ?? null,
        'error' => $exception->getMessage(),
    ], JSON_UNESCAPED_UNICODE));

    json_error('Kuvien lataus epäonnistui.');
}
