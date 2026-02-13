<?php
/**
 * Blog Controller
 */

class BlogController {
    
    public function index() {
        $db = Database::getInstance();
        
        $posts = $db->fetchAll(
            "SELECT * FROM blog_posts WHERE status = 'published' ORDER BY published_at DESC LIMIT 20"
        );
        
        require __DIR__ . '/../views/blog/index.php';
    }
    
    public function show($params) {
        $slug = $params['slug'] ?? '';
        $db = Database::getInstance();
        
        $post = $db->fetch(
            "SELECT * FROM blog_posts WHERE slug = ? AND status = 'published'",
            [$slug]
        );
        
        if (!$post) {
            http_response_code(404);
            require __DIR__ . '/../views/404.php';
            return;
        }
        
        require __DIR__ . '/../views/blog/show.php';
    }
}
