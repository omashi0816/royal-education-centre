<?php
/**
 * Royal Education Center Management System
 * Course Model
 */

class Course extends Model {
    protected $table = 'courses';
    protected $primaryKey = 'id';
    
    // Get course with subjects
    public function getWithSubjects($id) {
        $sql = "SELECT c.*, GROUP_CONCAT(s.name SEPARATOR ', ') as subjects 
                FROM courses c 
                LEFT JOIN course_subjects cs ON c.id = cs.course_id 
                LEFT JOIN subjects s ON cs.subject_id = s.id 
                WHERE c.id = ? 
                GROUP BY c.id";
        return $this->db->query($sql)->bind(1, $id)->fetch();
    }
    
    // Get all courses with subject count
    public function getAllWithSubjectCount($status = null) {
        $sql = "SELECT c.*, COUNT(cs.subject_id) as subject_count 
                FROM courses c 
                LEFT JOIN course_subjects cs ON c.id = cs.course_id";
        if ($status) {
            $sql .= " WHERE c.status = ?";
        }
        $sql .= " GROUP BY c.id ORDER BY c.id DESC";
        
        $this->db->query($sql);
        if ($status) {
            $this->db->bind(1, $status);
        }
        return $this->db->fetchAll();
    }
    
    // Get course subjects
    public function getSubjects($courseId) {
        $sql = "SELECT s.*, cs.teacher_id, t.first_name, t.last_name 
                FROM subjects s 
                INNER JOIN course_subjects cs ON s.id = cs.subject_id 
                LEFT JOIN teachers t ON cs.teacher_id = t.id 
                WHERE cs.course_id = ? 
                ORDER BY s.name";
        return $this->db->query($sql)->bind(1, $courseId)->fetchAll();
    }
    
    // Add subject to course
    public function addSubject($courseId, $subjectId, $teacherId = null) {
        $sql = "INSERT INTO course_subjects (course_id, subject_id, teacher_id) VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE teacher_id = ?";
        return $this->db->query($sql)
                    ->bind(1, $courseId)
                    ->bind(2, $subjectId)
                    ->bind(3, $teacherId)
                    ->bind(4, $teacherId)
                    ->execute();
    }
    
    // Remove subject from course
    public function removeSubject($courseId, $subjectId) {
        $sql = "DELETE FROM course_subjects WHERE course_id = ? AND subject_id = ?";
        return $this->db->query($sql)->bind(1, $courseId)->bind(2, $subjectId)->execute();
    }
    
    // Get course batches
    public function getBatches($courseId) {
        return $this->db->query("SELECT b.*, 
                                       (SELECT COUNT(*) FROM enrollments WHERE batch_id = b.id AND status = 'active') as student_count 
                                FROM batches b WHERE b.course_id = ? ORDER BY b.start_date DESC")
                    ->bind(1, $courseId)
                    ->fetchAll();
    }
    
    // Search courses
    public function search($keyword) {
        $sql = "SELECT * FROM courses 
                WHERE code LIKE ? OR name LIKE ? OR description LIKE ?
                ORDER BY id DESC";
        $term = "%$keyword%";
        return $this->db->query($sql)
                    ->bind(1, $term)
                    ->bind(2, $term)
                    ->bind(3, $term)
                    ->fetchAll();
    }
}
