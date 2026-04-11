<?php
/**
 * Database connection class
 */
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            $dbPath = __DIR__ . '/../../database/huuto247.db';
            $dsn = "sqlite:$dbPath";
            
            $this->pdo = new PDO($dsn, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            
            // Enable foreign keys for SQLite
            $this->pdo->exec('PRAGMA foreign_keys = ON');
            
        } catch (PDOException $e) {
            throw new RuntimeException("Tietokantayhteys epäonnistui: " . $e->getMessage());
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
