<?php
/**
 * Royal Education Center Management System
 * Assignment Model
 */

class Assignment extends Model {
    protected $table = 'assignments';
    protected $primaryKey = 'id';
    
    // Get assignment with details
    public function getWithDetails($id) {
        $sql = "SELECT a.*, 
                       b.batch_name, 
                       s.name as subject_name, 
                       t.first_name, t.last_name 
                FROM assignments a 
                INNER JOIN batches b ON a.batch_id = b.id 
                INNER JOIN subjects s ON a.subject_id = s.id 
                INNER JOIN teachers t ON a.teacher_id = t.id 
                WHERE a.id = ?";
        return $this->db->query($sql)->bind(1, $id)->fetch();
    }
    
    // Get assignments by batch
    public function getByBatch($batchId) {
        $sql = "SELECT a.*, 
                       s.name as subject_name, 
                       t.first_name, t.last_name 
                FROM assignments a 
                INNER JOIN subjects s ON a.subject_id = s.id 
                INNER JOIN teachers t ON a.teacher_id = t.id 
                WHERE a.batch_id = ? 
                ORDER BY a.due_date DESC";
        return $this->db->query($sql)->bind(1, $batchId)->fetchAll();
    }
    
    // Get assignments by teacher
    public function getByTeacher($teacherId) {
        $sql = "SELECT a.*, 
                       b.batch_name, 
                       s.name as subject_name 
                FROM assignments a 
                INNER JOIN batches b ON a.batch_id = b.id 
                INNER JOIN subjects s ON a.subject_id = s.id 
                WHERE a.teacher_id = ? 
                ORDER BY a.due_date DESC";
        return $this->db->query($sql)->bind(1, $teacherId)->fetchAll();
    }
    
    // Get assignments by student
    public function getByStudent($studentId) {
        $sql = "SELECT a.*, 
                       b.batch_name, 
                       s.name as subject_name,
                       asub.id as submission_id, 
                       asub.status as submission_status, 
                       asub.submitted_at,
                       asub.marks 
                FROM assignments a 
                INNER JOIN batches b ON a.batch_id = b.id 
                INNER JOIN subjects s ON a.subject_id = s.id 
                INNER JOIN enrollments e ON b.id = e.batch_id 
                LEFT JOIN assignment_submissions asub ON a.id = asub.assignment_id AND asub.student_id = e.student_id 
                WHERE e.student_id = ? AND e.status = 'active'
                ORDER BY a.due_date DESC";
        return $this->db->query($sql)->bind(1, $studentId)->fetchAll();
    }
    
    // Get assignment submissions
    public function getSubmissions($assignmentId) {
        $sql = "SELECT asub.*, 
                       s.student_code, s.first_name, s.last_name,
                       u.email 
                FROM assignment_submissions asub 
                INNER JOIN students s ON asub.student_id = s.id 
                INNER JOIN users u ON s.user_id = u.id 
                WHERE asub.assignment_id = ? 
                ORDER BY asub.submitted_at DESC";
        return $this->db->query($sql)->bind(1, $assignmentId)->fetchAll();
    }
    
    // Get submission by student
    public function getSubmissionByStudent($assignmentId, $studentId) {
        $sql = "SELECT * FROM assignment_submissions 
                WHERE assignment_id = ? AND student_id = ?";
        return $this->db->query($sql)->bind(1, $assignmentId)->bind(2, $studentId)->fetch();
    }
}
