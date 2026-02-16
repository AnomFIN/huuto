<?php
/**
 * Auction model - handles all auction-related database operations
 */
class Auction {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get popular auctions (most bids/views)
     */
    public function getPopularAuctions($limit = 20) {
        $sql = "SELECT a.*, c.name as category_name, c.slug as category_slug,
                       u.username as seller_username,
                       (SELECT image_path FROM auction_images WHERE auction_id = a.id AND is_primary = 1 LIMIT 1) as primary_image,
                       (SELECT COUNT(*) FROM bids WHERE auction_id = a.id) as bid_count,
                       (SELECT MAX(amount) FROM bids WHERE auction_id = a.id) as highest_bid
                FROM auctions a
                JOIN categories c ON a.category_id = c.id
                JOIN users u ON a.user_id = u.id
                WHERE a.status = 'active' AND a.end_time > NOW()
                ORDER BY (SELECT COUNT(*) FROM bids WHERE auction_id = a.id) DESC, a.views DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $results = $stmt->fetchAll();
        foreach ($results as &$auction) {
            // Use highest bid as current price, fallback to starting price
            $auction['current_price'] = $auction['highest_bid'] ?: $auction['starting_price'];
        }
        
        return $results;
    }
    
    /**
     * Get auctions closing soon (within next 24 hours)
     */
    public function getClosingSoonAuctions($limit = 5) {
        $sql = "SELECT a.*, c.name as category_name, c.slug as category_slug,
                       u.username as seller_username,
                       (SELECT image_path FROM auction_images WHERE auction_id = a.id AND is_primary = 1 LIMIT 1) as primary_image,
                       (SELECT COUNT(*) FROM bids WHERE auction_id = a.id) as bid_count,
                       (SELECT MAX(amount) FROM bids WHERE auction_id = a.id) as current_price
                FROM auctions a
                JOIN categories c ON a.category_id = c.id
                JOIN users u ON a.user_id = u.id
                WHERE a.status = 'active' 
                  AND a.end_time > NOW() 
                  AND a.end_time <= DATE_ADD(NOW(), INTERVAL 24 HOUR)
                ORDER BY a.end_time ASC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $results = $stmt->fetchAll();
        foreach ($results as &$auction) {
            // Use highest bid as current price, fallback to starting price
            $auction['current_price'] = $auction['current_price'] ?: $auction['starting_price'];
        }
        
        return $results;
    }
    
