<?php
/**
 * Database connection class
 */
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            // Load config if not already loaded
            if (!defined('DB_HOST') || !defined('DB_NAME') || !defined('DB_USER') || !defined('DB_PASS')) {
                $this->loadDatabaseConfig();
            }
            
            $isProduction = (defined('APP_ENV') && APP_ENV === 'production') || 
                           (!defined('APP_ENV') && strpos($_SERVER['HTTP_HOST'] ?? '', 'huuto247.fi') !== false);
            
            // PRODUCTION: Use MySQL ONLY - No fallbacks allowed
            if ($isProduction) {
                $dsn = sprintf(
                    'mysql:host=%s;dbname=%s;charset=%s',
                    DB_HOST,
                    DB_NAME,
                    defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4'
                );
                
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ];
                
                $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                $this->pdo->exec("SET NAMES " . (defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4'));
                
                error_log("Database: Connected to production MySQL successfully");
                return;
            }
            
            // DEVELOPMENT: Try MySQL first, fallback to SQLite if needed
            try {
                if (defined('DB_TYPE') && DB_TYPE === 'sqlite') {
                    throw new Exception('Configured for SQLite');
                }
                
                // MySQL connection for development
                $dsn = sprintf(
                    'mysql:host=%s;dbname=%s;charset=%s',
                    DB_HOST,
                    DB_NAME,
                    defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4'
                );
                
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ];
                
                $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                $this->pdo->exec("SET NAMES " . (defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4'));
                
                error_log("Database: Connected to development MySQL successfully");
                
            } catch (Exception $mysqlError) {
                // Fallback to SQLite ONLY in development
                error_log("Database: MySQL connection failed in development, falling back to SQLite: " . $mysqlError->getMessage());
                
                $dbPath = defined('DB_PATH') ? DB_PATH : 'test.db';
                if ($dbPath[0] !== '/') {
                    $dbPath = (defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2)) . '/' . $dbPath;
                }
                
                $dsn = 'sqlite:' . $dbPath;
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                
                $this->pdo = new PDO($dsn, null, null, $options);
                error_log("Database: Connected to SQLite successfully as development fallback");
            }
            
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new RuntimeException("Tietokantayhteys epäonnistui: " . $e->getMessage() . ". Tarkista tietokanta-asetukset.", 0, $e);
        } catch (Exception $e) {
            error_log("General database error: " . $e->getMessage());
            throw new RuntimeException("Tietokantayhteys epäonnistui. Yritä myöhemmin uudelleen.", 0, $e);
        }
    }
    
    private function loadDatabaseConfig() {
        // Lataa tietokanta konfiguraatio config tiedostosta jos ei ole vielä ladattu
        if (!defined('DB_HOST') || !defined('DB_NAME') || !defined('DB_USER') || !defined('DB_PASS')) {
            // Tuotantopalvelimen MySQL asetukset - nämä pitää määritellä config.php:ssä
            if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
            if (!defined('DB_NAME')) define('DB_NAME', 'huuto247_db');  
            if (!defined('DB_USER')) define('DB_USER', 'huuto247_user');
            if (!defined('DB_PASS')) define('DB_PASS', ''); // Todellinen salasana config.php:ssä
            if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');
        }
    }
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}
