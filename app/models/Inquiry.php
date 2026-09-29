<?php
class Inquiry extends Model {
    protected $table = 'inquiries';
    protected $primaryKey = 'id';
    
    public function getAllWithDetails() {
        $sql = "SELECT i.*, u.full_name as responded_by_name 
                FROM inquiries i 
                LEFT JOIN users u ON i.responded_by = u.id 
                ORDER BY i.created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }
    
    public function getByStatus($status) {
        return $this->where('status', $status);
    }
    
    public function markHandled($id, $userId, $notes = null) {
        return $this->update($id, [
            'status' => 'responded',
            'responded_by' => $userId,
            'responded_at' => date('Y-m-d H:i:s'),
            'response' => $notes
        ]);
    }
}
