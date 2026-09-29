<?php
/**
 * Royal Education Center Management System
 * Subject Model
 */

class Subject extends Model {
    protected $table = 'subjects';
    protected $primaryKey = 'id';
    
    // Get subject by code
    public function getByCode($code) {
        return $this->where('code', $code)[0] ?? null;
    }
    
    // Get subjects by course
    public function getByCourse($courseId) {
        $sql = "SELECT s.*, cs.teacher_id 
                FROM subjects s 
                INNER JOIN course_subjects cs ON s.id = cs.subject_id 
                WHERE cs.course_id = ?";
        return $this->db->query($sql)->bind(1, $courseId)->fetchAll();
    }
    
    // Search subjects
    public function search($keyword) {
        $sql = "SELECT * FROM subjects 
                WHERE code LIKE ? OR name LIKE ? OR description LIKE ?
                ORDER BY name";
        $term = "%$keyword%";
        return $this->db->query($sql)
                    ->bind(1, $term)
                    ->bind(2, $term)
                    ->bind(3, $term)
                    ->fetchAll();
    }
}
