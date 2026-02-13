<?php
/**
 * Admin Controller
 */

class AdminController {
    
    public function index() {
        Security::requireAdmin();
        
        $db = Database::getInstance();
        
        $stats = [
            'users' => $db->fetch("SELECT COUNT(*) as count FROM users")['count'],
            'listings' => $db->fetch("SELECT COUNT(*) as count FROM listings WHERE status = 'active'")['count'],
            'bids' => $db->fetch("SELECT COUNT(*) as count FROM bids")['count'],
            'categories' => $db->fetch("SELECT COUNT(*) as count FROM categories")['count']
        ];
        
        require __DIR__ . '/../views/admin/index.php';
    }
    
    public function users() {
        Security::requireAdmin();
        
        $db = Database::getInstance();
        $users = $db->fetchAll("SELECT * FROM users ORDER BY created_at DESC");
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if (!Security::verifyToken($_POST['csrf_token'] ?? '')) {
                die('Invalid CSRF token');
            }
            
            $userId = intval($_POST['user_id'] ?? 0);
            $action = $_POST['action'];
            
            if ($action === 'ban') {
                $db->execute("UPDATE users SET status = 'banned' WHERE id = ?", [$userId]);
            } elseif ($action === 'activate') {
                $db->execute("UPDATE users SET status = 'active' WHERE id = ?", [$userId]);
            }
            
            header('Location: /admin/kayttajat');
            exit;
        }
        
        require __DIR__ . '/../views/admin/users.php';
    }
    
    public function listings() {
        Security::requireAdmin();
        
        $db = Database::getInstance();
        $listings = $db->fetchAll(
            "SELECT l.*, u.name as user_name, c.name as category_name
             FROM listings l
             LEFT JOIN users u ON l.user_id = u.id
             LEFT JOIN categories c ON l.category_id = c.id
             ORDER BY l.created_at DESC
             LIMIT 100"
        );
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if (!Security::verifyToken($_POST['csrf_token'] ?? '')) {
                die('Invalid CSRF token');
            }
            
            $listingId = intval($_POST['listing_id'] ?? 0);
            $action = $_POST['action'];
            
            if ($action === 'delete') {
                $db->execute("DELETE FROM listings WHERE id = ?", [$listingId]);
            } elseif ($action === 'archive') {
                $db->execute("UPDATE listings SET status = 'archived' WHERE id = ?", [$listingId]);
            }
            
            header('Location: /admin/ilmoitukset');
            exit;
        }
        
        require __DIR__ . '/../views/admin/listings.php';
    }
    
    public function categories() {
        Security::requireAdmin();
        
        $db = Database::getInstance();
        $categories = $db->fetchAll("SELECT * FROM categories ORDER BY sort_order");
        
        require __DIR__ . '/../views/admin/categories.php';
    }
    
    public function saveCategory() {
        Security::requireAdmin();
        
        if (!Security::verifyToken($_POST['csrf_token'] ?? '')) {
            die('Invalid CSRF token');
        }
        
        // Category CRUD implementation would go here
        header('Location: /admin/kategoriat');
        exit;
    }
    
    public function blog() {
        Security::requireAdmin();
        
        $db = Database::getInstance();
        $posts = $db->fetchAll("SELECT * FROM blog_posts ORDER BY published_at DESC");
        
        require __DIR__ . '/../views/admin/blog.php';
    }
    
    public function saveBlogPost() {
        Security::requireAdmin();
        
        if (!Security::verifyToken($_POST['csrf_token'] ?? '')) {
            die('Invalid CSRF token');
        }
        
        // Blog CRUD implementation would go here
        header('Location: /admin/blogi');
        exit;
    }
}
