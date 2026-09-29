<?php
class StudyMaterial extends Model {
    protected $table = 'study_materials';
    protected $primaryKey = 'id';
    
    public function getWithDetails($id) {
        $sql = "SELECT sm.*, s.name as subject_name, b.batch_name, 
                       t.first_name, t.last_name 
                FROM study_materials sm 
                INNER JOIN subjects s ON sm.subject_id = s.id 
                INNER JOIN batches b ON sm.batch_id = b.id 
                INNER JOIN teachers t ON sm.teacher_id = t.id 
                WHERE sm.id = ?";
        return $this->db->query($sql)->bind(1, $id)->fetch();
    }
    
    public function getByBatch($batchId) {
        $sql = "SELECT sm.*, s.name as subject_name 
                FROM study_materials sm 
                INNER JOIN subjects s ON sm.subject_id = s.id 
                WHERE sm.batch_id = ? 
                ORDER BY sm.created_at DESC";
        return $this->db->query($sql)->bind(1, $batchId)->fetchAll();
    }
    
    public function getByTeacher($teacherId) {
        $sql = "SELECT sm.*, s.name as subject_name, b.batch_name 
                FROM study_materials sm 
                INNER JOIN subjects s ON sm.subject_id = s.id 
                INNER JOIN batches b ON sm.batch_id = b.id 
                WHERE sm.teacher_id = ? 
                ORDER BY sm.created_at DESC";
        return $this->db->query($sql)->bind(1, $teacherId)->fetchAll();
    }
    
    public function getByStudent($studentId) {
        $sql = "SELECT sm.*, s.name as subject_name, b.batch_name,
                       CONCAT(t.first_name, ' ', t.last_name) as teacher_name 
                FROM study_materials sm 
                INNER JOIN subjects s ON sm.subject_id = s.id 
                INNER JOIN batches b ON sm.batch_id = b.id 
                INNER JOIN teachers t ON sm.teacher_id = t.id
                INNER JOIN enrollments e ON b.id = e.batch_id 
                WHERE e.student_id = ? AND e.status = 'active' 
                ORDER BY sm.created_at DESC";
        return $this->db->query($sql)->bind(1, $studentId)->fetchAll();
    }
}
