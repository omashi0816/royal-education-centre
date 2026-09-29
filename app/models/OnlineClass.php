<?php
class OnlineClass extends Model {
    protected $table = 'online_classes';
    protected $primaryKey = 'id';
    
    public function getWithDetails($id) {
        $sql = "SELECT oc.*, b.batch_name, s.name as subject_name, 
                       t.first_name, t.last_name 
                FROM online_classes oc 
                INNER JOIN batches b ON oc.batch_id = b.id 
                INNER JOIN subjects s ON oc.subject_id = s.id 
                INNER JOIN teachers t ON oc.teacher_id = t.id 
                WHERE oc.id = ?";
        return $this->db->query($sql)->bind(1, $id)->fetch();
    }
    
    public function getByBatch($batchId) {
        $sql = "SELECT oc.*, s.name as subject_name 
                FROM online_classes oc 
                INNER JOIN subjects s ON oc.subject_id = s.id 
                WHERE oc.batch_id = ? 
                ORDER BY oc.class_date DESC, oc.start_time DESC";
        return $this->db->query($sql)->bind(1, $batchId)->fetchAll();
    }
    
    public function getByTeacher($teacherId) {
        $sql = "SELECT oc.*, b.batch_name, s.name as subject_name 
                FROM online_classes oc 
                INNER JOIN batches b ON oc.batch_id = b.id 
                INNER JOIN subjects s ON oc.subject_id = s.id 
                WHERE oc.teacher_id = ? 
                ORDER BY oc.class_date DESC, oc.start_time DESC";
        return $this->db->query($sql)->bind(1, $teacherId)->fetchAll();
    }
    
    public function getByStudent($studentId) {
        $sql = "SELECT oc.*, b.batch_name, s.name as subject_name 
                FROM online_classes oc 
                INNER JOIN batches b ON oc.batch_id = b.id 
                INNER JOIN subjects s ON oc.subject_id = s.id 
                INNER JOIN enrollments e ON b.id = e.batch_id 
                WHERE e.student_id = ? AND e.status = 'active' 
                ORDER BY oc.class_date DESC, oc.start_time DESC";
        return $this->db->query($sql)->bind(1, $studentId)->fetchAll();
    }
    
    public function getUpcoming($batchId = null) {
        $sql = "SELECT oc.*, b.batch_name, s.name as subject_name 
                FROM online_classes oc 
                INNER JOIN batches b ON oc.batch_id = b.id 
                INNER JOIN subjects s ON oc.subject_id = s.id 
                WHERE TIMESTAMP(oc.class_date, oc.start_time) > NOW() AND oc.status = 'scheduled'";
        if ($batchId) {
            $sql .= " AND oc.batch_id = ?";
        }
        $sql .= " ORDER BY oc.class_date ASC, oc.start_time ASC";
        
        $this->db->query($sql);
        if ($batchId) $this->db->bind(1, $batchId);
        return $this->db->fetchAll();
    }
}
