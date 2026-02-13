<?php
/**
 * Huuto - Finnish Auction Platform Setup
 * This installer will create database tables and initial configuration
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if already installed
if (file_exists(__DIR__ . '/../config/config.php') && !isset($_GET['force'])) {
    die('Application already installed. Delete config/config.php to reinstall or add ?force=1');
}

$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

// CSRF token for forms
if (!isset($_SESSION['setup_token'])) {
    $_SESSION['setup_token'] = bin2hex(random_bytes(32));
}

function testDbConnection($host, $dbname, $user, $pass) {
    try {
        $dsn = "mysql:host=$host;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        
        // Try to create database if it doesn't exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$dbname`");
        
        return ['success' => true, 'pdo' => $pdo];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function createTables($pdo) {
    $schema = file_get_contents(__DIR__ . '/../schema.sql');
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
}

function insertCategories($pdo) {
    $categories = [
        ['maakunnittain', 'Maakunnittain', '📍'],
        ['ajoneuvot', 'Ajoneuvot ja tarvikkeet', '🚗'],
        ['tyokoneet', 'Työkoneet ja raskas kalusto', '🚜'],
        ['asunnot', 'Asunnot, mökit, toimitilat ja tontit', '🏠'],
        ['harrastus', 'Harrastusvälineet ja vapaa-aika', '⚽'],
        ['piha', 'Piha ja puutarha', '🌳'],
        ['tyokalut', 'Työkalut ja työkalusarjat', '🔧'],
        ['rakennus', 'Rakennustarvikkeet', '🏗️'],
        ['sisustus', 'Sisustaminen ja koti', '🛋️'],
        ['elektroniikka', 'Elektroniikka', '💻'],
        ['keraily', 'Keräily', '🎨'],
        ['tukkuerat', 'Tukkuerät', '📦'],
        ['muut', 'Muut', '📌'],
        ['perinteiset', 'Perinteiset huutokaupat', '⚖️'],
        ['ulosotto', 'Ulosotto', '⚖️'],
        ['konkurssi', 'Konkurssipesät', '💼'],
        ['puolustusvoimat', 'Puolustusvoimat', '🎖️'],
        ['metsahallitus', 'Metsähallitus', '🌲'],
        ['rahoitus', 'Rahoitusyhtiöt', '💰'],
        ['julkinen', 'Julkinen sektori', '🏛️'],
        ['paattyvat', 'Päättyvät', '⏰']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO categories (slug, name, icon, sort_order) VALUES (?, ?, ?, ?)");
    foreach ($categories as $i => $cat) {
        $stmt->execute([$cat[0], $cat[1], $cat[2], $i + 1]);
    }
}

function createSeedListings($pdo) {
    // Get all categories
    $categories = $pdo->query("SELECT id FROM categories")->fetchAll(PDO::FETCH_COLUMN);
    
    // Get admin user
    $adminId = $pdo->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1")->fetchColumn();
    
    $regions = ['Uusimaa', 'Pirkanmaa', 'Varsinais-Suomi', 'Pohjois-Pohjanmaa', 'Kanta-Häme', 'Satakunta'];
    $conditions = ['Uusi', 'Erinomainen', 'Hyvä', 'Tyydyttävä', 'Korjattava'];
    
    $sampleListings = [
        'Laadukas sohva - mukava ja hyväkuntoinen',
        'Vanha rahapussi - keräilijän kohde',
        'Puutarhatyökalut 15 kpl - käytetty',
        'Talonrakennustarvikkeita - iso erä',
        'Polkupyörä 28" - toimiva kunto'
    ];
    
    foreach ($categories as $catId) {
        for ($i = 0; $i < 3; $i++) {
            $title = $sampleListings[array_rand($sampleListings)] . " #" . rand(1000, 9999);
            $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $title));
            $startPrice = rand(10, 500);
            $currentPrice = $startPrice + (rand(0, 5) * 10);
            $minIncrement = 5.00;
            $buyNow = rand(0, 1) ? ($currentPrice * 2) : null;
            $endsAt = date('Y-m-d H:i:s', strtotime('+' . rand(1, 30) . ' days'));
            
            $description = "Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\n";
            $description .= "Kunto: " . $conditions[array_rand($conditions)] . "\n";
            $description .= "Sijainti: " . $regions[array_rand($regions)] . "\n";
            $description .= "Lisätietoja: Ota yhteyttä myyjään.";
            
            $stmt = $pdo->prepare("
                INSERT INTO listings (user_id, category_id, title, slug, description, `condition`, region, 
                    start_price, buy_now_price, min_increment, current_price, ends_at, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')
            ");
            
            $stmt->execute([
                $adminId, $catId, $title, $slug, $description,
                $conditions[array_rand($conditions)],
                $regions[array_rand($regions)],
                $startPrice, $buyNow, $minIncrement, $currentPrice, $endsAt
            ]);
            
            // Add placeholder images
            $listingId = $pdo->lastInsertId();
            $numImages = rand(2, 4);
            for ($j = 0; $j < $numImages; $j++) {
                $pdo->prepare("INSERT INTO listing_images (listing_id, path, sort_order) VALUES (?, ?, ?)")
                    ->execute([$listingId, "/assets/img/placeholder-{$j}.jpg", $j]);
            }
        }
    }
}

function createStaticPages($pdo) {
    $pages = [
        ['tietoa-palvelusta', 'Tietoa palvelusta', '<h2>Tietoa Huuto-palvelusta</h2><p>Huuto on moderni suomalainen huutokauppa-alusta.</p>'],
        ['tietoa-huutajalle', 'Tietoa huutajalle', '<h2>Ohjeita huutajalle</h2><p>Täältä löydät ohjeita huutamiseen.</p>'],
        ['kayttoehdot', 'Käyttöehdot', '<h2>Käyttöehdot</h2><p>Palvelun käyttöehdot.</p>'],
        ['aloita-myyminen', 'Aloita myyminen', '<h2>Aloita myyminen</h2><p>Ohjeita myyjälle.</p>'],
        ['myyntiehdot', 'Myyntiehdot', '<h2>Myyntiehdot</h2><p>Myyntiin liittyvät ehdot.</p>'],
        ['hinnasto', 'Hinnasto', '<h2>Hinnasto</h2><p>Palvelumaksut.</p>'],
        ['maksutavat', 'Maksutavat', '<h2>Maksutavat</h2><p>Tuetut maksutavat.</p>'],
        ['asiakaspalvelu', 'Asiakaspalvelu', '<h2>Asiakaspalvelu</h2><p>Ota yhteyttä.</p>'],
        ['ohjeet', 'Ohjeet ja vinkit', '<h2>Ohjeet</h2><p>Vinkkejä käyttöön.</p>'],
        ['yritys', 'Tietoa meistä', '<h2>Yritys</h2><p>Tietoa yrityksestämme.</p>'],
        ['tyopaikat', 'Meille töihin', '<h2>Työpaikat</h2><p>Avoimet työpaikat.</p>'],
        ['media', 'Medialle', '<h2>Medialle</h2><p>Lehdistötiedotteet ja mediamateriaalit.</p>'],
        ['tietosuoja', 'Tietosuojaseloste', '<h2>Tietosuoja</h2><p>Henkilötietojen käsittely.</p>'],
        ['saavutettavuus', 'Saavutettavuusseloste', '<h2>Saavutettavuus</h2><p>Saavutettavuusseloste.</p>'],
        ['lapinakyvyys', 'Läpinäkyvyysraportti', '<h2>Läpinäkyvyys</h2><p>Läpinäkyvyysraportti.</p>']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO pages (slug, title, content_html) VALUES (?, ?, ?)");
    foreach ($pages as $page) {
        $stmt->execute($page);
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['setup_token']) {
        $error = 'Invalid security token';
    } else {
        if ($step == 1) {
            // Test database connection
            $result = testDbConnection(
                $_POST['db_host'],
                $_POST['db_name'],
                $_POST['db_user'],
                $_POST['db_pass']
            );
            
            if ($result['success']) {
                $_SESSION['db_config'] = [
                    'host' => $_POST['db_host'],
                    'name' => $_POST['db_name'],
                    'user' => $_POST['db_user'],
                    'pass' => $_POST['db_pass']
                ];
                header('Location: asennus.php?step=2');
                exit;
            } else {
                $error = 'Database connection failed: ' . $result['error'];
            }
        } elseif ($step == 2) {
            // Create admin and install
            if (empty($_POST['admin_email']) || empty($_POST['admin_password'])) {
                $error = 'Admin email and password required';
            } else {
                try {
                    $db = $_SESSION['db_config'];
                    $result = testDbConnection($db['host'], $db['name'], $db['user'], $db['pass']);
                    $pdo = $result['pdo'];
                    
                    // Create tables
                    createTables($pdo);
                    
                    // Insert categories (use INSERT IGNORE for idempotency)
                    insertCategories($pdo);
                    
                    // Create admin user
                    $adminEmail = $_POST['admin_email'];
                    $stmt = $pdo->prepare("INSERT IGNORE INTO users (email, password_hash, name, role, status) VALUES (?, ?, ?, 'admin', 'active')");
                    $stmt->execute([
                        $adminEmail,
                        password_hash($_POST['admin_password'], PASSWORD_DEFAULT),
                        $_POST['admin_name'] ?: 'Admin'
                    ]);
                    
                    $adminId = $pdo->lastInsertId();
                    if (!$adminId) {
                        // User already exists, get their ID
                        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                        $stmt->execute([$adminEmail]);
                        $adminId = $stmt->fetchColumn();
                    }
                    
                    // Create demo user (use INSERT IGNORE)
                    $stmt = $pdo->prepare("INSERT IGNORE INTO users (email, password_hash, name, role, status) VALUES (?, ?, ?, 'user', 'active')");
                    $stmt->execute([
                        'demo@huuto.local',
                        password_hash('demo123', PASSWORD_DEFAULT),
                        'Demo User'
                    ]);
                    
                    // Create seed listings
                    createSeedListings($pdo);
                    
                    // Create static pages (use INSERT IGNORE)
                    createStaticPages($pdo);
                    
                    // Store admin email for display on completion page
                    $_SESSION['admin_email'] = $adminEmail;
                    
                    // Create config file
                    $configContent = "<?php\n";
                    $configContent .= "return [\n";
                    $configContent .= "    'db' => [\n";
                    $configContent .= "        'host' => " . var_export($db['host'], true) . ",\n";
                    $configContent .= "        'name' => " . var_export($db['name'], true) . ",\n";
                    $configContent .= "        'user' => " . var_export($db['user'], true) . ",\n";
                    $configContent .= "        'pass' => " . var_export($db['pass'], true) . ",\n";
                    $configContent .= "    ],\n";
                    $configContent .= "    'site' => [\n";
                    $configContent .= "        'name' => 'Huuto',\n";
                    $configContent .= "        'url' => 'http://localhost',\n";
                    $configContent .= "    ]\n";
                    $configContent .= "];\n";

                    $configPath = __DIR__ . '/../config/config.php';
                    $configDir  = dirname($configPath);

                    if (!is_dir($configDir)) {
                        throw new RuntimeException("Configuration directory does not exist: " . $configDir);
                    }

                    if (!is_writable($configDir)) {
                        throw new RuntimeException("Configuration directory is not writable: " . $configDir);
                    }

                    $bytesWritten = file_put_contents($configPath, $configContent);
                    if ($bytesWritten === false) {
                        throw new RuntimeException("Failed to write configuration file: " . $configPath . ". Please check file permissions.");
                    }
                    header('Location: asennus.php?step=3');
                    exit;
                } catch (Exception $e) {
                    $error = 'Installation failed: ' . $e->getMessage();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Huuto - Asennusohjelma</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #667eea 100%);
            background-size: 200% 200%;
            animation: gradientShift 10s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 25px 70px rgba(0,0,0,0.3);
            max-width: 650px;
            width: 100%;
            padding: 45px;
            animation: fadeInUp 0.6s ease;
        }
        
        h1 { 
            color: #333; 
            margin-bottom: 10px; 
            font-size: 32px;
            animation: slideIn 0.5s ease;
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
            padding: 0;
            list-style: none;
            position: relative;
        }
        
        .step-indicator::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 3px;
            background: #eee;
            z-index: 0;
            transform: translateY(-50%);
        }
        
        .step-indicator li {
            flex: 1;
            text-align: center;
            padding: 12px;
            background: #eee;
            margin: 0 5px;
            border-radius: 8px;
            font-size: 14px;
            color: #999;
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
        }
        
        .step-indicator li.active {
            background: #667eea;
            color: white;
            font-weight: bold;
            animation: pulse 1s ease infinite;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .step-indicator li.completed {
            background: #51cf66;
            color: white;
            box-shadow: 0 4px 15px rgba(81, 207, 102, 0.4);
        }
        
        .step-indicator li.completed::after {
            content: '✓';
            margin-left: 5px;
        }
        
        .form-group {
            margin-bottom: 20px;
            animation: fadeInUp 0.4s ease;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
            font-size: 14px;
            transition: color 0.3s;
        }
        
        .form-group:focus-within label {
            color: #667eea;
        }
        
        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 14px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #fafafa;
        }
        
        input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }
        
        input.valid {
            border-color: #51cf66;
            background: #f0fdf4;
        }
        
        input.invalid {
            border-color: #ff6b6b;
            background: #fff5f5;
        }
        
        .validation-message {
            font-size: 12px;
            margin-top: 5px;
            animation: slideIn 0.3s ease;
        }
        
        .validation-message.error {
            color: #ff6b6b;
        }
        
        .validation-message.success {
            color: #51cf66;
        }
        
        button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 16px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        
        button:hover::before {
            left: 100%;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .error {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            animation: slideIn 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        }
        
        .success {
            background: linear-gradient(135deg, #51cf66 0%, #40c057 100%);
            color: white;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            animation: fadeInUp 0.5s ease;
            box-shadow: 0 4px 15px rgba(81, 207, 102, 0.3);
        }
        
        .info {
            background: linear-gradient(135deg, #e7f5ff 0%, #d0ebff 100%);
            border-left: 4px solid #1c7ed6;
            padding: 16px;
            margin-bottom: 20px;
            color: #1864ab;
            border-radius: 8px;
            animation: fadeInUp 0.4s ease;
        }
        
        .credentials {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 25px;
            border-radius: 10px;
            margin: 20px 0;
            border: 2px solid #dee2e6;
            animation: fadeInUp 0.6s ease;
        }
        
        .credentials h3 {
            margin-bottom: 15px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .credentials p {
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            background: white;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }
        
        .credentials p:hover {
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        a:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        .progress-bar {
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            margin: 20px 0;
            overflow: hidden;
        }
        
        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transition: width 0.5s ease;
            border-radius: 2px;
        }
        
        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        button.loading .loading-spinner {
            display: inline-block;
        }
        
        .tooltip {
            position: relative;
            display: inline-block;
            cursor: help;
        }
        
        .tooltip .tooltiptext {
            visibility: hidden;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 8px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 12px;
            white-space: nowrap;
        }
        
        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎯 Huuto - Asennusohjelma</h1>
        <p style="color: #666; margin-bottom: 20px;">Suomalainen huutokauppa-alusta</p>
        
        <ul class="step-indicator">
            <li class="<?= $step >= 1 ? 'active' : '' ?> <?= $step > 1 ? 'completed' : '' ?>">1. Tietokanta</li>
            <li class="<?= $step >= 2 ? 'active' : '' ?> <?= $step > 2 ? 'completed' : '' ?>">2. Admin</li>
            <li class="<?= $step >= 3 ? 'active' : '' ?>">3. Valmis</li>
        </ul>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($step == 1): ?>
            <form method="POST">
                <input type="hidden" name="token" value="<?= $_SESSION['setup_token'] ?>">
                
                <div class="info">
                    <strong>Vaihe 1:</strong> Syötä tietokannan yhteystiedot. Tietokanta luodaan automaattisesti, jos sitä ei ole olemassa.
                </div>
                
                <div class="form-group">
                    <label>Tietokantapalvelin</label>
                    <input type="text" name="db_host" value="localhost" required>
                </div>
                
                <div class="form-group">
                    <label>Tietokannan nimi</label>
                    <input type="text" name="db_name" value="huuto" required>
                </div>
                
                <div class="form-group">
                    <label>Käyttäjänimi</label>
                    <input type="text" name="db_user" value="root" required>
                </div>
                
                <div class="form-group">
                    <label>Salasana</label>
                    <input type="password" name="db_pass">
                </div>
                
                <button type="submit">Jatka →</button>
            </form>
        
        <?php elseif ($step == 2): ?>
            <form method="POST">
                <input type="hidden" name="token" value="<?= $_SESSION['setup_token'] ?>">
                
                <div class="info">
                    <strong>Vaihe 2:</strong> Luo pääkäyttäjätili. Tällä tilillä voit hallita koko sivustoa.
                </div>
                
                <div class="form-group">
                    <label>Admin nimi</label>
                    <input type="text" name="admin_name" value="Admin" required>
                </div>
                
                <div class="form-group">
                    <label>Admin sähköposti</label>
                    <input type="email" name="admin_email" value="admin@huuto.local" required>
                </div>
                
                <div class="form-group">
                    <label>Admin salasana</label>
                    <input type="password" name="admin_password" required>
                </div>
                
                <button type="submit">Asenna →</button>
            </form>
        
        <?php elseif ($step == 3): ?>
            <div class="success">
                <strong>✓ Asennus onnistui!</strong><br>
                Huuto-alusta on nyt käyttövalmis.
            </div>
            
            <div class="credentials">
                <h3>📋 Käyttäjätunnukset:</h3>
                <p><strong>Admin:</strong> <?= htmlspecialchars($_SESSION['admin_email'] ?? 'admin@huuto.local') ?></p>
                <p><strong>Demo käyttäjä:</strong> demo@huuto.local / demo123</p>
            </div>
            
            <?php
            // Reconnect to database to show statistics
            $db = $_SESSION['db_config'];
            $result = testDbConnection($db['host'], $db['name'], $db['user'], $db['pass']);
            if ($result['success']) {
                $pdo = $result['pdo'];
                $categoryCount = $pdo->query("SELECT COUNT(*) as count FROM categories")->fetch()['count'];
                $listingCount = $pdo->query("SELECT COUNT(*) as count FROM listings")->fetch()['count'];
                $pageCount = $pdo->query("SELECT COUNT(*) as count FROM pages")->fetch()['count'];
            ?>
            <div class="info">
                <strong>Tiedot:</strong><br>
                • Luotu <?= $categoryCount ?> kategoriaa<br>
                • Luotu <?= $listingCount ?> ilmoitusta<br>
                • Luotu <?= $pageCount ?> staattista sivua<br>
            </div>
            <?php } ?>
            
            <a href="/" style="display: inline-block; margin-top: 20px; text-align: center; width: 100%; padding: 14px; background: #667eea; color: white; border-radius: 6px; text-decoration: none;">
                Siirry sivustolle →
            </a>
            
            <p style="margin-top: 20px; text-align: center; color: #999; font-size: 14px;">
                Voit poistaa asennus.php-tiedoston nyt turvallisesti.
            </p>
        <?php endif; ?>
    </div>
    
    <script>
        // Real-time form validation
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            
            forms.forEach(form => {
                const inputs = form.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
                const submitButton = form.querySelector('button[type="submit"]');
                
                inputs.forEach(input => {
                    // Add real-time validation
                    input.addEventListener('input', function() {
                        validateField(this);
                        checkFormValidity(form, submitButton);
                    });
                    
                    input.addEventListener('blur', function() {
                        validateField(this);
                    });
                });
                
                // Prevent double submission
                form.addEventListener('submit', function(e) {
                    if (submitButton.classList.contains('loading')) {
                        e.preventDefault();
                        return false;
                    }
                    
                    submitButton.classList.add('loading');
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<span class="loading-spinner"></span> Käsitellään...';
                });
            });
            
            function validateField(field) {
                const value = field.value.trim();
                const type = field.type;
                const name = field.name;
                let isValid = true;
                let message = '';
                
                // Remove existing validation message
                const existingMsg = field.parentElement.querySelector('.validation-message');
                if (existingMsg) {
                    existingMsg.remove();
                }
                
                // Check if required field is empty
                if (field.hasAttribute('required') && value === '') {
                    isValid = false;
                    message = 'Tämä kenttä on pakollinen';
                }
                // Email validation
                else if (type === 'email' && value !== '') {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        isValid = false;
                        message = 'Virheellinen sähköpostiosoite';
                    } else {
                        message = 'Sähköposti OK ✓';
                    }
                }
                // Password validation
                else if (type === 'password' && name === 'admin_password' && value !== '') {
                    if (value.length < 6) {
                        isValid = false;
                        message = 'Salasanan tulee olla vähintään 6 merkkiä';
                    } else if (value.length >= 8) {
                        message = 'Vahva salasana ✓';
                    } else {
                        message = 'Keskivahva salasana';
                    }
                }
                // Database name validation
                else if (name === 'db_name' && value !== '') {
                    const dbNameRegex = /^[a-zA-Z0-9_]+$/;
                    if (!dbNameRegex.test(value)) {
                        isValid = false;
                        message = 'Sallitut merkit: a-z, A-Z, 0-9, _';
                    } else {
                        message = 'Tietokannan nimi OK ✓';
                    }
                }
                // Generic text validation
                else if (type === 'text' && value !== '' && value.length >= 3) {
                    message = 'OK ✓';
                }
                
                // Update field styling
                field.classList.remove('valid', 'invalid');
                if (value !== '') {
                    if (isValid) {
                        field.classList.add('valid');
                    } else {
                        field.classList.add('invalid');
                    }
                }
                
                // Add validation message
                if (message && value !== '') {
                    const msgElement = document.createElement('div');
                    msgElement.className = 'validation-message ' + (isValid ? 'success' : 'error');
                    msgElement.textContent = message;
                    field.parentElement.appendChild(msgElement);
                }
                
                return isValid;
            }
            
            function checkFormValidity(form, button) {
                const requiredInputs = form.querySelectorAll('input[required]');
                let allValid = true;
                
                requiredInputs.forEach(input => {
                    if (input.value.trim() === '' || input.classList.contains('invalid')) {
                        allValid = false;
                    }
                });
                
                button.disabled = !allValid;
                button.style.opacity = allValid ? '1' : '0.6';
            }
            
            // Add progress bar animation
            const stepIndicator = document.querySelector('.step-indicator');
            if (stepIndicator) {
                const activeStep = stepIndicator.querySelector('.active');
                if (activeStep) {
                    const progressPercent = (Array.from(stepIndicator.children).indexOf(activeStep) + 1) / stepIndicator.children.length * 100;
                    
                    // Create progress bar if it doesn't exist
                    if (!document.querySelector('.progress-bar')) {
                        const progressBar = document.createElement('div');
                        progressBar.className = 'progress-bar';
                        progressBar.innerHTML = '<div class="progress-bar-fill"></div>';
                        stepIndicator.parentElement.insertBefore(progressBar, stepIndicator.nextSibling);
                        
                        setTimeout(() => {
                            progressBar.querySelector('.progress-bar-fill').style.width = progressPercent + '%';
                        }, 100);
                    }
                }
            }
            
            // Add smooth scroll to errors
            const errorElement = document.querySelector('.error');
            if (errorElement) {
                errorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            
            // Add keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Alt + N for next button
                if (e.altKey && e.key === 'n') {
                    const button = document.querySelector('button[type="submit"]');
                    if (button && !button.disabled) {
                        button.click();
                    }
                }
            });
            
            // Add tooltip for keyboard shortcuts
            const buttons = document.querySelectorAll('button[type="submit"]');
            buttons.forEach(button => {
                const originalText = button.textContent;
                button.innerHTML = `${originalText} <span class="tooltip" style="font-size: 11px; opacity: 0.7;">(Alt+N)</span>`;
            });
        });
    </script>
</body>
</html>
