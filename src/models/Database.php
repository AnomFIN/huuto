<?php
/**
 * Database connection class
 */
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            // MySQL yhteys konfiguraatiosta
            if (!defined('DB_HOST') || !defined('DB_NAME') || !defined('DB_USER') || !defined('DB_PASS')) {
                // Lataa config tiedot jos ei ole vielä ladattu
                $this->loadDatabaseConfig();
            }
            
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
            
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new RuntimeException("Tietokantayhteys epäonnistui. Yritä myöhemmin uudelleen.", 0, $e);
        }
    }
    
    private function loadDatabaseConfig() {
        // Lataa tietokanta konfiguraatio jos ei ole vielä ladattu
        if (!defined('DB_HOST') || !defined('DB_NAME') || !defined('DB_USER') || !defined('DB_PASS')) {
            // Fallback arvot kehitysympäristöön
            define('DB_HOST', 'localhost');
            define('DB_NAME', 'huuto247');  // Käytä oikeaa tietokanta nimeä
            define('DB_USER', 'root');
            define('DB_PASS', '');
            define('DB_CHARSET', 'utf8mb4');
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
