<?php
/**
 * Bid Controller
 */

class BidController {
    
    public function place($params) {
        Security::requireLogin();
        
        if (!isset($_POST['csrf_token']) || !Security::verifyToken($_POST['csrf_token'])) {
            die('Invalid CSRF token');
        }
        
        $listingId = $params['id'] ?? 0;
        $amount = floatval($_POST['amount'] ?? 0);
        $userId = $_SESSION['user_id'];
        
        $db = Database::getInstance();
        
        // Get listing
        $listing = $db->fetch(
            "SELECT * FROM listings WHERE id = ? AND status = 'active'",
            [$listingId]
        );
        
        if (!$listing) {
            $_SESSION['error_message'] = 'Ilmoitusta ei löytynyt';
            header('Location: /');
            exit;
        }
        
        // Check if ended
        if (strtotime($listing['ends_at']) <= time()) {
            $_SESSION['error_message'] = 'Huutokauppa on päättynyt';
            header('Location: /kohde/' . $listingId . '/' . $listing['slug']);
            exit;
        }
        
        // Check if user is seller
        if ($listing['user_id'] == $userId) {
            $_SESSION['error_message'] = 'Et voi huutaa omaan ilmoitukseesi';
            header('Location: /kohde/' . $listingId . '/' . $listing['slug']);
            exit;
        }
        
        // Check minimum increment
        $minBid = $listing['current_price'] + $listing['min_increment'];
        if ($amount < $minBid) {
            $_SESSION['error_message'] = 'Huudon tulee olla vähintään ' . Security::formatPrice($minBid);
            header('Location: /kohde/' . $listingId . '/' . $listing['slug']);
            exit;
        }
        
        // Place bid
        $db->execute(
            "INSERT INTO bids (listing_id, user_id, amount) VALUES (?, ?, ?)",
            [$listingId, $userId, $amount]
        );
        
        // Update listing
        $db->execute(
            "UPDATE listings SET current_price = ?, highest_bidder_id = ? WHERE id = ?",
            [$amount, $userId, $listingId]
        );
        
        $_SESSION['success_message'] = 'Huuto onnistui!';
        header('Location: /kohde/' . $listingId . '/' . $listing['slug']);
        exit;
    }
}
