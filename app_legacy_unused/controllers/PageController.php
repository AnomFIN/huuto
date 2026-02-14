<?php
/**
 * Page Controller
 */

class PageController {
    
    public function show($params) {
        $slug = $params['slug'] ?? '';
        $db = Database::getInstance();
        
        $page = $db->fetch("SELECT * FROM pages WHERE slug = ?", [$slug]);
        
        if (!$page) {
            http_response_code(404);
            require __DIR__ . '/../views/404.php';
            return;
        }
        
        require __DIR__ . '/../views/page.php';
    }
}
