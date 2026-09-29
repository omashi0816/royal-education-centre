<?php
/**
 * Royal Education Center Management System
 * Exam Model
 */

class Exam extends Model {
    protected $table = 'exams';
    protected $primaryKey = 'id';
    
    // Get exam with details
    public function getWithDetails($id) {
        $sql = "SELECT e.*, 
                       b.batch_name, 
                       s.name as subject_name, 
                       t.first_name, t.last_name 
                FROM exams e 
                INNER JOIN batches b ON e.batch_id = b.id 
                INNER JOIN subjects s ON e.subject_id = s.id 
                INNER JOIN teachers t ON e.teacher_id = t.id 
                WHERE e.id = ?";
        return $this->db->query($sql)->bind(1, $id)->fetch();
    }
    
    // Get exams by batch
    public function getByBatch($batchId) {
        $sql = "SELECT e.*, 
                       s.name as subject_name, 
                       t.first_name, t.last_name 
                FROM exams e 
                INNER JOIN subjects s ON e.subject_id = s.id 
                INNER JOIN teachers t ON e.teacher_id = t.id 
                WHERE e.batch_id = ? 
                ORDER BY e.exam_date DESC";
        return $this->db->query($sql)->bind(1, $batchId)->fetchAll();
    }
    
    // Get exams by teacher
    public function getByTeacher($teacherId) {
        $sql = "SELECT e.*, 
                       b.batch_name, 
                       s.name as subject_name 
                FROM exams e 
                INNER JOIN batches b ON e.batch_id = b.id 
                INNER JOIN subjects s ON e.subject_id = s.id 
                WHERE e.teacher_id = ? 
                ORDER BY e.exam_date DESC";
        return $this->db->query($sql)->bind(1, $teacherId)->fetchAll();
    }
    
    // Get exams by student
    public function getByStudent($studentId) {
        $sql = "SELECT e.*, 
                       b.batch_name, 
                       s.name as subject_name,
                       er.marks_obtained, 
                       er.total_marks, 
                       er.percentage, 
                       er.grade, 
                       er.status as result_status 
                FROM exams e 
                INNER JOIN batches b ON e.batch_id = b.id 
                INNER JOIN subjects s ON e.subject_id = s.id 
                INNER JOIN enrollments en ON b.id = en.batch_id 
                LEFT JOIN exam_results er ON e.id = er.exam_id AND er.student_id = en.student_id 
                WHERE en.student_id = ? AND en.status = 'active'
                ORDER BY e.exam_date DESC";
        return $this->db->query($sql)->bind(1, $studentId)->fetchAll();
    }
    
    // Get exam questions
    public function getQuestions($examId) {
        $sql = "SELECT * FROM exam_questions WHERE exam_id = ? ORDER BY display_order";
        return $this->db->query($sql)->bind(1, $examId)->fetchAll();
    }
    
    // Get exam results
    public function getResults($examId) {
        $sql = "SELECT er.*, 
                       s.student_code, s.first_name, s.last_name 
                FROM exam_results er 
                INNER JOIN students s ON er.student_id = s.id 
                WHERE er.exam_id = ? 
                ORDER BY er.marks_obtained DESC";
        return $this->db->query($sql)->bind(1, $examId)->fetchAll();
    }
    
    // Get student exam result
    public function getStudentResult($examId, $studentId) {
        $sql = "SELECT * FROM exam_results WHERE exam_id = ? AND student_id = ?";
        return $this->db->query($sql)->bind(1, $examId)->bind(2, $studentId)->fetch();
    }
    
    // Calculate grade from percentage
    public function calculateGrade($percentage, $passingMarks) {
        $percentage = floatval($percentage);
        
        if ($percentage < $passingMarks) {
            return 'F';
        } elseif ($percentage >= 90) {
            return 'A+';
        } elseif ($percentage >= 80) {
            return 'A';
        } elseif ($percentage >= 70) {
            return 'B';
        } elseif ($percentage >= 60) {
            return 'C';
        } elseif ($percentage >= 50) {
            return 'S';
        } else {
            return 'F';
        }
    }
}
