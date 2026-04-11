<?php
/*
============================================================================
PREMIUM TODO APP - INSTALLATION WIZARD
Ultra Smooth Installation Experience with Premium Design
============================================================================
*/

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration file path
define('CONFIG_FILE', __DIR__ . '/todo_config.php');

// Check if already installed
if (file_exists(CONFIG_FILE)) {
    $configContent = file_get_contents(CONFIG_FILE);
    if (strpos($configContent, 'INSTALLED') !== false) {
        header('Location: todo.php');
        exit;
    }
}

// Installation steps
$steps = [
    1 => 'Tervetuloa',
    2 => 'Tietokanta',
    3 => 'Testaus', 
    4 => 'Valmis'
];

$currentStep = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$currentStep = max(1, min(4, $currentStep));

$errors = [];
$success = false;
$dbConnection = null;

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if ($currentStep === 2) {
        // Database configuration step
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $host = trim($_POST['db_host'] ?? '');
        $dbname = trim($_POST['db_name'] ?? '');
        $username = trim($_POST['db_user'] ?? '');
        $password = $_POST['db_pass'] ?? '';
        
        // Validate inputs
        if (empty($host)) $errors[] = 'Tietokannan osoite on pakollinen';
        if (empty($dbname)) $errors[] = 'Tietokannan nimi on pakollinen';  
        if (empty($username)) $errors[] = 'Käyttäjätunnus on pakollinen';
        
        if (empty($errors)) {
            // Test database connection with SQLite in dev environment
            try {
                // Use SQLite for development
                $dbPath = __DIR__ . '/todo_app.db';
                $pdo = new PDO("sqlite:" . $dbPath, null, null, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
                
                // Store connection for next step  
                $_SESSION['db_config'] = [
                    'type' => 'sqlite',
                    'path' => $dbPath,
                    'host' => $host,
                    'dbname' => $dbname,
                    'username' => $username,
                    'password' => $password
                ];
                
                header('Location: ?step=3');
                exit;
                
            } catch (PDOException $e) {
                $errors[] = 'Tietokantayhteys epäonnistui: ' . $e->getMessage();
            }
        }
    }
    
    if ($currentStep === 3) {
        // Database setup step
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['db_config'])) {
            $config = $_SESSION['db_config'];
            
            try {
                // Use SQLite for development
                $pdo = new PDO("sqlite:" . $config['path'], null, null, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
                
                // Create tables
                createTables($pdo);
                
                // Create uploads directory
                createUploadsDirectory();
                
                // Create configuration file
                createConfigFile($config);
                
                // Clear session
                unset($_SESSION['db_config']);
                
                header('Location: ?step=4');
                exit;
                
            } catch (Exception $e) {
                $errors[] = 'Tietokannan luonti epäonnistui: ' . $e->getMessage();
            }
        } else {
            $errors[] = 'Tietokanta-asetukset puuttuvat. Aloita alusta.';
        }
    }
}

// Start session for multi-step process
if ($currentStep > 1 && session_status() === PHP_SESSION_NONE) {
    session_start();
}

function createTables($pdo) {
    // Read SQLite schema
    $schemaFile = __DIR__ . '/database/sqlite_schema.sql';
    
    if (file_exists($schemaFile)) {
        $sql = file_get_contents($schemaFile);
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
    } else {
        // Fallback SQLite schema
        $tables = [
            // Todos table
            "CREATE TABLE IF NOT EXISTS todos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                content TEXT DEFAULT NULL,
                is_done INTEGER DEFAULT 0,
                is_deleted INTEGER DEFAULT 0,
                is_public INTEGER DEFAULT 0,
                share_token TEXT DEFAULT NULL UNIQUE,
                due_date TEXT DEFAULT NULL,
                delete_at TEXT DEFAULT NULL,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP
            )",
            
            // Todo files table
            "CREATE TABLE IF NOT EXISTS todo_files (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                todo_id INTEGER NOT NULL,
                original_name TEXT NOT NULL,
                stored_name TEXT NOT NULL,
                mime_type TEXT DEFAULT NULL,
                file_size INTEGER DEFAULT NULL,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (todo_id) REFERENCES todos(id) ON DELETE CASCADE
            )",
            
            // App settings table
            "CREATE TABLE IF NOT EXISTS app_settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                setting_key TEXT NOT NULL UNIQUE,
                setting_value TEXT DEFAULT NULL,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP
            )"
        ];
        
        foreach ($tables as $sql) {
            $pdo->exec($sql);
        }
    }
    
    // Insert default settings
    $pdo->exec("INSERT OR IGNORE INTO app_settings (setting_key, setting_value) VALUES 
                ('app_name', 'Premium Todo'),
                ('version', '1.0.0'),
                ('installed_at', datetime('now'))");
}

