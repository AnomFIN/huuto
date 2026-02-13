<?php
/**
 * Huuto - Finnish Auction Platform Setup
 * This installer will create database tables and initial configuration
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if already installed
if (file_exists(__DIR__ . '/config/config.php') && !isset($_GET['force'])) {
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
    $schema = file_get_contents(__DIR__ . '/schema.sql');
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
    
    $stmt = $pdo->prepare("INSERT INTO categories (slug, name, icon, sort_order) VALUES (?, ?, ?, ?)");
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
    
    $stmt = $pdo->prepare("INSERT INTO pages (slug, title, content_html) VALUES (?, ?, ?)");
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
                header('Location: setup.php?step=2');
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
                    
                    // Insert categories
                    insertCategories($pdo);
                    
                    // Create admin user
                    $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, name, role, status) VALUES (?, ?, ?, 'admin', 'active')");
                    $stmt->execute([
                        $_POST['admin_email'],
                        password_hash($_POST['admin_password'], PASSWORD_DEFAULT),
                        $_POST['admin_name'] ?: 'Admin'
                    ]);
                    
                    $adminId = $pdo->lastInsertId();
                    
                    // Create demo user
                    $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, name, role, status) VALUES (?, ?, ?, 'user', 'active')");
                    $stmt->execute([
                        'demo@huuto.local',
                        password_hash('demo123', PASSWORD_DEFAULT),
                        'Demo User'
                    ]);
                    
                    // Create seed listings
                    createSeedListings($pdo);
                    
                    // Create static pages
                    createStaticPages($pdo);
                    
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
                    
                    file_put_contents(__DIR__ . '/config/config.php', $configContent);
                    
                    header('Location: setup.php?step=3');
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
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }
        h1 { color: #333; margin-bottom: 10px; font-size: 28px; }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
            padding: 0;
            list-style: none;
        }
        .step-indicator li {
            flex: 1;
            text-align: center;
            padding: 10px;
            background: #eee;
            margin: 0 5px;
            border-radius: 5px;
            font-size: 14px;
            color: #999;
        }
        .step-indicator li.active {
            background: #667eea;
            color: white;
            font-weight: bold;
        }
        .step-indicator li.completed {
            background: #51cf66;
            color: white;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }
        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            transition: border-color 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        button {
            background: #667eea;
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s;
        }
        button:hover {
            background: #5568d3;
        }
        .error {
            background: #ff6b6b;
            color: white;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .success {
            background: #51cf66;
            color: white;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .info {
            background: #e7f5ff;
            border-left: 4px solid #1c7ed6;
            padding: 15px;
            margin-bottom: 20px;
            color: #1864ab;
        }
        .credentials {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .credentials h3 {
            margin-bottom: 15px;
            color: #333;
        }
        .credentials p {
            margin: 8px 0;
            font-family: monospace;
            background: white;
            padding: 8px;
            border-radius: 4px;
        }
        a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        a:hover {
            text-decoration: underline;
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
                <p><strong>Admin:</strong> <?= htmlspecialchars($_SESSION['db_config']['user'] ?? 'admin@huuto.local') ?></p>
                <p><strong>Demo käyttäjä:</strong> demo@huuto.local / demo123</p>
            </div>
            
            <div class="info">
                <strong>Tiedot:</strong><br>
                • Luotu <?= count($pdo->query("SELECT id FROM categories")->fetchAll()) ?> kategoriaa<br>
                • Luotu <?= count($pdo->query("SELECT id FROM listings")->fetchAll()) ?> ilmoitusta<br>
                • Luotu <?= count($pdo->query("SELECT id FROM pages")->fetchAll()) ?> staattista sivua<br>
            </div>
            
            <a href="/public/index.php" style="display: inline-block; margin-top: 20px; text-align: center; width: 100%; padding: 14px; background: #667eea; color: white; border-radius: 6px; text-decoration: none;">
                Siirry sivustolle →
            </a>
            
            <p style="margin-top: 20px; text-align: center; color: #999; font-size: 14px;">
                Voit poistaa setup.php-tiedoston nyt turvallisesti.
            </p>
        <?php endif; ?>
    </div>
</body>
</html>
