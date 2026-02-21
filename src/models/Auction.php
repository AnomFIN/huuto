<?php
/**
 * Auction model - handles all auction-related database operations
 */
class Auction {
    private $db;
    private static $captionColumnEnsured = false;
    private static $sellerCommitmentColumnEnsured = false;
    private static $metadataTableEnsured = false;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->ensureImageCaptionColumn();
        $this->ensureSellerCommitmentColumn();
        $this->ensureAuctionMetadataTable();
    }

    private function ensureImageCaptionColumn(): void
    {
        if (self::$captionColumnEnsured) {
            return;
        }

        try {
            $this->db->exec("ALTER TABLE auction_images ADD COLUMN IF NOT EXISTS caption VARCHAR(255) NULL AFTER image_path");
        } catch (Throwable $exception) {
            // Non-fatal in case DB user has limited ALTER permissions.
        }

        self::$captionColumnEnsured = true;
    }

    private function ensureSellerCommitmentColumn(): void
    {
        if (self::$sellerCommitmentColumnEnsured) {
            return;
        }

        try {
            $this->db->exec("ALTER TABLE auctions ADD COLUMN IF NOT EXISTS seller_commitment BOOLEAN DEFAULT FALSE");
        } catch (Throwable $exception) {
            // Non-fatal in case DB user has limited ALTER permissions or legacy server limitations.
        }

        self::$sellerCommitmentColumnEnsured = true;
    }

    private function ensureAuctionMetadataTable(): void
    {
        if (self::$metadataTableEnsured) {
            return;
        }

        try {
            $this->db->exec("CREATE TABLE IF NOT EXISTS auction_metadata (
                id INT AUTO_INCREMENT PRIMARY KEY,
                auction_id INT NOT NULL,
                field_name VARCHAR(100) NOT NULL,
                field_value TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_auction_field (auction_id, field_name),
                INDEX idx_auction (auction_id),
                INDEX idx_field (field_name)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        } catch (Throwable $exception) {
            // Non-fatal; metadata writes are guarded in createAuction.
        }

        self::$metadataTableEnsured = true;
    }

    /**
     * Get popular auctions (most bids/views)
     */
    public function getPopularAuctions($limit = 20) {
        $sql = "SELECT a.*, 
                       COALESCE(c.name, 'Luokittelematon') as category_name, 
                       COALESCE(c.slug, 'other') as category_slug,
                       COALESCE(u.username, 'Tuntematon myyjä') as seller_username,
                       (SELECT image_path
                        FROM auction_images
                        WHERE auction_id = a.id
                        ORDER BY is_primary DESC, sort_order ASC, id ASC
                        LIMIT 1) as primary_image,
                       (SELECT COUNT(*) FROM bids WHERE auction_id = a.id) as bid_count,
                       (SELECT MAX(amount) FROM bids WHERE auction_id = a.id) as highest_bid
                FROM auctions a
                LEFT JOIN categories c ON a.category_id = c.id
                LEFT JOIN users u ON a.user_id = u.id
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
        $sql = "SELECT a.*, 
                       COALESCE(c.name, 'Luokittelematon') as category_name, 
                       COALESCE(c.slug, 'other') as category_slug,
                       COALESCE(u.username, 'Tuntematon myyjä') as seller_username,
                       (SELECT image_path
                        FROM auction_images
                        WHERE auction_id = a.id
                        ORDER BY is_primary DESC, sort_order ASC, id ASC
                        LIMIT 1) as primary_image,
                       (SELECT COUNT(*) FROM bids WHERE auction_id = a.id) as bid_count,
                       (SELECT MAX(amount) FROM bids WHERE auction_id = a.id) as current_price
                FROM auctions a
                LEFT JOIN categories c ON a.category_id = c.id
                LEFT JOIN users u ON a.user_id = u.id
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
        $sql = "SELECT a.*, 
                       COALESCE(c.name, 'Luokittelematon') as category_name, 
                       COALESCE(c.slug, 'other') as category_slug,
                       COALESCE(u.username, 'Tuntematon myyjä') as seller_username,
                       (SELECT image_path
                        FROM auction_images
                        WHERE auction_id = a.id
                        ORDER BY is_primary DESC, sort_order ASC, id ASC
                        LIMIT 1) as primary_image,
                       (SELECT COUNT(*) FROM bids WHERE auction_id = a.id) as bid_count
                FROM auctions a
                LEFT JOIN categories c ON a.category_id = c.id
                LEFT JOIN users u ON a.user_id = u.id
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
                       (SELECT image_path
                        FROM auction_images
                        WHERE auction_id = a.id
                        ORDER BY is_primary DESC, sort_order ASC, id ASC
                        LIMIT 1) as primary_image,
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
                       (SELECT image_path
                        FROM auction_images
                        WHERE auction_id = a.id
                        ORDER BY is_primary DESC, sort_order ASC, id ASC
                        LIMIT 1) as primary_image,
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
                        bid_increment, end_time, status, location, condition_description,
                        seller_commitment
                    ) VALUES (
                        :user_id, :category_id, :title, :description,
                        :starting_price, :current_price, :reserve_price, :buy_now_price,
                        :bid_increment, :end_time, :status, :location, :condition_description,
                        :seller_commitment
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
                ':condition_description' => $data['condition_description'] ?? null,
                ':seller_commitment' => $data['seller_commitment'] ?? false
            ]);

            $auctionId = $this->db->lastInsertId();

            // Add images if provided
            if (!empty($data['images'])) {
                foreach ($data['images'] as $index => $imageItem) {
                    if (is_array($imageItem)) {
                        $imagePath = (string)($imageItem['path'] ?? '');
                        $caption = isset($imageItem['caption']) ? (string)$imageItem['caption'] : null;
                    } else {
                        $imagePath = (string)$imageItem;
                        $caption = null;
                    }

                    if ($imagePath === '') {
                        continue;
                    }

                    $this->addAuctionImage($auctionId, $imagePath, $index === 0, $index, $caption);
                }
            }
            
            // Add category-specific metadata
            if (!empty($data['metadata'])) {
                foreach ($data['metadata'] as $fieldName => $fieldValue) {
                    if ($fieldValue !== '' && $fieldValue !== null) {
                        try {
                            $this->addAuctionMetadata($auctionId, $fieldName, $fieldValue);
                        } catch (Throwable $exception) {
                            // Metadata is optional; ignore failures to keep auction creation working.
                        }
                    }
                }
            }

            $this->db->commit();
            return $auctionId;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Add an image to an auction
     */
    public function addAuctionImage($auctionId, $imagePath, $isPrimary = false, $sortOrder = 0, $caption = null) {
        $this->ensureImageCaptionColumn();

        $sql = "INSERT INTO auction_images (auction_id, image_path, caption, is_primary, sort_order) 
                VALUES (:auction_id, :image_path, :caption, :is_primary, :sort_order)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':auction_id' => $auctionId,
            ':image_path' => $imagePath,
            ':caption' => $caption !== null ? mb_substr(trim((string)$caption), 0, 255) : null,
            ':is_primary' => $isPrimary ? 1 : 0,
            ':sort_order' => $sortOrder
        ]);
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
                       (SELECT image_path
                        FROM auction_images
                        WHERE auction_id = a.id
                        ORDER BY is_primary DESC, sort_order ASC, id ASC
                        LIMIT 1) as primary_image,
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
    /**
     * Add metadata field for auction
     */
    public function addAuctionMetadata($auctionId, $fieldName, $fieldValue) {
        $sql = "INSERT INTO auction_metadata (auction_id, field_name, field_value) 
                VALUES (:auction_id, :field_name, :field_value)
                ON DUPLICATE KEY UPDATE field_value = VALUES(field_value), updated_at = CURRENT_TIMESTAMP";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':auction_id' => $auctionId,
            ':field_name' => $fieldName,
            ':field_value' => $fieldValue
        ]);
    }
    
    /**
     * Get metadata for auction
     */
    public function getAuctionMetadata($auctionId) {
        $sql = "SELECT field_name, field_value FROM auction_metadata WHERE auction_id = :auction_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':auction_id' => $auctionId]);
        
        $metadata = [];
        while ($row = $stmt->fetch()) {
            $metadata[$row['field_name']] = $row['field_value'];
        }
        
        return $metadata;
    }}
