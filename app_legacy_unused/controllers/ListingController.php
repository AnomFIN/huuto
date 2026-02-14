<?php
/**
 * Listing Controller
 */

class ListingController {
    
    public function show($params) {
        $id = $params['id'] ?? 0;
        $db = Database::getInstance();
        
        $listing = $db->fetch(
            "SELECT l.*, c.name as category_name, c.slug as category_slug,
                    u.name as seller_name, u.email as seller_email
             FROM listings l
             LEFT JOIN categories c ON l.category_id = c.id
             LEFT JOIN users u ON l.user_id = u.id
             WHERE l.id = ?",
            [$id]
        );
        
        if (!$listing) {
            http_response_code(404);
            require __DIR__ . '/../views/404.php';
            return;
        }
        
        // Get images
        $images = $db->fetchAll(
            "SELECT * FROM listing_images WHERE listing_id = ? ORDER BY sort_order",
            [$id]
        );
        
        // Get bid history
        $bids = $db->fetchAll(
            "SELECT b.*, u.name as bidder_name
             FROM bids b
             LEFT JOIN users u ON b.user_id = u.id
             WHERE b.listing_id = ?
             ORDER BY b.created_at DESC
             LIMIT 20",
            [$id]
        );
        
        // Get highest bidder name
        if ($listing['highest_bidder_id']) {
            $highestBidder = $db->fetch(
                "SELECT name FROM users WHERE id = ?",
                [$listing['highest_bidder_id']]
            );
            $listing['highest_bidder_name'] = $highestBidder['name'] ?? '';
        }
        
        require __DIR__ . '/../views/listings/show.php';
    }
    
    public function ending() {
        $db = Database::getInstance();
        
        $listings = $db->fetchAll(
            "SELECT l.*, c.name as category_name, c.slug as category_slug,
                    (SELECT path FROM listing_images WHERE listing_id = l.id ORDER BY sort_order LIMIT 1) as image
             FROM listings l
             LEFT JOIN categories c ON l.category_id = c.id
             WHERE l.status = 'active' AND l.ends_at > NOW() AND l.ends_at <= DATE_ADD(NOW(), INTERVAL 24 HOUR)
             ORDER BY l.ends_at ASC"
        );
        
        require __DIR__ . '/../views/listings/ending.php';
    }
}
