<?php
/**
 * Royal Education Center Management System
 * Student Model
 */

class Student extends Model {
    protected $table = 'students';
    protected $primaryKey = 'id';
    
    // Get student with user details
    public function getWithUser($id) {
        $sql = "SELECT s.*, u.id as user_id, u.username, u.email, u.phone, u.status, u.avatar 
                FROM students s 
                INNER JOIN users u ON s.user_id = u.id 
                WHERE s.id = ?";
        return $this->db->query($sql)->bind(1, $id)->fetch();
    }
    
    // Get all students with user details
    public function getAllWithUser($status = null) {
        $sql = "SELECT s.*, u.id as user_id, u.username, u.email, u.phone, u.status, u.avatar 
                FROM students s 
                INNER JOIN users u ON s.user_id = u.id";
        if ($status) {
            $sql .= " WHERE u.status = ?";
        }
        $sql .= " ORDER BY s.id DESC";
        
        $this->db->query($sql);
        if ($status) {
            $this->db->bind(1, $status);
        }
        return $this->db->fetchAll();
    }
    
    // Get student by student code
    public function getByCode($code) {
        $sql = "SELECT s.*, u.id as user_id, u.username, u.email, u.phone, u.status 
                FROM students s 
                INNER JOIN users u ON s.user_id = u.id 
                WHERE s.student_code = ?";
        return $this->db->query($sql)->bind(1, $code)->fetch();
    }
    
    // Get student by user ID
    public function getByUserId($userId) {
        return $this->where('user_id', $userId)[0] ?? null;
    }
    
    // Create student with user
    public function createWithUser($userData, $studentData) {
        $this->beginTransaction();
        
        try {
            // Create user
            $userSql = "INSERT INTO users (role_id, username, email, password_hash, full_name, phone, status) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
            $this->db->query($userSql);
            $this->db->bind(1, ROLE_STUDENT);
            $this->db->bind(2, $userData['username']);
            $this->db->bind(3, $userData['email']);
            $this->db->bind(4, Security::hashPassword($userData['password']));
            $this->db->bind(5, $studentData['first_name'] . ' ' . $studentData['last_name']);
            $this->db->bind(6, $userData['phone'] ?? null);
            $this->db->bind(7, 'active');
            $this->db->execute();
            
            $userId = $this->db->lastInsertId();
            
            // Generate student code
            $studentCode = generateCode('S', 'students', 'student_code', 4);
            
            // Create student
            $studentData['user_id'] = $userId;
            $studentData['student_code'] = $studentCode;
            $studentId = $this->create($studentData);
            
            $this->commit();
            return ['success' => true, 'user_id' => $userId, 'student_id' => $studentId, 'student_code' => $studentCode];
        } catch (Exception $e) {
            $this->rollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    // Search students
    public function search($keyword) {
        $sql = "SELECT s.*, u.id as user_id, u.username, u.email, u.phone, u.status 
                FROM students s 
                INNER JOIN users u ON s.user_id = u.id 
                WHERE s.student_code LIKE ? 
                   OR s.first_name LIKE ? 
                   OR s.last_name LIKE ? 
                   OR u.email LIKE ? 
                   OR u.username LIKE ?
                ORDER BY s.id DESC";
        
        $term = "%$keyword%";
        $this->db->query($sql);
        $this->db->bind(1, $term);
        $this->db->bind(2, $term);
        $this->db->bind(3, $term);
        $this->db->bind(4, $term);
        $this->db->bind(5, $term);
        
        return $this->db->fetchAll();
    }
    
    // Get student enrollments
    public function getEnrollments($studentId) {
        $sql = "SELECT e.*, b.batch_name, c.name as course_name, c.code as course_code 
                FROM enrollments e 
                INNER JOIN batches b ON e.batch_id = b.id 
                INNER JOIN courses c ON b.course_id = c.id 
                WHERE e.student_id = ? 
                ORDER BY e.enrolled_date DESC";
        return $this->db->query($sql)->bind(1, $studentId)->fetchAll();
    }
}
