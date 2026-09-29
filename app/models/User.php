<?php
/**
 * Royal Education Center Management System
 * User Model
 */

class User extends Model {
    protected $table = 'users';
    protected $primaryKey = 'id';
    
    // Get user with role
    public function getUserWithRole($id) {
        $sql = "SELECT u.*, r.name as role_name, r.display_name as role_display_name 
                FROM users u 
                INNER JOIN roles r ON u.role_id = r.id 
                WHERE u.id = ?";
        return $this->db->query($sql)->bind(1, $id)->fetch();
    }
    
    // Get all users with role
    public function getAllWithRole($roleId = null) {
        $sql = "SELECT u.*, r.name as role_name, r.display_name as role_display_name 
                FROM users u 
                INNER JOIN roles r ON u.role_id = r.id";
        if ($roleId) {
            $sql .= " WHERE u.role_id = ?";
        }
        $sql .= " ORDER BY u.id DESC";
        
        $this->db->query($sql);
        if ($roleId) {
            $this->db->bind(1, $roleId);
        }
        return $this->db->fetchAll();
    }
    
    // Get all users with role (alias for getAllWithRole)
    public function getAllWithUser($status = null) {
        return $this->getAllWithRole();
    }

    // Get users by role
    public function getByRole($roleName) {
        $sql = "SELECT u.*, r.name as role_name 
                FROM users u 
                INNER JOIN roles r ON u.role_id = r.id 
                WHERE r.name = ? 
                ORDER BY u.id DESC";
        return $this->db->query($sql)->bind(1, $roleName)->fetchAll();
    }
    
    // Update user status
    public function updateStatus($id, $status) {
        $sql = "UPDATE users SET status = ? WHERE id = ?";
        return $this->db->query($sql)->bind(1, $status)->bind(2, $id)->execute();
    }
    
    // Search users
    public function search($keyword, $roleId = null) {
        $sql = "SELECT u.*, r.name as role_name 
                FROM users u 
                INNER JOIN roles r ON u.role_id = r.id 
                WHERE (u.username LIKE ? OR u.email LIKE ? OR u.full_name LIKE ?)";
        $params = ["%$keyword%", "%$keyword%", "%$keyword%"];
        
        if ($roleId) {
            $sql .= " AND u.role_id = ?";
            $params[] = $roleId;
        }
        
        $sql .= " ORDER BY u.id DESC";
        
        $this->db->query($sql);
        $i = 1;
        foreach ($params as $param) {
            $this->db->bind($i++, $param);
        }
        return $this->db->fetchAll();
    }
}
