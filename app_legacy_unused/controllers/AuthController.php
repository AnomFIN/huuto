<?php
/**
 * Auth Controller
 */

class AuthController {
    
    public function register() {
        if (Security::isLoggedIn()) {
            header('Location: /');
            exit;
        }
        require __DIR__ . '/../views/auth/register.php';
    }
    
    public function doRegister() {
        if (!isset($_POST['csrf_token']) || !Security::verifyToken($_POST['csrf_token'])) {
            die('Invalid CSRF token');
        }
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $name = trim($_POST['name'] ?? '');
        
        $errors = [];
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Virheellinen sähköpostiosoite';
        }
        
        if (strlen($password) < 6) {
            $errors[] = 'Salasanan tulee olla vähintään 6 merkkiä';
        }
        
        if (empty($name)) {
            $errors[] = 'Nimi vaaditaan';
        }
        
        if (empty($errors)) {
            $db = Database::getInstance();
            
            // Check if email exists
            $existing = $db->fetch("SELECT id FROM users WHERE email = ?", [$email]);
            if ($existing) {
                $errors[] = 'Sähköpostiosoite on jo käytössä';
            } else {
                // Create user
                $token = bin2hex(random_bytes(32));
                $db->execute(
                    "INSERT INTO users (email, password_hash, name, verification_token, status) VALUES (?, ?, ?, ?, 'pending')",
                    [$email, password_hash($password, PASSWORD_DEFAULT), $name, $token]
                );
                
                $_SESSION['verification_token'] = $token;
                $_SESSION['verification_email'] = $email;
                header('Location: /vahvista/' . $token);
                exit;
            }
        }
        
        $_SESSION['register_errors'] = $errors;
        $_SESSION['register_data'] = ['email' => $email, 'name' => $name];
        header('Location: /rekisteroidy');
        exit;
    }
    
    public function login() {
        if (Security::isLoggedIn()) {
            header('Location: /');
            exit;
        }
        require __DIR__ . '/../views/auth/login.php';
    }
    
    public function doLogin() {
        if (!isset($_POST['csrf_token']) || !Security::verifyToken($_POST['csrf_token'])) {
            die('Invalid CSRF token');
        }
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $ip = Security::getClientIp();
        
        // Check rate limit
        if (!Security::checkRateLimit($ip, $email)) {
            $_SESSION['login_error'] = 'Liian monta yritystä. Odota hetki ja yritä uudelleen.';
            header('Location: /kirjaudu');
            exit;
        }
        
        $db = Database::getInstance();
        $user = $db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['status'] === 'active') {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                $redirect = $_SESSION['redirect_after_login'] ?? '/';
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
                exit;
            } else {
                $_SESSION['login_error'] = 'Tili ei ole aktiivinen';
            }
        } else {
            Security::logLoginAttempt($ip, $email);
            $_SESSION['login_error'] = 'Virheellinen sähköposti tai salasana';
        }
        
        header('Location: /kirjaudu');
        exit;
    }
    
    public function logout() {
        session_destroy();
        header('Location: /');
        exit;
    }
    
    public function verify($params) {
        $token = $params['token'] ?? '';
        
        if ($token) {
            $db = Database::getInstance();
            $user = $db->fetch("SELECT * FROM users WHERE verification_token = ?", [$token]);
            
            if ($user) {
                $db->execute(
                    "UPDATE users SET status = 'active', verification_token = NULL WHERE id = ?",
                    [$user['id']]
                );
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                $_SESSION['success_message'] = 'Tilisi on nyt vahvistettu!';
                header('Location: /');
                exit;
            }
        }
        
        $_SESSION['error_message'] = 'Virheellinen vahvistustunnus';
        header('Location: /');
        exit;
    }
}
