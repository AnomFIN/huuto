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
            
            // Check if using SQLite for development
            if (defined('DB_TYPE') && DB_TYPE === 'sqlite') {
                $dbPath = defined('DB_PATH') ? DB_PATH : 'test.db';
                // Handle relative and absolute paths
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
            } else {
                // MySQL connection for production
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
                
                // Set charset after connection  
                $this->pdo->exec("SET NAMES " . (defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4'));
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
