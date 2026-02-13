<?php
/**
 * Category model
 */
class Category {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all categories
     */
    public function getAllCategories() {
        $sql = "SELECT c.*, 
                       (SELECT COUNT(*) FROM auctions a WHERE a.category_id = c.id AND a.status = 'active' AND a.end_time > NOW()) as active_count
                FROM categories c
                ORDER BY c.sort_order ASC, c.name ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get category by slug
     */
    public function getCategoryBySlug($slug) {
        $sql = "SELECT * FROM categories WHERE slug = :slug";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }
}
