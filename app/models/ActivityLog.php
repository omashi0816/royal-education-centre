<?php
class ActivityLog extends Model {
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    
    public function getAllWithUsers($limit = 50, $filters = []) {
        $sql = "SELECT al.*, u.full_name, u.username 
                FROM activity_logs al 
                LEFT JOIN users u ON al.user_id = u.id 
                WHERE 1=1";
        $params = [];
        
        if (!empty($filters['action'])) {
            $sql .= " AND al.action = ?";
            $params[] = $filters['action'];
        }
        if (!empty($filters['module'])) {
            $sql .= " AND al.module = ?";
            $params[] = $filters['module'];
        }
        if (!empty($filters['user_id'])) {
            $sql .= " AND al.user_id = ?";
            $params[] = $filters['user_id'];
        }
        
        $sql .= " ORDER BY al.created_at DESC LIMIT ?";
        $params[] = $limit;
        
        $this->db->query($sql);
        $i = 1;
        foreach ($params as $param) {
            $this->db->bind($i++, $param);
        }
        return $this->db->fetchAll();
    }
    
    public function getByUser($userId, $limit = 20) {
        $sql = "SELECT * FROM activity_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT ?";
        return $this->db->query($sql)->bind(1, $userId)->bind(2, $limit)->fetchAll();
    }
}
