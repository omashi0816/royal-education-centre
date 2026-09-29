<?php
/**
 * Royal Education Center Management System
 * Notice Model
 */

class Notice extends Model {
    protected $table = 'notices';
    protected $primaryKey = 'id';
    
    // Get notice with poster details
    public function getWithDetails($id) {
        $sql = "SELECT n.*, u.full_name as posted_by_name, u.role_id 
                FROM notices n 
                INNER JOIN users u ON n.posted_by = u.id 
                WHERE n.id = ?";
        return $this->db->query($sql)->bind(1, $id)->fetch();
    }
    
    // Get all notices with poster details
    public function getAllWithDetails() {
        $sql = "SELECT n.*, u.full_name as posted_by_name 
                FROM notices n 
                INNER JOIN users u ON n.posted_by = u.id 
                ORDER BY n.is_pinned DESC, n.created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }
    
    // Get notices by role
    public function getByRole($role) {
        $sql = "SELECT n.*, u.full_name as posted_by_name 
                FROM notices n 
                INNER JOIN users u ON n.posted_by = u.id 
                WHERE n.target_role = 'all' OR n.target_role = ? 
                ORDER BY n.is_pinned DESC, n.created_at DESC";
        return $this->db->query($sql)->bind(1, $role)->fetchAll();
    }
    
    // Get notices by batch
    public function getByBatch($batchId) {
        $sql = "SELECT n.*, u.full_name as posted_by_name 
                FROM notices n 
                INNER JOIN users u ON n.posted_by = u.id 
                WHERE n.target_batch_id = ? OR n.target_batch_id IS NULL 
                ORDER BY n.is_pinned DESC, n.created_at DESC";
        return $this->db->query($sql)->bind(1, $batchId)->fetchAll();
    }
    
    // Get pinned notices
    public function getPinned($role = null) {
        $sql = "SELECT n.*, u.full_name as posted_by_name 
                FROM notices n 
                INNER JOIN users u ON n.posted_by = u.id 
                WHERE n.is_pinned = 1";
        
        if ($role) {
            $sql .= " AND (n.target_role = 'all' OR n.target_role = ?)";
        }
        
        $sql .= " ORDER BY n.created_at DESC";
        
        $this->db->query($sql);
        if ($role) {
            $this->db->bind(1, $role);
        }
        
        return $this->db->fetchAll();
    }
    
    // Get recent notices
    public function getRecent($limit = 5, $role = null) {
        $sql = "SELECT n.*, u.full_name as posted_by_name 
                FROM notices n 
                INNER JOIN users u ON n.posted_by = u.id 
                WHERE 1=1";
        
        if ($role) {
            $sql .= " AND (n.target_role = 'all' OR n.target_role = ?)";
        }
        
        $sql .= " ORDER BY n.is_pinned DESC, n.created_at DESC LIMIT ?";
        
        $this->db->query($sql);
        if ($role) {
            $this->db->bind(1, $role);
            $this->db->bind(2, $limit);
        } else {
            $this->db->bind(1, $limit);
        }
        
        return $this->db->fetchAll();
    }
}
