<?php
/**
 * Huuto - Finnish Auction Platform Setup
 * This installer will create database tables and initial configuration
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if already installed using lock file
$lockFile = __DIR__ . '/config/installed.lock';
if (file_exists($lockFile) && !isset($_GET['force'])) {
    die('Application already installed. Delete config/installed.lock to reinstall or add ?force=1');
}

$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

// CSRF token for forms
if (!isset($_SESSION['setup_token'])) {
    $_SESSION['setup_token'] = bin2hex(random_bytes(32));
}

function testDbConnection($host, $dbname, $user, $pass) {
    // Validate database name - only allow alphanumeric and underscores
    if (!preg_match('/^[A-Za-z0-9_]+$/', $dbname)) {
        return ['success' => false, 'error' => 'Invalid database name. Only letters, numbers, and underscores are allowed.'];
    }
    
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
    // Use database/schema.sql which matches the public_html app structure
    $schema = file_get_contents(__DIR__ . '/database/schema.sql');
    if ($schema === false) {
        throw new Exception("Could not read schema file: " . __DIR__ . '/database/schema.sql');
    }
    
    // Remove the CREATE DATABASE and USE statements from the schema file
    $schema = preg_replace('/^CREATE DATABASE.*?;/mi', '', $schema);
    $schema = preg_replace('/^USE .*?;/mi', '', $schema);
    
    // Split by semicolon - simple but works for well-formatted SQL
    $statements = array_filter(array_map('trim', preg_split('/;(?=(?:[^\'"`]*[\'"`][^\'"`]*[\'"`])*[^\'"`]*$)/m', $schema)));
    
    $createdTables = [];
    $executedStatements = 0;
    $errors = [];
    
    error_log("Starting table creation with " . count($statements) . " total SQL statements");
    
    foreach ($statements as $i => $statement) {
        $statement = trim($statement);
        // Skip empty statements and comments
        if (empty($statement) || preg_match('/^\s*--/', $statement) || preg_match('/^\s*$/', $statement)) {
            continue;
        }
        
        // Log the statement being executed (first 100 chars)
        error_log("Executing statement " . ($i + 1) . ": " . substr($statement, 0, 100) . "...");
        
        try {
            $result = $pdo->exec($statement);
            if ($result === false) {
                $errorInfo = $pdo->errorInfo();
                $errors[] = "Statement " . ($i + 1) . " failed: " . $errorInfo[2];
                error_log("Statement " . ($i + 1) . " exec returned false: " . $errorInfo[2]);
                continue;
            }
            
            $executedStatements++;
            
            // Track which tables were created
            if (preg_match('/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?(\w+)`?/i', $statement, $matches)) {
                $createdTables[] = $matches[1];
                error_log("Successfully created table: " . $matches[1]);
            }
        } catch (PDOException $e) {
            $errorMsg = "Statement " . ($i + 1) . " failed: " . $e->getMessage();
            $errors[] = $errorMsg;
            error_log($errorMsg . "\nStatement: " . substr($statement, 0, 200));
            
            // For CREATE TABLE IF NOT EXISTS, ignore "table already exists" errors
            if (strpos($statement, 'IF NOT EXISTS') !== false && 
                (strpos($e->getMessage(), 'already exists') !== false || strpos($e->getMessage(), 'Table') !== false)) {
                // Extract table name and add to created list anyway
                if (preg_match('/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?(\w+)`?/i', $statement, $matches)) {
                    $createdTables[] = $matches[1];
                    error_log("Table " . $matches[1] . " already exists, continuing");
                }
                continue;
            }
            
            // For critical errors, stop execution
            throw new Exception("Critical SQL error: " . $e->getMessage() . "\nStatement: " . substr($statement, 0, 100) . "...");
        }
    }
    
    // Debug info
    error_log("Executed $executedStatements SQL statements successfully");
    error_log("Created " . count($createdTables) . " tables: " . implode(', ', $createdTables));
    if (!empty($errors)) {
        error_log("Errors encountered: " . implode("; ", $errors));
    }
    
    return $createdTables;
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
    $adminId = $pdo->query("SELECT id FROM users LIMIT 1")->fetchColumn();
    
    if (!$adminId) {
        return; // Skip seeding if no users exist
    }
    
    $regions = ['Uusimaa', 'Pirkanmaa', 'Varsinais-Suomi', 'Pohjois-Pohjanmaa', 'Kanta-Häme', 'Satakunta'];
    $conditions = ['new', 'excellent', 'good', 'fair', 'poor'];
    
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
            $startPrice = rand(10, 500);
            $currentPrice = $startPrice + (rand(0, 5) * 10);
            $buyNowPrice = rand(0, 1) ? ($currentPrice * 2) : null;
            $endTime = date('Y-m-d H:i:s', strtotime('+' . rand(1, 30) . ' days'));
            
            $description = "Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\n";
            $description .= "Kunto: " . $conditions[array_rand($conditions)] . "\n";
            $description .= "Sijainti: " . $regions[array_rand($regions)] . "\n";
            $description .= "Lisätietoja: Ota yhteyttä myyjään.";
            
            // Using auctions table schema from database/schema.sql
            $stmt = $pdo->prepare("
                INSERT INTO auctions (user_id, category_id, title, description, starting_price, 
                    current_price, buy_now_price, end_time, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')
            ");
            
            $stmt->execute([
                $adminId, $catId, $title, $description,
                $startPrice, $currentPrice, $buyNowPrice, $endTime
            ]);
            
            // Don't add placeholder images since they don't exist in the repository
            // Images can be added manually by users after installation
        }
    }
}

// Note: Static pages table doesn't exist in database/schema.sql
// Pages can be added manually or through a separate content management system

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
            // Guard: Check if session has DB config
            if (!isset($_SESSION['db_config'])) {
                header('Location: asennus.php?step=1');
                exit;
            }
            
            if (empty($_POST['admin_email']) || empty($_POST['admin_password'])) {
                $error = 'Admin email and password required';
            } else {
                try {
                    $db = $_SESSION['db_config'];
                    $result = testDbConnection($db['host'], $db['name'], $db['user'], $db['pass']);
                    
                    if (!$result['success']) {
                        throw new Exception('Database connection failed: ' . $result['error']);
                    }
                    
                    $pdo = $result['pdo'];
                    
                    // Add debugging info about database connection
                    $currentDb = $pdo->query("SELECT DATABASE()")->fetchColumn();
                    error_log("Connected to database: " . $currentDb);
                    
                    // Add some debugging output for browser
                    echo "<script>console.log('Creating database tables...');</script>";
                    
                    // Create tables and get list of created tables
                    try {
                        $createdTables = createTables($pdo);
                        echo "<script>console.log('Created tables: " . implode(', ', $createdTables) . "');</script>";
                    } catch (Exception $e) {
                        echo "<script>console.error('Table creation error: " . addslashes($e->getMessage()) . "');</script>";
                        throw $e;
                    }
                    
                    // Verify critical tables exist before proceeding
                    $requiredTables = ['users', 'categories', 'auctions'];
                    $missingTables = [];
                    
                    foreach ($requiredTables as $table) {
                        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
                        if ($stmt->rowCount() == 0) {
                            $missingTables[] = $table;
                        } else {
                            error_log("Table '$table' verified successfully");
                        }
                    }
                    
                    if (!empty($missingTables)) {
                        // Show all tables that actually exist for debugging
                        $existingTables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                        error_log("Existing tables in database: " . implode(', ', $existingTables));
                        error_log("Created tables reported by createTables(): " . implode(', ', $createdTables));
                        
                        // Try to create the missing tables individually as a fallback
                        echo "<script>console.log('Attempting fallback table creation for: " . implode(', ', $missingTables) . "');</script>";
                        
                        $manualTableCreation = [
                            'users' => "CREATE TABLE IF NOT EXISTS users (
                                id INT AUTO_INCREMENT PRIMARY KEY,
                                username VARCHAR(50) UNIQUE NOT NULL,
                                email VARCHAR(100) UNIQUE NOT NULL,
                                password_hash VARCHAR(255) NOT NULL,
                                full_name VARCHAR(100),
                                phone VARCHAR(20),
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                INDEX idx_email (email),
                                INDEX idx_username (username)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                            
                            'categories' => "CREATE TABLE IF NOT EXISTS categories (
                                id INT AUTO_INCREMENT PRIMARY KEY,
                                name VARCHAR(100) NOT NULL,
                                slug VARCHAR(100) UNIQUE NOT NULL,
                                description TEXT,
                                parent_id INT DEFAULT NULL,
                                icon VARCHAR(50),
                                sort_order INT DEFAULT 0,
                                INDEX idx_slug (slug),
                                INDEX idx_parent (parent_id)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                            
                            'auctions' => "CREATE TABLE IF NOT EXISTS auctions (
                                id INT AUTO_INCREMENT PRIMARY KEY,
                                user_id INT NOT NULL,
                                category_id INT NOT NULL,
                                title VARCHAR(200) NOT NULL,
                                description TEXT,
                                starting_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                                current_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                                reserve_price DECIMAL(10,2) DEFAULT NULL,
                                buy_now_price DECIMAL(10,2) DEFAULT NULL,
                                bid_increment DECIMAL(10,2) NOT NULL DEFAULT 1.00,
                                start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                end_time TIMESTAMP NOT NULL,
                                status ENUM('draft', 'active', 'ended', 'cancelled') DEFAULT 'draft',
                                views INT DEFAULT 0,
                                location VARCHAR(200),
                                condition_description VARCHAR(50),
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
                                INDEX idx_status (status),
                                INDEX idx_end_time (end_time),
                                INDEX idx_category (category_id),
                                INDEX idx_user (user_id)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
                        ];
                        
                        $fallbackCreated = [];
                        foreach ($missingTables as $table) {
                            if (isset($manualTableCreation[$table])) {
                                try {
                                    $pdo->exec($manualTableCreation[$table]);
                                    $fallbackCreated[] = $table;
                                    echo "<script>console.log('Fallback created table: $table');</script>";
                                } catch (PDOException $e) {
                                    echo "<script>console.error('Fallback failed for table $table: " . addslashes($e->getMessage()) . "');</script>";
                                }
                            }
                        }
                        
                        // Re-check which tables now exist
                        $stillMissing = [];
                        foreach ($requiredTables as $table) {
                            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
                            if ($stmt->rowCount() == 0) {
                                $stillMissing[] = $table;
                            }
                        }
                        
                        if (!empty($stillMissing)) {
                            $existingTablesAfterFallback = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                            throw new Exception("Critical tables were not created: " . implode(', ', $stillMissing) . 
                                              ". Please check database permissions and schema file. Existing tables: " . 
                                              implode(', ', $existingTablesAfterFallback));
                        } else {
                            echo "<script>console.log('All required tables now exist after fallback');</script>";
                        }
                    }
                    
                    // Insert categories (use INSERT IGNORE for idempotency)
                    insertCategories($pdo);
                    
                    // Create admin user (using database/schema.sql structure)
                    $adminEmail = $_POST['admin_email'];
                    $adminName = $_POST['admin_name'] ?: 'Admin';
                    $username = strtolower(preg_replace('/[^a-z0-9]+/i', '', $adminName)) . rand(1000, 9999);
                    
                    $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, email, password_hash, full_name) VALUES (?, ?, ?, ?)");
                    $stmt->execute([
                        $username,
                        $adminEmail,
                        password_hash($_POST['admin_password'], PASSWORD_DEFAULT),
                        $adminName
                    ]);
                    
                    $adminId = $pdo->lastInsertId();
                    if (!$adminId) {
                        // User already exists, get their ID
                        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                        $stmt->execute([$adminEmail]);
                        $adminId = $stmt->fetchColumn();
                    }
                    
                    // Create demo user (use INSERT IGNORE)
                    $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, email, password_hash, full_name) VALUES (?, ?, ?, ?)");
                    $stmt->execute([
                        'demouser',
                        'demo@huuto.local',
                        password_hash('demo123', PASSWORD_DEFAULT),
                        'Demo User'
                    ]);
                    
                    // Create seed listings
                    createSeedListings($pdo);
                    
                    // Store admin email for display on completion page
                    $_SESSION['admin_email'] = $adminEmail;
                    
                    // Write to config/database.php instead of overwriting config/config.php
                    $configContent = "<?php\n";
                    $configContent .= "// Database configuration\n";
                    $configContent .= "return [\n";
                    $configContent .= "    'host' => " . var_export($db['host'], true) . ",\n";
                    $configContent .= "    'dbname' => " . var_export($db['name'], true) . ",\n";
                    $configContent .= "    'username' => " . var_export($db['user'], true) . ",\n";
                    $configContent .= "    'password' => " . var_export($db['pass'], true) . ",\n";
                    $configContent .= "    'charset' => 'utf8mb4'\n";
                    $configContent .= "];\n";

                    $configPath = __DIR__ . '/config/database.php';
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
                    
                    // Create installation lock file
                    $lockFile = __DIR__ . '/config/installed.lock';
                    $lockContent = "Installation completed: " . date('Y-m-d H:i:s') . "\n";
                    $lockContent .= "Admin: " . $adminEmail . "\n";
                    file_put_contents($lockFile, $lockContent);
                    
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
            // Reconnect to database to show statistics (if session has db_config)
            if (isset($_SESSION['db_config'])) {
                $db = $_SESSION['db_config'];
                $result = testDbConnection($db['host'], $db['name'], $db['user'], $db['pass']);
                if ($result['success']) {
                    $pdo = $result['pdo'];
                    $categoryCount = $pdo->query("SELECT COUNT(*) as count FROM categories")->fetch()['count'];
                    $auctionCount = $pdo->query("SELECT COUNT(*) as count FROM auctions")->fetch()['count'];
            ?>
            <div class="info">
                <strong>Tiedot:</strong><br>
                • Luotu <?= $categoryCount ?> kategoriaa<br>
                • Luotu <?= $auctionCount ?> huutokauppaa<br>
            </div>
            <?php 
                }
            } 
            ?>
            
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
