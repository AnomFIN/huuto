<?php
/**
 * Auth Modal API Handler
 * Provides JSON responses for the premium auth modal
 */

// Set JSON response headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Enable CORS for same-origin requests
header('Access-Control-Allow-Origin: ' . (isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*'));
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    require_once __DIR__ . '/../bootstrap.php';
    
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? $_POST['action'] ?? '';
    
    switch ($action) {
        case 'login':
            handleLogin($input);
            break;
            
        case 'register':
            handleRegister($input);
            break;
            
        case 'check_email':
            handleEmailCheck($input);
            break;
            
        case 'check_password_strength':
            handlePasswordStrength($input);
            break;
            
        default:
            throw new Exception('Tuntematon toiminto');
    }
    
} catch (Throwable $e) {
    error_log("Auth Modal API Error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Palvelimella tapahtui virhe. Yritä hetken kuluttua uudelleen.'
    ]);
}

function handleLogin($data) {
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $remember = $data['remember'] ?? false;
    
    if (!$email || !$password) {
        echo json_encode([
            'success' => false,
            'message' => 'Sähköposti ja salasana ovat pakollisia'
        ]);
        return;
    }
    
    try {
        $result = auth()->login($email, $password, $remember);
        
        if (!empty($result['success'])) {
            // Set session redirect if needed
            if (!empty($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
            } else {
                $redirect = '/';
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Kirjautuminen onnistui!',
                'redirect' => $redirect
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => $result['error'] ?? 'Virheellinen sähköposti tai salasana'
            ]);
        }
        
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Kirjautuminen epäonnistui. Yritä hetken kuluttua uudelleen.'
        ]);
    }
}

function handleRegister($data) {
    $fullName = trim($data['full_name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $confirmPassword = $data['confirm_password'] ?? '';
    $termsAccepted = !empty($data['terms']);
    
    // Validate required fields
    if (!$fullName || !$email || !$password || !$confirmPassword) {
        echo json_encode([
            'success' => false,
            'message' => 'Täytä kaikki vaaditut kentät'
        ]);
        return;
    }
    
    // Validate password confirmation
    if ($password !== $confirmPassword) {
        echo json_encode([
            'success' => false,
            'message' => 'Salasanat eivät täsmää'
        ]);
        return;
    }
    
    // Validate terms acceptance
    if (!$termsAccepted) {
        echo json_encode([
            'success' => false,
            'message' => 'Hyväksy käyttöehdot jatkaaksesi'
        ]);
        return;
    }
    
    // Validate name
    if (strlen($fullName) < 2 || strlen($fullName) > 50) {
        echo json_encode([
            'success' => false,
            'message' => 'Nimen on oltava 2-50 merkkiä pitkä'
        ]);
        return;
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Syötä kelvollinen sähköpostiosoite'
        ]);
        return;
    }
    
    // Validate password strength
    if (strlen($password) < 8) {
        echo json_encode([
            'success' => false,
            'message' => 'Salasanassa oltava vähintään 8 merkkiä'
        ]);
        return;
    }
    
    try {
        $result = auth()->register($email, $password, $fullName);
        
        if (!empty($result['success'])) {
            // Auto-login after successful registration
            $loginResult = auth()->login($email, $password, false);
            
            echo json_encode([
                'success' => true,
                'message' => 'Rekisteröinti onnistui! Tervetuloa Huuto247:aan!',
                'redirect' => '/'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => $result['error'] ?? 'Rekisteröinti epäonnistui'
            ]);
        }
        
    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Rekisteröinti epäonnistui. Yritä hetken kuluttua uudelleen.'
        ]);
    }
}

function handleEmailCheck($data) {
    $email = trim($data['email'] ?? '');
    
    if (!$email) {
        echo json_encode(['available' => false, 'message' => 'Sähköposti on pakollinen']);
        return;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['available' => false, 'message' => 'Virheellinen sähköpostiosoite']);
        return;
    }
    
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $exists = $stmt->fetch() !== false;
        
        echo json_encode([
            'available' => !$exists,
            'message' => $exists ? 'Sähköpostiosoite on jo käytössä' : 'Sähköpostiosoite on käytettävissä'
        ]);
        
    } catch (Exception $e) {
        error_log("Email check error: " . $e->getMessage());
        echo json_encode(['available' => false, 'message' => 'Tarkistus epäonnistui']);
    }
}

function handlePasswordStrength($data) {
    $password = $data['password'] ?? '';
    
    if (!$password) {
        echo json_encode(['strength' => 0, 'message' => 'Syötä salasana']);
        return;
    }
    
    $score = 0;
    $feedback = [];
    
    // Length check
    if (strlen($password) >= 8) {
        $score++;
    } else {
        $feedback[] = 'Vähintään 8 merkkiä';
    }
    
    // Lowercase check
    if (preg_match('/[a-z]/', $password)) {
        $score++;
    } else {
        $feedback[] = 'Pienet kirjaimet (a-z)';
    }
    
    // Uppercase check
    if (preg_match('/[A-Z]/', $password)) {
        $score++;
    } else {
        $feedback[] = 'Isot kirjaimet (A-Z)';
    }
    
    // Number check
    if (preg_match('/[0-9]/', $password)) {
        $score++;
    } else {
        $feedback[] = 'Numerot (0-9)';
    }
    
    // Special character check
    if (preg_match('/[^A-Za-z0-9]/', $password)) {
        $score++;
    } else {
        $feedback[] = 'Erikoismerkit (!@#$%^&*)';
    }
    
    // Determine strength level
    $strengthLevels = [
        0 => 'Erittäin heikko',
        1 => 'Heikko', 
        2 => 'Kohtalainen',
        3 => 'Hyvä',
        4 => 'Vahva',
        5 => 'Erittäin vahva'
    ];
    
    $strength = $strengthLevels[$score] ?? 'Tuntematon';
    
    echo json_encode([
        'strength' => $score,
        'max_strength' => 5,
        'strength_text' => $strength,
        'feedback' => $feedback,
        'valid' => $score >= 3 // Require at least 3/5 for validity
    ]);
}