<?php
/**
 * Huuto - Simple Router
 */

class Router {
    private $routes = [];
    private $basePath = '';
    
    public function __construct($basePath = '') {
        $this->basePath = $basePath;
    }
    
    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
    }
    
    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
    }
    
    private function addRoute($method, $path, $handler) {
        $pattern = $this->pathToPattern($path);
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'path' => $path,
            'handler' => $handler
        ];
    }
    
    private function pathToPattern($path) {
        // Convert route parameters like :id to regex patterns
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
    
    public function dispatch() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'];
        
        // Remove query string and base path
        $requestUri = parse_url($requestUri, PHP_URL_PATH);
        if ($this->basePath && strpos($requestUri, $this->basePath) === 0) {
            $requestUri = substr($requestUri, strlen($this->basePath));
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }
            
            if (preg_match($route['pattern'], $requestUri, $matches)) {
                // Extract named parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                $handler = $route['handler'];
                
                if (is_callable($handler)) {
                    return call_user_func_array($handler, [$params]);
                } elseif (is_string($handler) && strpos($handler, '@') !== false) {
                    // Controller@method format
                    list($controller, $method) = explode('@', $handler);
                    $controllerFile = __DIR__ . '/../controllers/' . $controller . '.php';
                    
                    if (file_exists($controllerFile)) {
                        require_once $controllerFile;
                        $controllerInstance = new $controller();
                        return call_user_func_array([$controllerInstance, $method], [$params]);
                    }
                }
            }
        }
        
        // 404 Not Found
        http_response_code(404);
        require __DIR__ . '/../views/404.php';
    }
}
