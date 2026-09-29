<?php
/**
 * Royal Education Center Management System
 * Staff Model
 */

class Staff extends Model {
    protected $table = 'staff';
    protected $primaryKey = 'id';
    
    // Get staff with user details
    public function getWithUser($id) {
        $sql = "SELECT s.*, u.id as user_id, u.username, u.email, u.phone, u.status, u.avatar 
                FROM staff s 
                INNER JOIN users u ON s.user_id = u.id 
                WHERE s.id = ?";
        return $this->db->query($sql)->bind(1, $id)->fetch();
    }
    
    // Get all staff with user details
    public function getAllWithUser($status = null) {
        $sql = "SELECT s.*, u.id as user_id, u.username, u.email, u.phone, u.status, u.avatar 
                FROM staff s 
                INNER JOIN users u ON s.user_id = u.id";
        if ($status) {
            $sql .= " WHERE u.status = ?";
        }
        $sql .= " ORDER BY s.id DESC";
        
        $this->db->query($sql);
        if ($status) {
            $this->db->bind(1, $status);
        }
        return $this->db->fetchAll();
    }
    
    // Get staff by staff code
    public function getByCode($code) {
        $sql = "SELECT s.*, u.id as user_id, u.username, u.email, u.phone, u.status 
                FROM staff s 
                INNER JOIN users u ON s.user_id = u.id 
                WHERE s.staff_code = ?";
        return $this->db->query($sql)->bind(1, $code)->fetch();
    }
    
    // Get staff by user ID
    public function getByUserId($userId) {
        return $this->where('user_id', $userId)[0] ?? null;
    }
    
    // Create staff with user
    public function createWithUser($userData, $staffData) {
        $this->beginTransaction();
        
        try {
            // Create user
            $userSql = "INSERT INTO users (role_id, username, email, password_hash, full_name, phone, status) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
            $this->db->query($userSql);
            $this->db->bind(1, ROLE_MANAGER); // Staff uses manager role or create separate
            $this->db->bind(2, $userData['username']);
            $this->db->bind(3, $userData['email']);
            $this->db->bind(4, Security::hashPassword($userData['password']));
            $this->db->bind(5, $staffData['first_name'] . ' ' . $staffData['last_name']);
            $this->db->bind(6, $userData['phone'] ?? null);
            $this->db->bind(7, 'active');
            $this->db->execute();
            
            $userId = $this->db->lastInsertId();
            
            // Generate staff code
            $staffCode = generateCode('ST', 'staff', 'staff_code', 3);
            
            // Create staff
            $staffData['user_id'] = $userId;
            $staffData['staff_code'] = $staffCode;
            $staffId = $this->create($staffData);
            
            $this->commit();
            return ['success' => true, 'user_id' => $userId, 'staff_id' => $staffId, 'staff_code' => $staffCode];
        } catch (Exception $e) {
            $this->rollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    // Search staff
    public function search($keyword) {
        $sql = "SELECT s.*, u.id as user_id, u.username, u.email, u.phone, u.status 
                FROM staff s 
                INNER JOIN users u ON s.user_id = u.id 
                WHERE s.staff_code LIKE ? 
                   OR s.first_name LIKE ? 
                   OR s.last_name LIKE ? 
                   OR u.email LIKE ? 
                   OR u.username LIKE ?
                ORDER BY s.id DESC";
        
        $term = "%$keyword%";
        $this->db->query($sql);
        $this->db->bind(1, $term);
        $this->db->bind(2, $term);
        $this->db->bind(3, $term);
        $this->db->bind(4, $term);
        $this->db->bind(5, $term);
        
        return $this->db->fetchAll();
    }
}
