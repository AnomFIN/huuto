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
                       (SELECT COUNT(*) FROM watchlist WHERE auction_id = a.id) as watch_count
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
}
