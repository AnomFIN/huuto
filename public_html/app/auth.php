<?php
/**
 * Authentication System
 * Handles login, registration, OAuth, and email verification
 */

class Auth {
    private $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    // ============================================================
    // Password-based Authentication
    // ============================================================
    
    /**
     * Register a new user with password
     */
    public function register($email, $password, $fullName, $username = null) {
        // Validate input
        if (!validate_email($email)) {
            return ['success' => false, 'error' => 'Virheellinen sähköpostiosoite'];
        }
        
        $passwordValidation = validate_password($password);
        if (!$passwordValidation['valid']) {
            return ['success' => false, 'error' => $passwordValidation['error']];
        }
        
        // Check if email already exists
        if ($this->emailExists($email)) {
            return ['success' => false, 'error' => 'Sähköpostiosoite on jo käytössä'];
        }
        
        // Generate username if not provided
        if (!$username) {
            $username = $this->generateUsername($fullName, $email);
        }
        
        // Check if username exists
        if ($this->usernameExists($username)) {
            $username = $username . rand(1000, 9999);
        }
        
        try {
            // Hash password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
            // Generate verification token
            $verificationToken = bin2hex(random_bytes(32));
            
            // Insert user
            $userId = $this->db->insert('users', [
                'username' => $username,
                'email' => $email,
                'password_hash' => $passwordHash,
                'full_name' => $fullName,
                'email_verified' => false,
                'status' => 'pending'
            ]);
            
            // Create verification token
            $this->db->insert('email_tokens', [
                'user_id' => $userId,
                'email' => $email,
                'token' => $verificationToken,
                'type' => 'verification',
                'expires_at' => date('Y-m-d H:i:s', time() + 86400), // 24 hours
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null
            ]);
            
            // Send verification email
            email()->sendVerification($email, $verificationToken);
            
            // Log audit event
            audit_log('user_registered', 'user', $userId, ['email' => $email]);
            
            return [
                'success' => true,
                'user_id' => $userId,
                'email' => $email,
                'message' => 'Rekisteröinti onnistui! Tarkista sähköpostisi vahvistaaksesi tilisi.'
            ];
        } catch (Exception $e) {
            error_log('Registration failed: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Rekisteröinti epäonnistui. Yritä uudelleen.'];
        }
    }
    
    /**
     * Verify email with token
     */
    public function verifyEmail($token) {
        $tokenData = $this->db->queryOne(
            'SELECT * FROM email_tokens WHERE token = ? AND type = ? AND used_at IS NULL',
            [$token, 'verification']
        );
        
        if (!$tokenData) {
            return ['success' => false, 'error' => 'Virheellinen tai vanhentunut vahvistuslinkki'];
        }
        
        // Check if expired
        if (strtotime($tokenData['expires_at']) < time()) {
            return ['success' => false, 'error' => 'Vahvistuslinkki on vanhentunut'];
        }
        
        try {
            // Mark token as used
            $this->db->update('email_tokens', ['used_at' => date('Y-m-d H:i:s')], 'id = ?', [$tokenData['id']]);
            
            // Activate user
            $this->db->update('users', [
                'email_verified' => true,
                'status' => 'active'
            ], 'id = ?', [$tokenData['user_id']]);
            
            audit_log('email_verified', 'user', $tokenData['user_id']);
            
            return ['success' => true, 'message' => 'Sähköpostiosoite vahvistettu!'];
        } catch (Exception $e) {
            error_log('Email verification failed: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Vahvistus epäonnistui'];
        }
    }
    
    /**
     * Login with email and password
     */
    public function login($email, $password, $rememberMe = false) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        // Rate limiting
        if (!rate_limit_login($ip . ':' . $email)) {
            return ['success' => false, 'error' => 'Liian monta kirjautumisyritystä. Yritä myöhemmin uudelleen.'];
        }
        
        // Find user
        $user = $this->db->queryOne('SELECT * FROM users WHERE email = ?', [$email]);
        
