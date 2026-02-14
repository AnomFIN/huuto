<?php
/**
 * Database Wrapper
 * Provides PDO-based database access with prepared statements
 */

class DB {
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
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false, // Use real prepared statements
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            throw new RuntimeException('Tietokantayhteys epäonnistui. Yritä myöhemmin uudelleen.');
        }
    }
    
    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get raw PDO connection
     */
    public function getPDO() {
        return $this->pdo;
    }
    
    /**
     * Execute a query and return all results
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Query failed: ' . $e->getMessage() . ' SQL: ' . $sql);
            throw $e;
        }
    }
    
    /**
     * Execute a query and return a single row
     */
    public function queryOne($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Query failed: ' . $e->getMessage() . ' SQL: ' . $sql);
            throw $e;
        }
    }
    
    /**
     * Execute a query and return a single value
     */
    public function queryValue($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('Query failed: ' . $e->getMessage() . ' SQL: ' . $sql);
            throw $e;
        }
    }
    
    /**
     * Execute an INSERT/UPDATE/DELETE query
     */
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log('Execute failed: ' . $e->getMessage() . ' SQL: ' . $sql);
            throw $e;
        }
    }
    
    /**
     * Insert a record and return the last insert ID
     */
    public function insert($table, $data) {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_values($data));
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log('Insert failed: ' . $e->getMessage() . ' SQL: ' . $sql);
            throw $e;
        }
    }
    
    /**
     * Update records
     */
    public function update($table, $data, $where, $whereParams = []) {
        $sets = [];
        foreach (array_keys($data) as $column) {
            $sets[] = "$column = ?";
        }
        
        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $table,
            implode(', ', $sets),
            $where
        );
        
        $params = array_merge(array_values($data), $whereParams);
        
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log('Update failed: ' . $e->getMessage() . ' SQL: ' . $sql);
            throw $e;
        }
    }
    
    /**
     * Delete records
     */
    public function delete($table, $where, $params = []) {
        $sql = sprintf('DELETE FROM %s WHERE %s', $table, $where);
        
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log('Delete failed: ' . $e->getMessage() . ' SQL: ' . $sql);
            throw $e;
        }
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        return $this->pdo->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->pdo->rollBack();
    }
    
    /**
     * Get count of records
     */
    public function count($table, $where = '1=1', $params = []) {
        $sql = "SELECT COUNT(*) FROM $table WHERE $where";
        return $this->queryValue($sql, $params);
    }
    
    /**
     * Check if a record exists
     */
    public function exists($table, $where, $params = []) {
        return $this->count($table, $where, $params) > 0;
    }
}

/**
 * Global helper to get DB instance
 */
function db() {
    return DB::getInstance();
}
