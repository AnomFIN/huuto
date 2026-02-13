<?php
/**
 * Category Controller
 */

class CategoryController {
    
    public function index() {
        $db = Database::getInstance();
        
        $categories = $db->fetchAll(
            "SELECT c.*, COUNT(l.id) as listing_count
             FROM categories c
             LEFT JOIN listings l ON c.id = l.category_id AND l.status = 'active' AND l.ends_at > NOW()
             GROUP BY c.id
             ORDER BY c.sort_order"
        );
        
        require __DIR__ . '/../views/categories/index.php';
    }
    
    public function show($params) {
        $slug = $params['slug'] ?? '';
        $db = Database::getInstance();
        
        $category = $db->fetch("SELECT * FROM categories WHERE slug = ?", [$slug]);
        
        if (!$category) {
            http_response_code(404);
            require __DIR__ . '/../views/404.php';
            return;
        }
        
        // Get filters
        $minPrice = $_GET['min_price'] ?? '';
        $maxPrice = $_GET['max_price'] ?? '';
        $region = $_GET['region'] ?? '';
        $sortBy = $_GET['sort'] ?? 'ending';
        
        $sql = "SELECT l.*, 
                       (SELECT path FROM listing_images WHERE listing_id = l.id ORDER BY sort_order LIMIT 1) as image
                FROM listings l
                WHERE l.category_id = ? AND l.status = 'active' AND l.ends_at > NOW()";
        
        $params = [$category['id']];
        
        if ($minPrice !== '') {
            $sql .= " AND l.current_price >= ?";
            $params[] = $minPrice;
        }
        
        if ($maxPrice !== '') {
            $sql .= " AND l.current_price <= ?";
            $params[] = $maxPrice;
        }
        
        if ($region !== '') {
            $sql .= " AND l.region = ?";
            $params[] = $region;
        }
        
        switch ($sortBy) {
            case 'price_asc':
                $sql .= " ORDER BY l.current_price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY l.current_price DESC";
                break;
            case 'newest':
                $sql .= " ORDER BY l.created_at DESC";
                break;
            case 'ending':
            default:
                $sql .= " ORDER BY l.ends_at ASC";
                break;
        }
        
        $listings = $db->fetchAll($sql, $params);
        
        // Get available regions for filter
        $regions = $db->fetchAll(
            "SELECT DISTINCT region FROM listings WHERE category_id = ? AND region IS NOT NULL ORDER BY region",
            [$category['id']]
        );
        
        require __DIR__ . '/../views/categories/show.php';
    }
}