        if (!$user) {
            $this->logLoginAttempt($ip, $email, false);
            return ['success' => false, 'error' => 'Virheellinen sähköpostiosoite tai salasana'];
        }
        
        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            $this->logLoginAttempt($ip, $email, false);
            return ['success' => false, 'error' => 'Virheellinen sähköpostiosoite tai salasana'];
        }
        
        // Check if user is banned
        if ($user['status'] === 'banned' || $user['status'] === 'suspended') {
            return ['success' => false, 'error' => 'Tilisi on estetty'];
        }
        
        // Check if email is verified (optional - can be enforced or not)
        if (!$user['email_verified'] && env('REQUIRE_EMAIL_VERIFICATION', false)) {
            return ['success' => false, 'error' => 'Vahvista sähköpostiosoitteesi ennen kirjautumista'];
        }
        
        // Successful login
        $this->logLoginAttempt($ip, $email, true);
        $this->createSession($user['id'], $rememberMe);
        
        // Update last login
        $this->db->update('users', ['last_login_at' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);
        
        audit_log('login', 'user', $user['id'], ['method' => 'password']);
        
        return ['success' => true, 'user' => $user];
    }
    
    // ============================================================
    // Magic Code Login
    // ============================================================
    
    /**
     * Send magic login code to email
     */
    public function sendMagicCode($email) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        // Rate limiting
        if (!rate_limit_check($ip . ':magic_code', 'magic_code', 3, 300)) { // 3 attempts per 5 minutes
            return ['success' => false, 'error' => 'Liian monta yritystä. Yritä myöhemmin uudelleen.'];
        }
        
        // Check if email exists
        if (!$this->emailExists($email)) {
            // Don't reveal if email exists or not
            return ['success' => true, 'message' => 'Jos sähköpostiosoite on rekisteröity, lähetimme sinulle kirjautumiskoodin.'];
        }
        
        // Generate 6-digit code
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $token = bin2hex(random_bytes(32));
        
        try {
            // Delete old magic codes for this email
            $this->db->execute(
                'DELETE FROM email_tokens WHERE email = ? AND type = ? AND used_at IS NULL',
                [$email, 'magic_login']
            );
            
            // Create new magic code
            $this->db->insert('email_tokens', [
                'email' => $email,
                'token' => $token,
                'code' => $code,
                'type' => 'magic_login',
                'expires_at' => date('Y-m-d H:i:s', time() + EMAIL_CODE_EXPIRY),
                'ip_address' => $ip
            ]);
            
            // Send email
            email()->sendMagicCode($email, $code);
            
            return [
                'success' => true,
                'message' => 'Lähetimme kirjautumiskoodin sähköpostiisi. Koodi on voimassa 10 minuuttia.'
            ];
        } catch (Exception $e) {
            error_log('Magic code send failed: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Koodin lähetys epäonnistui'];
        }
    }
    
    /**
     * Verify magic code and login
     */
    public function verifyMagicCode($email, $code) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        // Rate limiting
        if (!rate_limit_check($ip . ':magic_verify', 'magic_verify', EMAIL_CODE_MAX_ATTEMPTS, EMAIL_CODE_EXPIRY)) {
            return ['success' => false, 'error' => 'Liian monta yritystä'];
        }
        
        // Find valid code
        $tokenData = $this->db->queryOne(
            'SELECT * FROM email_tokens WHERE email = ? AND code = ? AND type = ? AND used_at IS NULL AND expires_at > ?',
            [$email, $code, 'magic_login', date('Y-m-d H:i:s')]
        );
        
        if (!$tokenData) {
            return ['success' => false, 'error' => 'Virheellinen tai vanhentunut koodi'];
        }
        
        // Get user
        $user = $this->db->queryOne('SELECT * FROM users WHERE email = ?', [$email]);
        
        if (!$user) {
            return ['success' => false, 'error' => 'Käyttäjää ei löytynyt'];
        }
        
        try {
            // Mark code as used
            $this->db->update('email_tokens', ['used_at' => date('Y-m-d H:i:s')], 'id = ?', [$tokenData['id']]);
            
            // Auto-verify email if not verified
            if (!$user['email_verified']) {
                $this->db->update('users', [
                    'email_verified' => true,
                    'status' => 'active'
                ], 'id = ?', [$user['id']]);
            }
            
            // Create session
            $this->createSession($user['id']);
            
            // Update last login
            $this->db->update('users', ['last_login_at' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);
            
            audit_log('login', 'user', $user['id'], ['method' => 'magic_code']);
            
            return ['success' => true, 'user' => $user];
        } catch (Exception $e) {
            error_log('Magic code verify failed: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Kirjautuminen epäonnistui'];
        }
    }
    
    // ============================================================
    // Password Reset
    // ============================================================
    
    /**
     * Request password reset
     */
    public function requestPasswordReset($email) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        // Rate limiting
        if (!rate_limit_check($ip . ':password_reset', 'password_reset', 3, 3600)) {
            return ['success' => false, 'error' => 'Liian monta yritystä'];
        }
        
        // Check if email exists (don't reveal)
        $user = $this->db->queryOne('SELECT * FROM users WHERE email = ?', [$email]);
        
        if (!$user) {
            // Don't reveal if email exists
            return ['success' => true, 'message' => 'Jos sähköpostiosoite on rekisteröity, lähetimme sinulle ohjeet salasanan nollausta varten.'];
        }
        
        try {
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            
            // Delete old reset tokens
            $this->db->execute(
                'DELETE FROM password_resets WHERE user_id = ? AND used_at IS NULL',
                [$user['id']]
            );
            
            // Create reset token
            $this->db->insert('password_resets', [
                'user_id' => $user['id'],
                'token' => $token,
                'expires_at' => date('Y-m-d H:i:s', time() + 3600), // 1 hour
                'ip_address' => $ip
            ]);
            
            // Send email
            email()->sendPasswordReset($email, $token);
            
            audit_log('password_reset_requested', 'user', $user['id']);
            
            return ['success' => true, 'message' => 'Lähetimme ohjeet salasanan nollausta varten sähköpostiisi.'];
        } catch (Exception $e) {
            error_log('Password reset request failed: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Pyyntö epäonnistui'];
        }
    }
    
    /**
     * Reset password with token
     */
    public function resetPassword($token, $newPassword) {
        // Validate password
        $passwordValidation = validate_password($newPassword);
        if (!$passwordValidation['valid']) {
            return ['success' => false, 'error' => $passwordValidation['error']];
        }
        
        // Find valid token
        $tokenData = $this->db->queryOne(
            'SELECT * FROM password_resets WHERE token = ? AND used_at IS NULL AND expires_at > ?',
            [$token, date('Y-m-d H:i:s')]
        );
        
        if (!$tokenData) {
            return ['success' => false, 'error' => 'Virheellinen tai vanhentunut linkki'];
        }
        
        try {
            // Hash new password
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Update password
            $this->db->update('users', ['password_hash' => $passwordHash], 'id = ?', [$tokenData['user_id']]);
            
            // Mark token as used
            $this->db->update('password_resets', ['used_at' => date('Y-m-d H:i:s')], 'id = ?', [$tokenData['id']]);
            
            audit_log('password_reset_completed', 'user', $tokenData['user_id']);
            
            return ['success' => true, 'message' => 'Salasana vaihdettu onnistuneesti!'];
        } catch (Exception $e) {
            error_log('Password reset failed: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Salasanan vaihto epäonnistui'];
        }
    }
    
    // ============================================================
    // Google OAuth
    // ============================================================
    
    /**
     * Get Google OAuth URL
     */
    public function getGoogleAuthUrl() {
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;
        
        $params = [
            'client_id' => GOOGLE_CLIENT_ID,
            'redirect_uri' => GOOGLE_REDIRECT_URI,
            'response_type' => 'code',
            'scope' => 'email profile',
            'state' => $state,
            'access_type' => 'online'
        ];
        
        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }
    
    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback($code, $state) {
        // Verify state
        if (!isset($_SESSION['oauth_state']) || $state !== $_SESSION['oauth_state']) {
            return ['success' => false, 'error' => 'Invalid state parameter'];
        }
        
        unset($_SESSION['oauth_state']);
        
        try {
            // Exchange code for token
            $tokenData = $this->getGoogleAccessToken($code);
            
            if (!$tokenData) {
                return ['success' => false, 'error' => 'Failed to get access token'];
            }
            
            // Get user info
            $userInfo = $this->getGoogleUserInfo($tokenData['access_token']);
            
            if (!$userInfo) {
                return ['success' => false, 'error' => 'Failed to get user info'];
            }
            
            // Find or create user
            $user = $this->findOrCreateGoogleUser($userInfo);
            
            if (!$user) {
                return ['success' => false, 'error' => 'Failed to create user'];
            }
            
            // Create session
            $this->createSession($user['id']);
            
            // Update last login
            $this->db->update('users', ['last_login_at' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);
            
            audit_log('login', 'user', $user['id'], ['method' => 'google_oauth']);
            
            return ['success' => true, 'user' => $user];
        } catch (Exception $e) {
            error_log('Google OAuth callback failed: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Authentication failed'];
        }
    }
    
    /**
     * Get Google access token
     */
    private function getGoogleAccessToken($code) {
        $data = [
            'code' => $code,
            'client_id' => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri' => GOOGLE_REDIRECT_URI,
            'grant_type' => 'authorization_code'
        ];
        
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
    
    /**
     * Get Google user info
     */
    private function getGoogleUserInfo($accessToken) {
        $ch = curl_init('https://www.googleapis.com/oauth2/v2/userinfo');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
    
    /**
     * Find or create user from Google OAuth
     */
    private function findOrCreateGoogleUser($userInfo) {
        // Check if user exists with this Google ID
        $user = $this->db->queryOne('SELECT * FROM users WHERE google_id = ?', [$userInfo['id']]);
        
        if ($user) {
            return $user;
        }
        
        // Check if user exists with this email
        $user = $this->db->queryOne('SELECT * FROM users WHERE email = ?', [$userInfo['email']]);
        
        if ($user) {
            // Link Google ID to existing user
            $this->db->update('users', [
                'google_id' => $userInfo['id'],
                'email_verified' => true,
                'status' => 'active'
            ], 'id = ?', [$user['id']]);
            
            return $this->db->queryOne('SELECT * FROM users WHERE id = ?', [$user['id']]);
        }
        
        // Create new user
        $username = $this->generateUsername($userInfo['name'], $userInfo['email']);
        
        if ($this->usernameExists($username)) {
            $username = $username . rand(1000, 9999);
        }
        
        $userId = $this->db->insert('users', [
            'username' => $username,
            'email' => $userInfo['email'],
            'password_hash' => null, // No password for OAuth users
            'full_name' => $userInfo['name'],
            'google_id' => $userInfo['id'],
            'avatar_url' => $userInfo['picture'] ?? null,
            'email_verified' => true,
            'status' => 'active'
        ]);
        
        audit_log('user_registered', 'user', $userId, ['method' => 'google_oauth', 'email' => $userInfo['email']]);
        
        return $this->db->queryOne('SELECT * FROM users WHERE id = ?', [$userId]);
    }
    
    // ============================================================
    // Session Management
    // ============================================================
    
    /**
     * Create user session
     */
    private function createSession($userId, $rememberMe = false) {
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $userId;
        $_SESSION['logged_in_at'] = time();
        
        if ($rememberMe) {
            // Extend session lifetime for "remember me"
            $sessionLifetime = 30 * 24 * 3600; // 30 days
            session_set_cookie_params($sessionLifetime);
        }
    }
    
    /**
     * Logout user
     */
    public function logout() {
        $userId = current_user_id();
        
        if ($userId) {
            audit_log('logout', 'user', $userId);
        }
        
        $_SESSION = [];
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        session_destroy();
    }
    
    // ============================================================
    // Helper Methods
    // ============================================================
    
    /**
     * Check if email exists
     */
    private function emailExists($email) {
        return $this->db->exists('users', 'email = ?', [$email]);
    }
    
    /**
     * Check if username exists
     */
    private function usernameExists($username) {
        return $this->db->exists('users', 'username = ?', [$username]);
    }
    
    /**
     * Generate username from name or email
     */
    private function generateUsername($name, $email) {
        // Try to use name first
        $username = strtolower(preg_replace('/[^a-z0-9]+/i', '', $name));
        
        if (empty($username)) {
            // Fall back to email prefix
            $username = strtolower(preg_replace('/[^a-z0-9]+/i', '', explode('@', $email)[0]));
        }
        
        // Ensure minimum length
        if (strlen($username) < 3) {
            $username = 'user' . rand(1000, 9999);
        }
        
        return substr($username, 0, 50);
    }
    
    /**
     * Log login attempt
     */
    private function logLoginAttempt($ip, $email, $success) {
        try {
            $this->db->insert('login_attempts', [
                'ip_address' => $ip,
                'email' => $email,
                'success' => $success ? 1 : 0,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
        } catch (Exception $e) {
            error_log('Failed to log login attempt: ' . $e->getMessage());
        }
    }
}

/**
 * Global helper to get Auth instance
 */
function auth() {
    static $instance = null;
    if ($instance === null) {
        $instance = new Auth();
    }
    return $instance;
}