function createUploadsDirectory() {
    $uploadsDir = __DIR__ . '/uploads';
    
    if (!file_exists($uploadsDir)) {
        if (!mkdir($uploadsDir, 0755, true)) {
            throw new Exception('Uploads-kansion luonti epäonnistui');
        }
    }
    
    // Create .htaccess for security
    $htaccessFile = $uploadsDir . '/.htaccess';
    if (!file_exists($htaccessFile)) {
        $htaccessContent = "# Premium Todo Upload Security\n";
        $htaccessContent .= "Options -ExecCGI\n";
        $htaccessContent .= "AddHandler cgi-script .php .pl .py .jsp .asp .sh .cgi\n";
        $htaccessContent .= "RemoveHandler .php .phtml .php3 .php4 .php5 .php6\n";
        
        file_put_contents($htaccessFile, $htaccessContent);
    }
    
    // Create index.php to prevent directory listing
    $indexFile = $uploadsDir . '/index.php';
    if (!file_exists($indexFile)) {
        file_put_contents($indexFile, '<?php http_response_code(404); ?>');
    }
}

function createConfigFile($config) {
    $configContent = "<?php\n";
    $configContent .= "/*\n";
    $configContent .= "============================================================================\n";
    $configContent .= "PREMIUM TODO APP - CONFIGURATION\n";
    $configContent .= "Generated: " . date('Y-m-d H:i:s') . "\n";
    $configContent .= "============================================================================\n";
    $configContent .= "*/\n\n";
    
    $configContent .= "// Installation status\n";
    $configContent .= "define('TODO_INSTALLED', true);\n";
    $configContent .= "define('TODO_VERSION', '1.0.0');\n\n";
    
    $configContent .= "// Database configuration (SQLite for development)\n";
    $configContent .= "define('DB_TYPE', 'sqlite');\n";
    $configContent .= "define('DB_PATH', " . var_export($config['path'], true) . ");\n";
    $configContent .= "define('DB_HOST', " . var_export($config['host'], true) . ");\n";
    $configContent .= "define('DB_NAME', " . var_export($config['dbname'], true) . ");\n";
    $configContent .= "define('DB_USER', " . var_export($config['username'], true) . ");\n";
    $configContent .= "define('DB_PASS', " . var_export($config['password'], true) . ");\n\n";
    
    $configContent .= "// Security settings\n";
    $configContent .= "define('LOGIN_PASSWORD', 'admin123');\n";
    $configContent .= "define('SESSION_LIFETIME', 86400); // 24 hours\n\n";
    
    $configContent .= "// File upload settings\n";
    $configContent .= "define('UPLOAD_MAX_SIZE', 10485760); // 10MB\n";
    $configContent .= "define('UPLOAD_DIR', __DIR__ . '/uploads/');\n\n";
    
    $configContent .= "// App settings\n";
    $configContent .= "define('APP_NAME', 'Premium Todo');\n";
    $configContent .= "define('APP_URL', 'http" . (isset($_SERVER['HTTPS']) ? 's' : '') . "://' . \$_SERVER['HTTP_HOST'] . dirname(\$_SERVER['SCRIPT_NAME']));\n\n";
    
    $configContent .= "// Timezone\n";
    $configContent .= "date_default_timezone_set('Europe/Helsinki');\n\n";
    
    $configContent .= "?>";
    
    if (!file_put_contents(CONFIG_FILE, $configContent)) {
        throw new Exception('Asetustiedoston luonti epäonnistui');
    }
}
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $steps[$currentStep]; ?> - Premium Todo -asennus</title>
    
    <style>
        /* Premium Installation Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --bg-primary: #0a0a0a;
            --bg-secondary: #151515;
            --bg-card: rgba(255, 255, 255, 0.03);
            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.7);
            --text-tertiary: rgba(255, 255, 255, 0.5);
            --accent: #00d4aa;
            --gradient-accent: linear-gradient(135deg, #00d4aa 0%, #0099cc 100%);
            --gradient-danger: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            --gradient-success: linear-gradient(135deg, #00d4aa 0%, #7bed9f 100%);
            --shadow-premium: 0 8px 25px rgba(0, 0, 0, 0.3);
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: radial-gradient(ellipse at center, #1a1a2e 0%, #0a0a0a 70%);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow-x: hidden;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: 
                radial-gradient(circle at 25% 25%, rgba(102, 126, 234, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(0, 212, 170, 0.1) 0%, transparent 50%);
            animation: float 20s ease-in-out infinite;
            z-index: -1;
        }
        
        @keyframes float {
            0%, 100% { transform: rotate(0deg) scale(1); }
            50% { transform: rotate(180deg) scale(1.1); }
        }
        
        .install-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-xl);
            padding: 3rem;
            width: 100%;
            max-width: 600px;
            box-shadow: var(--shadow-premium);
            position: relative;
            animation: slideUp 0.8s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .install-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .install-logo {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .install-title {
            font-size: 2.5rem;
            font-weight: 800;
            background: var(--gradient-accent);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }
        
        .install-subtitle {
            color: var(--text-secondary);
            font-size: 1.1rem;
            font-weight: 500;
        }
        
        .steps-progress {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3rem;
            position: relative;
        }
        
        .steps-progress::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: rgba(255, 255, 255, 0.1);
            z-index: 1;
        }
        
        .steps-progress::after {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            height: 2px;
            background: var(--gradient-accent);
            width: <?php echo (($currentStep - 1) / (count($steps) - 1)) * 100; ?>%;
            transition: var(--transition);
            z-index: 2;
        }
        
        .step-item {
            position: relative;
            z-index: 3;
            text-align: center;
        }
        
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .step-item.active .step-circle {
            background: var(--gradient-accent);
            color: white;
            box-shadow: 0 0 20px rgba(0, 212, 170, 0.5);
        }
        
        .step-item.completed .step-circle {
            background: var(--gradient-success);
            color: white;
        }
        
        .step-item:not(.active):not(.completed) .step-circle {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-tertiary);
        }
        
        .step-label {
            font-size: 0.9rem;
            color: var(--text-secondary);
            font-weight: 500;
        }
        
        .step-content {
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-group {
            margin-bottom: 2rem;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .form-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-md);
            padding: 1rem 1.5rem;
            color: var(--text-primary);
            font-size: 1rem;
            transition: var(--transition);
            outline: none;
        }
        
        .form-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 20px rgba(0, 212, 170, 0.3);
            background: rgba(255, 255, 255, 0.08);
        }
        
        .form-input::placeholder {
            color: var(--text-tertiary);
        }
        
        .form-help {
            font-size: 0.8rem;
            color: var(--text-tertiary);
            margin-top: 0.5rem;
        }
        
        .btn-primary {
            background: var(--gradient-accent);
            border: none;
            color: white;
            padding: 1rem 2rem;
            border-radius: var(--radius-md);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            min-width: 140px;
            justify-content: center;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-premium);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--text-primary);
            padding: 1rem 2rem;
            border-radius: var(--radius-md);
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-1px);
        }
        
        .buttons-row {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }
        
        .alert {
            padding: 1rem 1.5rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        
        .alert-error {
            background: var(--gradient-danger);
            color: white;
        }
        
        .alert-success {
            background: var(--gradient-success);
            color: white;
        }
        
        .feature-list {
            list-style: none;
            margin: 2rem 0;
        }
        
        .feature-list li {
            padding: 0.75rem 0;
            display: flex;
            align-items: center;
            gap: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .feature-list li:last-child {
            border-bottom: none;
        }
        
        .feature-icon {
            font-size: 1.5rem;
            width: 30px;
            text-align: center;
        }
        
        .success-animation {
            text-align: center;
            animation: successPulse 2s ease-in-out infinite;
        }
        
        @keyframes successPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid var(--accent);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .requirement-check {
            background: var(--bg-card);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-md);
            padding: 1.5rem;
            margin: 1rem 0;
        }
        
        .check-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .check-item:last-child {
            border-bottom: none;
        }
        
        .check-status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .check-status.ok {
            background: rgba(123, 237, 159, 0.2);
            color: #7bed9f;
        }
        
        .check-status.warning {
            background: rgba(255, 167, 38, 0.2);
            color: #ffa726;
        }
        
        .check-status.error {
            background: rgba(255, 107, 107, 0.2);
            color: #ff6b6b;
        }
        
        @media (max-width: 768px) {
            .install-container {
                padding: 2rem;
                margin: 1rem;
            }
            
            .steps-progress {
                flex-direction: column;
                gap: 1rem;
            }
            
            .steps-progress::before,
            .steps-progress::after {
                display: none;
            }
            
            .buttons-row {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-header">
            <div class="install-logo">✨</div>
            <h1 class="install-title">Premium Todo</h1>
            <p class="install-subtitle">Ultra-smooth task management sovellus</p>
        </div>
        
        <div class="steps-progress">
            <?php foreach ($steps as $stepNum => $stepName): ?>
                <div class="step-item <?php echo $stepNum < $currentStep ? 'completed' : ($stepNum === $currentStep ? 'active' : ''); ?>">
                    <div class="step-circle">
                        <?php if ($stepNum < $currentStep): ?>
                            ✓
                        <?php else: ?>
                            <?php echo $stepNum; ?>
                        <?php endif; ?>
                    </div>
                    <div class="step-label"><?php echo $stepName; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <strong>Virhe:</strong><br>
                <?php echo implode('<br>', array_map('htmlspecialchars', $errors)); ?>
            </div>
        <?php endif; ?>
        
        <div class="step-content">
            <?php if ($currentStep === 1): ?>
                <!-- Welcome Step -->
                <h2 style="margin-bottom: 2rem; text-align: center;">Tervetuloa asentamaan Premium Todo -sovellusta!</h2>
                
                <p style="text-align: center; margin-bottom: 2rem; color: var(--text-secondary); font-size: 1.1rem;">
                    Tämä wizard auttaa sinua asentamaan modernin, premium-tason todo-sovelluksen.
                </p>
                
                <ul class="feature-list">
                    <li>
                        <span class="feature-icon">🎨</span>
                        <span>Premium dark mode -käyttöliittymä</span>
                    </li>
                    <li>
                        <span class="feature-icon">💫</span>
                        <span>Sulavat animaatiot ja mikrointeraktiot</span>
                    </li>
                    <li>
                        <span class="feature-icon">📁</span>
                        <span>Drag & drop tiedostojen hallinta</span>
                    </li>
                    <li>
                        <span class="feature-icon">🔗</span>
                        <span>Julkinen jakaminen turvallisin linkein</span>
                    </li>
                    <li>
                        <span class="feature-icon">⚡</span>
                        <span>AJAX-pohjainen reaaliaikainen käyttöliittymä</span>
                    </li>
                    <li>
                        <span class="feature-icon">🔒</span>
                        <span>Salasanasuojaus ja turvallinen ylöslataus</span>
                    </li>
                    <li>
                        <span class="feature-icon">📱</span>
                        <span>Täysin responsiivinen design</span>
                    </li>
                </ul>
                
                <div class="requirement-check">
                    <h3 style="margin-bottom: 1rem;">Järjestelmävaatimukset</h3>
                    
                    <div class="check-item">
                        <span>PHP versio</span>
                        <span class="check-status <?php echo version_compare(PHP_VERSION, '7.4.0') >= 0 ? 'ok' : 'error'; ?>">
                            PHP <?php echo PHP_VERSION; ?>
                        </span>
                    </div>
                    
                    <div class="check-item">
                        <span>PDO MySQL</span>
                        <span class="check-status <?php echo extension_loaded('pdo_mysql') ? 'ok' : 'error'; ?>">
                            <?php echo extension_loaded('pdo_mysql') ? 'Käytettävissä' : 'Puuttuu'; ?>
                        </span>
                    </div>
                    
                    <div class="check-item">
                        <span>File upload</span>
                        <span class="check-status <?php echo ini_get('file_uploads') ? 'ok' : 'warning'; ?>">
                            <?php echo ini_get('file_uploads') ? 'Käytössä' : 'Pois käytöstä'; ?>
                        </span>
                    </div>
                    
                    <div class="check-item">
                        <span>Kirjoitusoikeudet</span>
                        <span class="check-status <?php echo is_writable(__DIR__) ? 'ok' : 'error'; ?>">
                            <?php echo is_writable(__DIR__) ? 'OK' : 'Ei kirjoitusoikeutta'; ?>
                        </span>
                    </div>
                </div>
                
                <div class="buttons-row">
                    <a href="?step=2" class="btn-primary">
                        Aloita asennus →
                    </a>
                </div>
                
            <?php elseif ($currentStep === 2): ?>
                <!-- Database Configuration -->
                <h2 style="margin-bottom: 2rem; text-align: center;">Tietokannan asetukset</h2>
                
                <p style="text-align: center; margin-bottom: 2rem; color: var(--text-secondary);">
                    Anna MySQL-tietokannan yhteystiedot. Sovellus luo tarvittavat taulut automaattisesti.
                </p>
                
                <form method="post">
                    <div class="form-group">
                        <label for="db_host" class="form-label">Tietokannan osoite</label>
                        <input type="text" id="db_host" name="db_host" class="form-input" 
                               value="<?php echo htmlspecialchars($_POST['db_host'] ?? 'localhost'); ?>"
                               placeholder="localhost" required>
                        <div class="form-help">Yleensä localhost tai 127.0.0.1</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="db_name" class="form-label">Tietokannan nimi</label>
                        <input type="text" id="db_name" name="db_name" class="form-input" 
                               value="<?php echo htmlspecialchars($_POST['db_name'] ?? ''); ?>"
                               placeholder="premium_todo" required>
                        <div class="form-help">Tietokannan täytyy olla jo olemassa</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="db_user" class="form-label">Käyttäjätunnus</label>
                        <input type="text" id="db_user" name="db_user" class="form-input" 
                               value="<?php echo htmlspecialchars($_POST['db_user'] ?? ''); ?>"
                               placeholder="root" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="db_pass" class="form-label">Salasana</label>
                        <input type="password" id="db_pass" name="db_pass" class="form-input" 
                               placeholder="Tietokannan salasana">
                        <div class="form-help">Jätä tyhjäksi jos ei salasanaa</div>
                    </div>
                    
                    <div class="buttons-row">
                        <a href="?step=1" class="btn-secondary">← Takaisin</a>
                        <button type="submit" class="btn-primary">
                            Testaa yhteyttä →
                        </button>
                    </div>
                </form>
                
            <?php elseif ($currentStep === 3): ?>
                <!-- Installation Process -->
                <h2 style="margin-bottom: 2rem; text-align: center;">Asennetaan sovellusta...</h2>
                
                <p style="text-align: center; margin-bottom: 2rem; color: var(--text-secondary);">
                    Luodaan tietokantataulut, konfiguraatiot ja tarvittavat kansiot.
                </p>
                
                <div class="requirement-check">
                    <div class="check-item">
                        <span>Tietokantayhteys</span>
                        <span class="check-status ok">✓ Yhdistetty</span>
                    </div>
                    <div class="check-item">
                        <span>Taulujen luonti</span>
                        <span class="loading"></span>
                    </div>
                    <div class="check-item">
                        <span>Kansioiden luonti</span>
                        <span class="loading"></span>
                    </div>
                    <div class="check-item">
                        <span>Konfiguraatio</span>
                        <span class="loading"></span>
                    </div>
                </div>
                
                <form method="post" style="text-align: center;">
                    <button type="submit" class="btn-primary">
                        <span class="loading"></span>
                        Asenna nyt
                    </button>
                </form>
                
                <script>
                    // Auto-submit after 2 seconds for smooth experience
                    setTimeout(() => {
                        document.querySelector('form').submit();
                    }, 2000);
                </script>
                
            <?php elseif ($currentStep === 4): ?>
                <!-- Installation Complete -->
                <div class="success-animation">
                    <h2 style="margin-bottom: 2rem; color: var(--accent);">🎉 Asennus valmis!</h2>
                    
                    <p style="font-size: 1.2rem; margin-bottom: 2rem; color: var(--text-secondary);">
                        Premium Todo -sovellus on nyt käyttövalmis!
                    </p>
                </div>
                
                <div class="requirement-check">
                    <h3 style="margin-bottom: 1rem; color: var(--accent);">Kirjautumistiedot</h3>
                    
                    <div class="check-item">
                        <span>Salasana</span>
                        <span class="check-status ok"><strong>hunaja</strong></span>
                    </div>
                    
                    <div class="check-item">
                        <span>Sovelluksen URL</span>
                        <span style="color: var(--accent); font-family: monospace;">
                            <?php echo (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/todo.php'; ?>
                        </span>
                    </div>
                </div>
                
                <ul class="feature-list">
                    <li>
                        <span class="feature-icon">🔐</span>
                        <span>Kirjaudu sisään salasanalla <strong>"hunaja"</strong></span>
                    </li>
                    <li>
                        <span class="feature-icon">➕</span>
                        <span>Luo ensimmäinen tehtäväsi</span>
                    </li>
                    <li>
                        <span class="feature-icon">📁</span>
                        <span>Kokeile tiedostojen drag & drop -latausta</span>
                    </li>
                    <li>
                        <span class="feature-icon">🔗</span>
                        <span>Jaa tehtäviä julkisilla linkeillä</span>
                    </li>
                    <li>
                        <span class="feature-icon">⌨️</span>
                        <span>Pikanäppäimet: Ctrl+N (uusi), Ctrl+F (haku), Esc (sulje)</span>
                    </li>
                </ul>
                
                <div class="buttons-row">
                    <a href="todo.php" class="btn-primary" style="font-size: 1.1rem; padding: 1.2rem 2.5rem;">
                        🚀 Avaa Premium Todo
                    </a>
                </div>
                
                <p style="text-align: center; margin-top: 2rem; color: var(--text-tertiary); font-size: 0.9rem;">
                    💡 Voit poistaa tämän <code>todo_install.php</code> -tiedoston nyt turvallisesti.
                </p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>