    /**
     * Get all active auctions
     */
    public function getActiveAuctions($limit = 20, $offset = 0) {
        $sql = "SELECT a.*, c.name as category_name, c.slug as category_slug,
                       u.username as seller_username,
                       (SELECT image_path FROM auction_images WHERE auction_id = a.id AND is_primary = 1 LIMIT 1) as primary_image,
                       (SELECT COUNT(*) FROM bids WHERE auction_id = a.id) as bid_count
                FROM auctions a
                JOIN categories c ON a.category_id = c.id
                JOIN users u ON a.user_id = u.id
                WHERE a.status = 'active' AND a.end_time > NOW()
                ORDER BY a.end_time ASC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get auctions by category
     */
    public function getAuctionsByCategory($categorySlug, $limit = 20, $offset = 0) {
        $sql = "SELECT a.*, c.name as category_name, c.slug as category_slug,
                       u.username as seller_username,
                       (SELECT image_path FROM auction_images WHERE auction_id = a.id AND is_primary = 1 LIMIT 1) as primary_image,
                       (SELECT COUNT(*) FROM bids WHERE auction_id = a.id) as bid_count
                FROM auctions a
                JOIN categories c ON a.category_id = c.id
                JOIN users u ON a.user_id = u.id
                WHERE a.status = 'active' AND a.end_time > NOW() AND c.slug = :slug
                ORDER BY a.end_time ASC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':slug', $categorySlug, PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get auction by ID with full details
     */
    public function getAuctionById($id) {
        $sql = "SELECT a.*, c.name as category_name, c.slug as category_slug,
                       u.username as seller_username, u.full_name as seller_name,
                       (SELECT COUNT(*) FROM bids WHERE auction_id = a.id) as bid_count,
                       0 as watch_count
                FROM auctions a
                JOIN categories c ON a.category_id = c.id
                JOIN users u ON a.user_id = u.id
                WHERE a.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get auction images
     */
    public function getAuctionImages($auctionId) {
        $sql = "SELECT * FROM auction_images WHERE auction_id = :auction_id ORDER BY is_primary DESC, sort_order ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':auction_id', (int)$auctionId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get auction bids
     */
    public function getAuctionBids($auctionId, $limit = 10) {
        $sql = "SELECT b.*, u.username 
                FROM bids b
                JOIN users u ON b.user_id = u.id
                WHERE b.auction_id = :auction_id
                ORDER BY b.amount DESC, b.bid_time DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':auction_id', (int)$auctionId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Place a bid
     */
    public function placeBid($auctionId, $userId, $amount) {
        try {
            $this->db->beginTransaction();

            // Check if auction is still active
            $auction = $this->getAuctionById($auctionId);
            if (!$auction || $auction['status'] !== 'active' || strtotime($auction['end_time']) <= time()) {
                throw new Exception('Huutokauppa ei ole enää aktiivinen');
            }

            // Check if bid is high enough
            $minBid = $auction['current_price'] + $auction['bid_increment'];
            if ($amount < $minBid) {
                throw new Exception('Tarjous on liian pieni. Vähimmäistarjous: ' . number_format($minBid, 2) . ' €');
            }

            // Insert bid
            $sql = "INSERT INTO bids (auction_id, user_id, amount) VALUES (:auction_id, :user_id, :amount)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':auction_id' => $auctionId,
                ':user_id' => $userId,
                ':amount' => $amount
            ]);

            // Update auction current price
            $sql = "UPDATE auctions SET current_price = :amount WHERE id = :auction_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':amount' => $amount,
                ':auction_id' => $auctionId
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Increment view count
     */
    public function incrementViews($auctionId) {
        $sql = "UPDATE auctions SET views = views + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', (int)$auctionId, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Search auctions
     */
    public function searchAuctions($query, $limit = 20, $offset = 0) {
        $sql = "SELECT a.*, c.name as category_name, c.slug as category_slug,
                       u.username as seller_username,
                       (SELECT image_path FROM auction_images WHERE auction_id = a.id AND is_primary = 1 LIMIT 1) as primary_image,
                       (SELECT COUNT(*) FROM bids WHERE auction_id = a.id) as bid_count
                FROM auctions a
                JOIN categories c ON a.category_id = c.id
                JOIN users u ON a.user_id = u.id
                WHERE a.status = 'active' AND a.end_time > NOW()
                AND (a.title LIKE :query OR a.description LIKE :query)
                ORDER BY a.end_time ASC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $searchTerm = '%' . $query . '%';
        $stmt->bindValue(':query', $searchTerm, PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Create a new auction
     */
    public function createAuction($data) {
        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO auctions (
                        user_id, category_id, title, description, 
                        starting_price, current_price, reserve_price, buy_now_price, 
                        bid_increment, end_time, status, location, condition_description
                    ) VALUES (
                        :user_id, :category_id, :title, :description,
                        :starting_price, :current_price, :reserve_price, :buy_now_price,
                        :bid_increment, :end_time, :status, :location, :condition_description
                    )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_id' => $data['user_id'],
                ':category_id' => $data['category_id'],
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':starting_price' => $data['starting_price'],
                ':current_price' => $data['starting_price'], // Initial current price = starting price
                ':reserve_price' => $data['reserve_price'] ?? null,
                ':buy_now_price' => $data['buy_now_price'] ?? null,
                ':bid_increment' => $data['bid_increment'] ?? 1.00,
                ':end_time' => $data['end_time'],
                ':status' => $data['status'] ?? 'active',
                ':location' => $data['location'] ?? null,
                ':condition_description' => $data['condition_description'] ?? null
            ]);

            $auctionId = $this->db->lastInsertId();

            // Add images if provided
            if (!empty($data['images'])) {
                foreach ($data['images'] as $index => $imagePath) {
                    $this->addAuctionImage($auctionId, $imagePath, $index === 0, $index);
                }
            }

            $this->db->commit();
            return $auctionId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Add an image to an auction
     */
    public function addAuctionImage($auctionId, $imagePath, $isPrimary = false, $sortOrder = 0) {
        $sql = "INSERT INTO auction_images (auction_id, image_path, is_primary, sort_order) 
                VALUES (:auction_id, :image_path, :is_primary, :sort_order)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':auction_id' => $auctionId,
            ':image_path' => $imagePath,
            ':is_primary' => $isPrimary ? 1 : 0,
            ':sort_order' => $sortOrder
        ]);
    }
    }

    /**
     * Get a default test user (for testing without login)
     */
    public function getDefaultTestUser() {
        $sql = "SELECT id FROM users LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $user = $stmt->fetch();
        
        if (!$user) {
            // Only allow automatic test user creation in explicitly enabled environments
            $allowTestUser = getenv('APP_ALLOW_TEST_USER');
            if ($allowTestUser !== '1') {
                throw new RuntimeException(
                    'Automatic test user creation is disabled. ' .
                    'Set APP_ALLOW_TEST_USER=1 in your environment if you want to allow this in development.'
                );
            }

            // Use a configured password if provided, otherwise generate a random one
            $password = getenv('TEST_USER_PASSWORD');
            if ($password === false || $password === '') {
                $password = bin2hex(random_bytes(16));
            }

            $hash = password_hash($password, PASSWORD_BCRYPT);

            // Create a test user if none exists
            $sql = "INSERT INTO users (username, email, password_hash, full_name) 
                    VALUES ('test_user', 'test@example.com', :hash, 'Test User')";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':hash' => $hash]);
            return $this->db->lastInsertId();
        }
        
        return $user['id'];
    }

    /**
     * Get all auctions (for admin interface)
     */
    public function getAllAuctions($limit = 100, $offset = 0) {
        $sql = "SELECT a.*, c.name as category_name, c.slug as category_slug,
                       u.username as seller_username,
                       (SELECT image_path FROM auction_images WHERE auction_id = a.id AND is_primary = 1 LIMIT 1) as primary_image,
                       (SELECT COUNT(*) FROM bids WHERE auction_id = a.id) as bid_count
                FROM auctions a
                JOIN categories c ON a.category_id = c.id
                JOIN users u ON a.user_id = u.id
                ORDER BY a.created_at DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Update auction data
     */
    public function updateAuction($id, $data) {
        $sql = "UPDATE auctions SET 
                    category_id = :category_id,
                    title = :title,
                    description = :description,
                    starting_price = :starting_price,
                    current_price = :current_price,
                    reserve_price = :reserve_price,
                    buy_now_price = :buy_now_price,
                    bid_increment = :bid_increment,
                    end_time = :end_time,
                    status = :status,
                    location = :location,
                    condition_description = :condition_description,
                    updated_at = NOW()
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':category_id' => $data['category_id'],
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':starting_price' => $data['starting_price'],
            ':current_price' => $data['current_price'],
            ':reserve_price' => $data['reserve_price'],
            ':buy_now_price' => $data['buy_now_price'],
            ':bid_increment' => $data['bid_increment'],
            ':end_time' => $data['end_time'],
            ':status' => $data['status'],
            ':location' => $data['location'],
            ':condition_description' => $data['condition_description']
        ]);
    }

    /**
     * Delete an auction image
     */
    public function deleteAuctionImage($imageId) {
        // First get the image path to delete the file
        $sql = "SELECT image_path FROM auction_images WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $imageId, PDO::PARAM_INT);
        $stmt->execute();
        $image = $stmt->fetch();

        if ($image) {
            // Delete the physical file
            $filePath = __DIR__ . '/../../' . ltrim($image['image_path'], '/');
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete from database
            $sql = "DELETE FROM auction_images WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $imageId]);
        }
        return false;
    }

    /**
     * Set primary image for an auction
     */
    public function setPrimaryImage($auctionId, $imageId) {
        try {
            $this->db->beginTransaction();

            // First, unset all primary images for this auction
            $sql = "UPDATE auction_images SET is_primary = 0 WHERE auction_id = :auction_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':auction_id' => $auctionId]);

            // Then set the new primary image
            $sql = "UPDATE auction_images SET is_primary = 1 WHERE id = :id AND auction_id = :auction_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id' => $imageId,
                ':auction_id' => $auctionId
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
