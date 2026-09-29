<?php
/**
 * Royal Education Center Management System
 * Base Model Class
 */

abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Find by primary key
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1";
        return $this->db->query($sql)->bind(1, $id)->fetch();
    }
    
    // Find all records
    public function all($orderBy = null, $limit = null) {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        return $this->db->query($sql)->fetchAll();
    }
    
    // Find by condition
    public function where($column, $operator, $value = null) {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        $sql = "SELECT * FROM {$this->table} WHERE $column $operator ?";
        return $this->db->query($sql)->bind(1, $value)->fetchAll();
    }
    
    // Find first by condition
    public function firstWhere($column, $operator, $value = null) {
        $results = $this->where($column, $operator, $value);
        return $results ? $results[0] : null;
    }
    
    // Create new record
    public function create($data) {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $this->db->query($sql);
        $i = 1;
        foreach ($data as $value) {
            $this->db->bind($i++, $value);
        }
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    // Update record
    public function update($id, $data) {
        $set = [];
        foreach (array_keys($data) as $column) {
            $set[] = "$column = ?";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . " 
                WHERE {$this->primaryKey} = ?";
        
        $this->db->query($sql);
        $i = 1;
        foreach ($data as $value) {
            $this->db->bind($i++, $value);
        }
        $this->db->bind($i, $id);
        
        return $this->db->execute();
    }
    
    // Delete record
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->query($sql)->bind(1, $id)->execute();
    }
    
    // Count records
    public function count($where = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        if ($where) {
            $sql .= " WHERE $where";
        }
        return $this->db->query($sql)->fetchColumn();
    }
    
    // Paginate records
    public function paginate($page = 1, $perPage = ITEMS_PER_PAGE, $orderBy = null) {
        $page = max(1, (int)$page);
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        $sql .= " LIMIT $perPage OFFSET $offset";
        
        $results = $this->db->query($sql)->fetchAll();
        $total = $this->count();
        
        return [
            'data' => $results,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }
    
    // Custom query
    public function query($sql, $params = []) {
        $this->db->query($sql);
        $i = 1;
        foreach ($params as $param) {
            $this->db->bind($i++, $param);
        }
        return $this->db->fetchAll();
    }
    
    // Execute custom query
    public function execute($sql, $params = []) {
        $this->db->query($sql);
        $i = 1;
        foreach ($params as $param) {
            $this->db->bind($i++, $param);
        }
        return $this->db->execute();
    }
    
    // Begin transaction
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }
    
    // Commit transaction
    public function commit() {
        return $this->db->commit();
    }
    
    // Rollback transaction
    public function rollback() {
        return $this->db->rollback();
    }
    
    // Get table name
    public function getTable() {
        return $this->table;
    }
}
