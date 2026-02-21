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
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        json_error('Virheellinen HTTP-metodi.', 405);
    }

    $auctionId = isset($_GET['auction_id']) ? (int)$_GET['auction_id'] : 0;
    $query = trim((string)($_GET['q'] ?? ''));

    if ($auctionId <= 0) {
        json_error('Virheellinen kohde.');
    }

    if ($query === '' || mb_strlen($query) < 2) {
        json_error('Anna vähintään 2 merkkiä sisältävä hakusana.');
    }

    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare('SELECT id, user_id FROM auctions WHERE id = ? LIMIT 1');
    $stmt->execute([$auctionId]);
    $auction = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$auction) {
        json_error('Kohdetta ei löytynyt.', 404);
    }

    if (is_logged_in() && !is_admin() && (int)$auction['user_id'] !== (int)current_user_id()) {
        json_error('Ei oikeuksia muokata kohdetta.', 403);
    }

    $params = [
        'action' => 'query',
        'format' => 'json',
        'generator' => 'search',
        'gsrnamespace' => 6,
        'gsrlimit' => 12,
        'gsrsearch' => $query,
        'prop' => 'imageinfo',
        'iiprop' => 'url|extmetadata',
        'iiurlwidth' => 500,
    ];

    $apiUrl = 'https://commons.wikimedia.org/w/api.php?' . http_build_query($params);

    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => 'huuto247-image-import/1.0',
    ]);

    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false || $statusCode >= 400) {
        throw new RuntimeException('Kuvahaun palvelu ei vastannut: ' . ($curlError ?: ('HTTP ' . $statusCode)));
    }

    $decoded = json_decode($response, true);
    $pages = $decoded['query']['pages'] ?? [];

    $results = [];
    foreach ($pages as $page) {
        $imageInfo = $page['imageinfo'][0] ?? null;
        if (!is_array($imageInfo)) {
            continue;
        }

        $fullUrl = trim((string)($imageInfo['url'] ?? ''));
        $thumbUrl = trim((string)($imageInfo['thumburl'] ?? $fullUrl));
        if ($fullUrl === '') {
            continue;
        }

        $meta = $imageInfo['extmetadata'] ?? [];
        $license = trim((string)($meta['LicenseShortName']['value'] ?? 'Commons-lisensoitu'));
        $licenseUrl = trim((string)($meta['LicenseUrl']['value'] ?? ''));

        $results[] = [
            'title' => (string)($page['title'] ?? 'Kuva'),
            'thumb_url' => $thumbUrl,
            'full_url' => $fullUrl,
            'source_page' => 'https://commons.wikimedia.org/wiki/' . rawurlencode((string)($page['title'] ?? 'File:')),
            'license' => strip_tags($license),
            'license_url' => $licenseUrl,
        ];
    }

    echo json_encode([
        'ok' => true,
        'results' => $results,
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $exception) {
    error_log(json_encode([
        'event' => 'search_free_images_failed',
        'error' => $exception->getMessage(),
    ], JSON_UNESCAPED_UNICODE));

    json_error('Kuvahaku epäonnistui.');
}
