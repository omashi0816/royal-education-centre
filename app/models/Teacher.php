<?php
/**
 * Royal Education Center Management System
 * Teacher Model
 */

class Teacher extends Model {
    protected $table = 'teachers';
    protected $primaryKey = 'id';
    
    // Get teacher with user details
    public function getWithUser($id) {
        $sql = "SELECT t.*, u.id as user_id, u.username, u.email, u.phone, u.status, u.avatar 
                FROM teachers t 
                INNER JOIN users u ON t.user_id = u.id 
                WHERE t.id = ?";
        return $this->db->query($sql)->bind(1, $id)->fetch();
    }
    
    // Get all teachers with user details
    public function getAllWithUser($status = null) {
        $sql = "SELECT t.*, u.id as user_id, u.username, u.email, u.phone, u.status, u.avatar 
                FROM teachers t 
                INNER JOIN users u ON t.user_id = u.id";
        if ($status) {
            $sql .= " WHERE u.status = ?";
        }
        $sql .= " ORDER BY t.id DESC";
        
        $this->db->query($sql);
        if ($status) {
            $this->db->bind(1, $status);
        }
        return $this->db->fetchAll();
    }
    
    // Get teacher by teacher code
    public function getByCode($code) {
        $sql = "SELECT t.*, u.id as user_id, u.username, u.email, u.phone, u.status 
                FROM teachers t 
                INNER JOIN users u ON t.user_id = u.id 
                WHERE t.teacher_code = ?";
        return $this->db->query($sql)->bind(1, $code)->fetch();
    }
    
    // Get teacher by user ID
    public function getByUserId($userId) {
        return $this->where('user_id', $userId)[0] ?? null;
    }
    
    // Create teacher with user
    public function createWithUser($userData, $teacherData) {
        $this->beginTransaction();
        
        try {
            // Create user
            $userSql = "INSERT INTO users (role_id, username, email, password_hash, full_name, phone, status) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
            $this->db->query($userSql);
            $this->db->bind(1, ROLE_TEACHER);
            $this->db->bind(2, $userData['username']);
            $this->db->bind(3, $userData['email']);
            $this->db->bind(4, Security::hashPassword($userData['password']));
            $this->db->bind(5, $teacherData['first_name'] . ' ' . $teacherData['last_name']);
            $this->db->bind(6, $userData['phone'] ?? null);
            $this->db->bind(7, 'active');
            $this->db->execute();
            
            $userId = $this->db->lastInsertId();
            
            // Generate teacher code
            $teacherCode = generateCode('T', 'teachers', 'teacher_code', 3);
            
            // Create teacher
            $teacherData['user_id'] = $userId;
            $teacherData['teacher_code'] = $teacherCode;
            $teacherId = $this->create($teacherData);
            
            $this->commit();
            return ['success' => true, 'user_id' => $userId, 'teacher_id' => $teacherId, 'teacher_code' => $teacherCode];
        } catch (Exception $e) {
            $this->rollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    // Get teacher assigned batches
    public function getAssignedBatches($teacherId) {
        $sql = "SELECT b.*, c.name as course_name,
                       s.name as subject_name,
                       ts.day_of_week, ts.start_time, ts.end_time
                FROM batches b 
                INNER JOIN courses c ON b.course_id = c.id
                INNER JOIN timetable_slots ts ON b.id = ts.batch_id 
                INNER JOIN subjects s ON ts.subject_id = s.id
                WHERE ts.teacher_id = ?
                ORDER BY b.start_date DESC, FIELD(ts.day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday'), ts.start_time";
        return $this->db->query($sql)->bind(1, $teacherId)->fetchAll();
    }
    
    // Get teacher assigned subjects
    public function getAssignedSubjects($teacherId) {
        $sql = "SELECT DISTINCT s.* 
                FROM subjects s 
                INNER JOIN course_subjects cs ON s.id = cs.subject_id 
                WHERE cs.teacher_id = ?
                ORDER BY s.name";
        return $this->db->query($sql)->bind(1, $teacherId)->fetchAll();
    }
    
    // Search teachers
    public function search($keyword) {
        $sql = "SELECT t.*, u.id as user_id, u.username, u.email, u.phone, u.status 
                FROM teachers t 
                INNER JOIN users u ON t.user_id = u.id 
                WHERE t.teacher_code LIKE ? 
                   OR t.first_name LIKE ? 
                   OR t.last_name LIKE ? 
                   OR u.email LIKE ? 
                   OR u.username LIKE ?
                ORDER BY t.id DESC";
        
        $term = "%$keyword%";
        $this->db->query($sql);
        $this->db->bind(1, $term);
        $this->db->bind(2, $term);
        $this->db->bind(3, $term);
        $this->db->bind(4, $term);
        $this->db->bind(5, $term);
        
        return $this->db->fetchAll();
    }
}
