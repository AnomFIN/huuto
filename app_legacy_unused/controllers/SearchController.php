<?php
/**
 * Search Controller
 */

class SearchController {
    
    public function index() {
        $db = Database::getInstance();
        
        $query = trim($_GET['q'] ?? '');
        $categoryId = $_GET['category'] ?? '';
        $endingSoon = isset($_GET['ending_soon']);
        
        $sql = "SELECT l.*, c.name as category_name, c.slug as category_slug,
                       (SELECT path FROM listing_images WHERE listing_id = l.id ORDER BY sort_order LIMIT 1) as image
                FROM listings l
                LEFT JOIN categories c ON l.category_id = c.id
                WHERE l.status = 'active' AND l.ends_at > NOW()";
        
        $params = [];
        
        if ($query !== '') {
            $sql .= " AND (l.title LIKE ? OR l.description LIKE ?)";
            $searchTerm = '%' . $query . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if ($categoryId !== '') {
            $sql .= " AND l.category_id = ?";
            $params[] = $categoryId;
        }
        
        if ($endingSoon) {
            $sql .= " AND l.ends_at <= DATE_ADD(NOW(), INTERVAL 24 HOUR)";
        }
        
        $sql .= " ORDER BY l.ends_at ASC LIMIT 100";
        
        $results = $db->fetchAll($sql, $params);
        
        // Get categories for filter
        $categories = $db->fetchAll("SELECT * FROM categories ORDER BY name");
        
        require __DIR__ . '/../views/search.php';
    }
}
