<?php
/**
 * Home Controller
 */

class HomeController {
    
    public function index() {
        $db = Database::getInstance();
        
        // Get featured listings (ending soon)
        $endingSoon = $db->fetchAll(
            "SELECT l.*, c.name as category_name, c.slug as category_slug,
                    (SELECT path FROM listing_images WHERE listing_id = l.id ORDER BY sort_order LIMIT 1) as image
             FROM listings l
             LEFT JOIN categories c ON l.category_id = c.id
             WHERE l.status = 'active' AND l.ends_at > NOW()
             ORDER BY l.ends_at ASC
             LIMIT 12"
        );
        
        // Get newest listings
        $newest = $db->fetchAll(
            "SELECT l.*, c.name as category_name, c.slug as category_slug,
                    (SELECT path FROM listing_images WHERE listing_id = l.id ORDER BY sort_order LIMIT 1) as image
             FROM listings l
             LEFT JOIN categories c ON l.category_id = c.id
             WHERE l.status = 'active' AND l.ends_at > NOW()
             ORDER BY l.created_at DESC
             LIMIT 12"
        );
        
        // Get categories
        $categories = $db->fetchAll(
            "SELECT * FROM categories ORDER BY sort_order"
        );
        
        require __DIR__ . '/../views/home.php';
    }
}
