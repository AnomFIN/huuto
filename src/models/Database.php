<?php
/**
 * Database connection class
 */
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );
            
            // Build options array, checking if MySQL constants are available
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            // Only add MySQL-specific options if the constant is defined
            if (defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
                $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES " . DB_CHARSET . " COLLATE " . DB_CHARSET . "_unicode_ci";
            }
            
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
            // If MySQL init command wasn't set via options, set it manually
            if (!defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
                $this->pdo->exec("SET NAMES " . DB_CHARSET . " COLLATE " . DB_CHARSET . "_unicode_ci");
            }
            
            // Set timezone to match PHP timezone (Finland = UTC+3/+2)
            $this->pdo->exec("SET time_zone = '+03:00'");
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new RuntimeException("Tietokantayhteys epäonnistui. Yritä myöhemmin uudelleen.", 0, $e);
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
