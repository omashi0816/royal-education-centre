<?php
class Attendance extends Model {
    protected $table = 'attendance';
    protected $primaryKey = 'id';
    
    public function getByBatchAndDate($batchId, $date) {
        $sql = "SELECT a.*, s.student_code, s.first_name, s.last_name 
                FROM attendance a 
                INNER JOIN enrollments e ON a.enrollment_id = e.id 
                INNER JOIN students s ON e.student_id = s.id 
                WHERE e.batch_id = ? AND a.date = ? 
                ORDER BY s.first_name";
        return $this->db->query($sql)->bind(1, $batchId)->bind(2, $date)->fetchAll();
    }
    
    public function getByStudent($studentId, $limit = null) {
        $sql = "SELECT a.*, b.batch_name 
                FROM attendance a 
                INNER JOIN enrollments e ON a.enrollment_id = e.id 
                INNER JOIN batches b ON e.batch_id = b.id 
                WHERE e.student_id = ? 
                ORDER BY a.date DESC";
        if ($limit) $sql .= " LIMIT $limit";
        return $this->db->query($sql)->bind(1, $studentId)->fetchAll();
    }
    
    public function saveAttendance($batchId, $date, $records) {
        $this->beginTransaction();
        try {
            foreach ($records as $studentId => $status) {
                $sql = "SELECT id FROM enrollments WHERE student_id = ? AND batch_id = ?";
                $enrollment = $this->db->query($sql)->bind(1, $studentId)->bind(2, $batchId)->fetch();
                if (!$enrollment) continue;
                
                $enrollmentId = $enrollment['id'];
                
                $sql = "SELECT id FROM attendance WHERE enrollment_id = ? AND date = ?";
                $existing = $this->db->query($sql)->bind(1, $enrollmentId)->bind(2, $date)->fetch();
                
                if ($existing) {
                    $sql = "UPDATE attendance SET status = ?, marked_by = ? WHERE id = ?";
                    $this->db->query($sql);
                    $this->db->bind(1, $status);
                    $this->db->bind(2, currentUserId());
                    $this->db->bind(3, $existing['id']);
                } else {
                    $sql = "INSERT INTO attendance (enrollment_id, date, status, marked_by) VALUES (?, ?, ?, ?)";
                    $this->db->query($sql);
                    $this->db->bind(1, $enrollmentId);
                    $this->db->bind(2, $date);
                    $this->db->bind(3, $status);
                    $this->db->bind(4, currentUserId());
                }
                $this->db->execute();
            }
            $this->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->rollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function getAttendanceStats($studentId) {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present,
                    SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent,
                    SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late,
                    SUM(CASE WHEN a.status = 'excused' THEN 1 ELSE 0 END) as excused
                FROM attendance a 
                INNER JOIN enrollments e ON a.enrollment_id = e.id 
                WHERE e.student_id = ?";
        return $this->db->query($sql)->bind(1, $studentId)->fetch();
    }

    public function getByTeacher($teacherId) {
        $sql = "SELECT DISTINCT a.*, b.batch_name,
                       s.student_code, s.first_name, s.last_name
                FROM attendance a
                INNER JOIN enrollments e ON a.enrollment_id = e.id
                INNER JOIN batches b ON e.batch_id = b.id
                INNER JOIN students s ON e.student_id = s.id
                WHERE EXISTS (
                    SELECT 1 FROM timetable_slots ts
                    WHERE ts.batch_id = e.batch_id AND ts.teacher_id = ?
                )
                ORDER BY a.date DESC, s.first_name, s.last_name";
        return $this->db->query($sql)->bind(1, $teacherId)->fetchAll();
    }
}
