<?php
/**
 * Royal Education Center Management System
 * Enrollment Model
 */

require_once APP_PATH . '/models/Batch.php';

class Enrollment extends Model {
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    
    // Get enrollment with student and batch
    public function getWithDetails($id) {
        $sql = "SELECT e.*, 
                       s.student_code, s.first_name, s.last_name, 
                       b.batch_name, b.start_date, b.end_date,
                       c.name as course_name, c.code as course_code 
                FROM enrollments e 
                INNER JOIN students s ON e.student_id = s.id 
                INNER JOIN batches b ON e.batch_id = b.id 
                INNER JOIN courses c ON b.course_id = c.id 
                WHERE e.id = ?";
        return $this->db->query($sql)->bind(1, $id)->fetch();
    }
    
    // Get enrollments by student
    public function getByStudent($studentId) {
        $sql = "SELECT e.*, e.status as enrollment_status,
                       b.batch_name, b.start_date, b.end_date, b.status as batch_status,
                       c.name as course_name, c.code as course_code 
                FROM enrollments e 
                INNER JOIN batches b ON e.batch_id = b.id 
                INNER JOIN courses c ON b.course_id = c.id 
                WHERE e.student_id = ? 
                ORDER BY e.enrolled_date DESC";
        return $this->db->query($sql)->bind(1, $studentId)->fetchAll();
    }
    
    // Get enrollments by batch
    public function getByBatch($batchId) {
        $sql = "SELECT e.*, 
                       s.student_code, s.first_name, s.last_name, s.phone,
                       u.email 
                FROM enrollments e 
                INNER JOIN students s ON e.student_id = s.id 
                INNER JOIN users u ON s.user_id = u.id 
                WHERE e.batch_id = ? 
                ORDER BY s.first_name";
        return $this->db->query($sql)->bind(1, $batchId)->fetchAll();
    }
    
    // Enroll student in batch
    public function enrollStudent($studentId, $batchId) {
        // Check if already enrolled
        $sql = "SELECT id FROM enrollments WHERE student_id = ? AND batch_id = ?";
        $existing = $this->db->query($sql)->bind(1, $studentId)->bind(2, $batchId)->fetch();
        
        if ($existing) {
            return ['success' => false, 'error' => 'Student already enrolled in this batch'];
        }
        
        // Check batch capacity
        $batchModel = new Batch();
        $enrollmentCount = $batchModel->getEnrollmentCount($batchId);
        $batch = $batchModel->find($batchId);
        
        if ($enrollmentCount >= $batch['max_students']) {
            return ['success' => false, 'error' => 'Batch is full'];
        }
        
        $data = [
            'student_id' => $studentId,
            'batch_id' => $batchId,
            'enrolled_date' => date(DATE_FORMAT),
            'status' => 'active'
        ];
        
        $enrollmentId = $this->create($data);
        
        if ($enrollmentId) {
            return ['success' => true, 'enrollment_id' => $enrollmentId];
        }
        
        return ['success' => false, 'error' => 'Failed to enroll student'];
    }
    
    // Update enrollment status
    public function updateStatus($id, $status) {
        return $this->update($id, ['status' => $status]);
    }
    
    // Update enrollment grade
    public function updateGrade($id, $grade) {
        return $this->update($id, ['final_grade' => $grade]);
    }
}
