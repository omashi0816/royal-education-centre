<?php
/**
 * Royal Education Center Management System
 * Batch Model
 */

class Batch extends Model {
    protected $table = 'batches';
    protected $primaryKey = 'id';
    
    // Get batch with course
    public function getWithCourse($id) {
        $sql = "SELECT b.*, c.name as course_name, c.code as course_code, c.course_fee 
                FROM batches b 
                INNER JOIN courses c ON b.course_id = c.id 
                WHERE b.id = ?";
        return $this->db->query($sql)->bind(1, $id)->fetch();
    }
    
    // Get all batches with course
    public function getAllWithCourse($status = null) {
        $sql = "SELECT b.*, c.name as course_name, c.code as course_code 
                FROM batches b 
                INNER JOIN courses c ON b.course_id = c.id";
        if ($status) {
            $sql .= " WHERE b.status = ?";
        }
        $sql .= " ORDER BY b.start_date DESC";
        
        $this->db->query($sql);
        if ($status) {
            $this->db->bind(1, $status);
        }
        return $this->db->fetchAll();
    }
    
    // Get batch by course
    public function getByCourse($courseId) {
        $sql = "SELECT b.*, c.name as course_name 
                FROM batches b 
                INNER JOIN courses c ON b.course_id = c.id 
                WHERE b.course_id = ? 
                ORDER BY b.start_date DESC";
        return $this->db->query($sql)->bind(1, $courseId)->fetchAll();
    }
    
    // Get batch enrollment count
    public function getEnrollmentCount($batchId) {
        $sql = "SELECT COUNT(*) FROM enrollments WHERE batch_id = ? AND status = 'active'";
        return $this->db->query($sql)->bind(1, $batchId)->fetchColumn();
    }
    
    // Get batch students
    public function getStudents($batchId) {
        $sql = "SELECT s.*, e.enrolled_date, e.status as enrollment_status, e.id as enrollment_id 
                FROM students s 
                INNER JOIN enrollments e ON s.id = e.student_id 
                WHERE e.batch_id = ? 
                ORDER BY s.first_name";
        return $this->db->query($sql)->bind(1, $batchId)->fetchAll();
    }
    
    // Search batches
    public function search($keyword) {
        $sql = "SELECT b.*, c.name as course_name 
                FROM batches b 
                INNER JOIN courses c ON b.course_id = c.id 
                WHERE b.batch_name LIKE ? OR c.name LIKE ?
                ORDER BY b.start_date DESC";
        $term = "%$keyword%";
        return $this->db->query($sql)
                    ->bind(1, $term)
                    ->bind(2, $term)
                    ->fetchAll();
    }
}
