<?php
/**
 * Royal Education Center Management System
 * Timetable Model
 */

class Timetable extends Model {
    protected $table = 'timetable_slots';
    protected $primaryKey = 'id';
    
    // Get timetable slot with details
    public function getWithDetails($id) {
        $sql = "SELECT ts.*, 
                       b.batch_name, 
                       c.name as course_name, c.code as course_code,
                       s.name as subject_name, s.code as subject_code,
                       t.first_name, t.last_name 
                FROM timetable_slots ts 
                INNER JOIN batches b ON ts.batch_id = b.id 
                INNER JOIN courses c ON b.course_id = c.id
                INNER JOIN subjects s ON ts.subject_id = s.id 
                INNER JOIN teachers t ON ts.teacher_id = t.id 
                WHERE ts.id = ?";
        return $this->db->query($sql)->bind(1, $id)->fetch();
    }
    
    // Get timetable by batch
    public function getByBatch($batchId) {
        $sql = "SELECT ts.*, 
                       b.batch_name,
                       c.name as course_name, c.code as course_code,
                       s.name as subject_name, s.code as subject_code,
                       t.first_name, t.last_name 
                FROM timetable_slots ts 
                INNER JOIN batches b ON ts.batch_id = b.id
                INNER JOIN courses c ON b.course_id = c.id
                INNER JOIN subjects s ON ts.subject_id = s.id 
                INNER JOIN teachers t ON ts.teacher_id = t.id 
                WHERE ts.batch_id = ? 
                ORDER BY FIELD(ts.day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday'),
                         ts.start_time";
        return $this->db->query($sql)->bind(1, $batchId)->fetchAll();
    }
    
    // Get timetable by teacher
    public function getByTeacher($teacherId) {
        $sql = "SELECT ts.*, 
                       b.batch_name, 
                       s.name as subject_name, s.code as subject_code,
                       c.name as course_name 
                FROM timetable_slots ts 
                INNER JOIN batches b ON ts.batch_id = b.id 
                INNER JOIN courses c ON b.course_id = c.id 
                INNER JOIN subjects s ON ts.subject_id = s.id 
                WHERE ts.teacher_id = ? 
                ORDER BY FIELD(ts.day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday'),
                         ts.start_time";
        return $this->db->query($sql)->bind(1, $teacherId)->fetchAll();
    }
    
    // Get timetable by day
    public function getByDay($dayOfWeek, $batchId = null) {
        $sql = "SELECT ts.*, 
                       b.batch_name, 
                       s.name as subject_name,
                       t.first_name, t.last_name 
                FROM timetable_slots ts 
                INNER JOIN batches b ON ts.batch_id = b.id 
                INNER JOIN subjects s ON ts.subject_id = s.id 
                INNER JOIN teachers t ON ts.teacher_id = t.id 
                WHERE ts.day_of_week = ?";
        if ($batchId) {
            $sql .= " AND ts.batch_id = ?";
        }
        $sql .= " ORDER BY ts.start_time";
        
        $this->db->query($sql)->bind(1, $dayOfWeek);
        if ($batchId) {
            $this->db->bind(2, $batchId);
        }
        return $this->db->fetchAll();
    }
    
    // Check for time conflict
    public function hasTimeConflict($batchId, $dayOfWeek, $startTime, $endTime, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM timetable_slots 
                WHERE batch_id = ? 
                AND day_of_week = ? 
                AND ((start_time < ? AND end_time > ?) 
                     OR (start_time >= ? AND end_time <= ?)
                     OR (start_time < ? AND end_time >= ?))";
        
        $this->db->query($sql);
        $this->db->bind(1, $batchId);
        $this->db->bind(2, $dayOfWeek);
        $this->db->bind(3, $endTime);
        $this->db->bind(4, $startTime);
        $this->db->bind(5, $startTime);
        $this->db->bind(6, $endTime);
        $this->db->bind(7, $startTime);
        $this->db->bind(8, $endTime);
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $this->db->bind(9, $excludeId);
        }
        
        return $this->db->fetchColumn() > 0;
    }
}
