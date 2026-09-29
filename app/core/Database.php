<?php
/**
 * Royal Education Center Management System
 * Database Class - PDO Wrapper
 */

class Database {
    private static $instance = null;
    private $connection;
    private $statement;
    
    // Private constructor for singleton
    private function __construct() {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => false
        ];
        
        try {
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }
    
    // Singleton pattern - get instance
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
    
    // Begin transaction
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    // Commit transaction
    public function commit() {
        return $this->connection->commit();
    }
    
    // Rollback transaction
    public function rollback() {
        return $this->connection->rollBack();
    }
    
    // Prepare query
    public function query($sql) {
        $this->statement = $this->connection->prepare($sql);
        return $this;
    }
    
    // Bind values
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->statement->bindValue($param, $value, $type);
        return $this;
    }
    
    // Execute prepared statement
    public function execute() {
        try {
            return $this->statement->execute();
        } catch (PDOException $e) {
            $this->handleError($e);
            return false;
        }
    }
    
    // Fetch single row
    public function fetch() {
        $this->execute();
        return $this->statement->fetch();
    }
    
    // Fetch all rows
    public function fetchAll() {
        $this->execute();
        return $this->statement->fetchAll();
    }
    
    // Fetch single column
    public function fetchColumn() {
        $this->execute();
        return $this->statement->fetchColumn();
    }
    
    // Get last insert ID
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    // Get row count
    public function rowCount() {
        return $this->statement->rowCount();
    }
    
    // Get raw PDO connection (for complex queries)
    public function getConnection() {
        return $this->connection;
    }
    
    // Handle database errors
    private function handleError($e) {
        if (ENVIRONMENT === 'development') {
            die('Database Error: ' . $e->getMessage());
        } else {
            error_log('Database Error: ' . $e->getMessage());
            die('A database error occurred. Please try again later.');
        }
    }
    
    // Close connection
    public function close() {
        $this->connection = null;
        self::$instance = null;
    }
}
