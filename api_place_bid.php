<?php
/**
 * Place Bid API
 * Accepts a bid for an active auction
 */

require_once __DIR__ . '/bootstrap.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Authentication required
if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'Kirjautuminen vaaditaan tarjouksen tekemiseen']);
    exit;
}

// CSRF protection
if (!csrf_verify()) {
    http_response_code(403);
    echo json_encode(['error' => 'Virheellinen pyyntö. Lataa sivu uudelleen.']);
    exit;
}

$auctionId = isset($_POST['auction_id']) ? (int)$_POST['auction_id'] : 0;
$amount    = isset($_POST['amount'])     ? (float)$_POST['amount']    : 0.0;

if (!$auctionId || $amount <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Virheelliset tiedot']);
    exit;
}

try {
    $auctionModel = new Auction();
    $userId = current_user_id();

    $auctionModel->placeBid($auctionId, $userId, $amount);

    echo json_encode([
        'success' => true,
        'message' => 'Tarjous ' . number_format($amount, 0, ',', ' ') . ' € tehty onnistuneesti!',
        'amount'  => $amount,
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error'   => $e->getMessage(),
        'success' => false,
    ]);
}
