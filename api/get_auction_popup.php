<?php
require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

$auctionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($auctionId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Virheellinen kohde-ID'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $auctionModel = new Auction();
    $auction = $auctionModel->getAuctionById($auctionId);

    if (!$auction) {
      http_response_code(404);
      echo json_encode(['success' => false, 'error' => 'Kohdetta ei löytynyt'], JSON_UNESCAPED_UNICODE);
      exit;
    }

    $images = $auctionModel->getAuctionImages($auctionId);
    $bids = $auctionModel->getAuctionBids($auctionId, 8);

    $metadata = [];
    try {
      $metadata = $auctionModel->getAuctionMetadata($auctionId);
    } catch (Throwable $exception) {
      $metadata = [];
    }

    $currentPrice = isset($auction['current_price']) && is_numeric($auction['current_price'])
      ? (float)$auction['current_price']
      : (float)($auction['starting_price'] ?? 0);

    $imageUrl = null;
    if (!empty($images)) {
      $imageUrl = (string)($images[0]['image_path'] ?? '');
    }

    echo json_encode([
      'success' => true,
      'item' => [
        'id' => (int)$auction['id'],
        'title' => (string)($auction['title'] ?? 'Kohde'),
        'description' => (string)($auction['description'] ?? ''),
        'category' => (string)($auction['category_name'] ?? 'Muut'),
        'location' => (string)($auction['location'] ?? 'Ei sijaintia'),
        'seller' => (string)($auction['seller_username'] ?? 'Tuntematon myyjä'),
        'delivery' => 'Nouto / Toimitus',
        'status' => (string)($auction['status'] ?? ''),
        'endTime' => (string)($auction['end_time'] ?? ''),
        'startingPrice' => (float)($auction['starting_price'] ?? 0),
        'currentPrice' => $currentPrice,
        'buyNowPrice' => isset($auction['buy_now_price']) && $auction['buy_now_price'] !== null ? (float)$auction['buy_now_price'] : null,
        'reservePrice' => isset($auction['reserve_price']) && $auction['reserve_price'] !== null ? (float)$auction['reserve_price'] : null,
        'bidIncrement' => (float)($auction['bid_increment'] ?? 1),
        'bidCount' => (int)($auction['bid_count'] ?? 0),
        'imageUrl' => $imageUrl,
        'metadata' => $metadata,
      ],
      'bids' => array_map(static function($bid) {
        return [
          'amount' => (float)($bid['amount'] ?? 0),
          'username' => (string)($bid['username'] ?? 'käyttäjä'),
          'bidTime' => (string)($bid['bid_time'] ?? ''),
        ];
      }, is_array($bids) ? $bids : []),
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $exception) {
    http_response_code(500);
    echo json_encode([
      'success' => false,
      'error' => 'Tietojen lataus epäonnistui',
    ], JSON_UNESCAPED_UNICODE);
}
