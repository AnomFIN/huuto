<?php
/**
 * User Controller
 */

class UserController {
    
    public function profile() {
        Security::requireLogin();
        require __DIR__ . '/../views/user/profile.php';
    }
    
    public function myListings() {
        Security::requireLogin();
        
        $db = Database::getInstance();
        $userId = $_SESSION['user_id'];
        
        $listings = $db->fetchAll(
            "SELECT l.*, c.name as category_name,
                    (SELECT path FROM listing_images WHERE listing_id = l.id ORDER BY sort_order LIMIT 1) as image,
                    (SELECT COUNT(*) FROM bids WHERE listing_id = l.id) as bid_count
             FROM listings l
             LEFT JOIN categories c ON l.category_id = c.id
             WHERE l.user_id = ?
             ORDER BY l.created_at DESC",
            [$userId]
        );
        
        require __DIR__ . '/../views/user/my-listings.php';
    }
    
    public function myBids() {
        Security::requireLogin();
        
        $db = Database::getInstance();
        $userId = $_SESSION['user_id'];
        
        $bids = $db->fetchAll(
            "SELECT b.*, l.title, l.slug, l.id as listing_id, l.current_price, l.highest_bidder_id, l.ends_at, l.status,
                    (SELECT path FROM listing_images WHERE listing_id = l.id ORDER BY sort_order LIMIT 1) as image
             FROM bids b
             LEFT JOIN listings l ON b.listing_id = l.id
             WHERE b.user_id = ?
             ORDER BY b.created_at DESC",
            [$userId]
        );
        
        require __DIR__ . '/../views/user/my-bids.php';
    }
    
    public function myWins() {
        Security::requireLogin();
        
        $db = Database::getInstance();
        $userId = $_SESSION['user_id'];
        
        $wins = $db->fetchAll(
            "SELECT l.*, c.name as category_name,
                    (SELECT path FROM listing_images WHERE listing_id = l.id ORDER BY sort_order LIMIT 1) as image
             FROM listings l
             LEFT JOIN categories c ON l.category_id = c.id
             WHERE l.highest_bidder_id = ? AND l.ends_at <= NOW()
             ORDER BY l.ends_at DESC",
            [$userId]
        );
        
        require __DIR__ . '/../views/user/my-wins.php';
    }
    
    public function createListing() {
        Security::requireLogin();
        
        $db = Database::getInstance();
        $categories = $db->fetchAll("SELECT * FROM categories ORDER BY name");
        
        require __DIR__ . '/../views/user/create-listing.php';
    }
    
    public function doCreateListing() {
        Security::requireLogin();
        
        if (!isset($_POST['csrf_token']) || !Security::verifyToken($_POST['csrf_token'])) {
            die('Invalid CSRF token');
        }
        
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $categoryId = intval($_POST['category_id'] ?? 0);
        $condition = trim($_POST['condition'] ?? '');
        $region = trim($_POST['region'] ?? '');
        $startPrice = floatval($_POST['start_price'] ?? 0);
        $buyNowPrice = !empty($_POST['buy_now_price']) ? floatval($_POST['buy_now_price']) : null;
        $minIncrement = floatval($_POST['min_increment'] ?? 5);
        $days = intval($_POST['days'] ?? 7);
        
        $errors = [];
        
        if (empty($title)) $errors[] = 'Otsikko vaaditaan';
        if (empty($description)) $errors[] = 'Kuvaus vaaditaan';
        if ($categoryId <= 0) $errors[] = 'Valitse kategoria';
        if ($startPrice <= 0) $errors[] = 'Aloitushinta vaaditaan';
        
        if (empty($errors)) {
            $db = Database::getInstance();
            $userId = $_SESSION['user_id'];
            $slug = Security::generateSlug($title);
            $endsAt = date('Y-m-d H:i:s', strtotime("+{$days} days"));
            
            $db->execute(
                "INSERT INTO listings (user_id, category_id, title, slug, description, `condition`, region, 
                 start_price, buy_now_price, min_increment, current_price, ends_at, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')",
                [$userId, $categoryId, $title, $slug, $description, $condition, $region, 
                 $startPrice, $buyNowPrice, $minIncrement, $startPrice, $endsAt]
            );
            
            $listingId = $db->lastInsertId();
            
            // Handle image uploads (simplified - would need proper validation)
            if (isset($_FILES['images'])) {
                // This is a placeholder - proper file upload would be needed
            }
            
            $_SESSION['success_message'] = 'Ilmoitus luotu!';
            header('Location: /kohde/' . $listingId . '/' . $slug);
            exit;
        }
        
        $_SESSION['listing_errors'] = $errors;
        header('Location: /luo-ilmoitus');
        exit;
    }
}
