<?php
/**
 * Royal Education Center Management System
 * Payment Model
 */

class Payment extends Model {
    protected $table = 'payments';
    protected $primaryKey = 'id';
    
    // Get payment with details
    public function getWithDetails($id) {
        $sql = "SELECT p.*, 
                       s.student_code, s.first_name, s.last_name,
                       u.email, u.phone,
                       b.batch_name, c.name as course_name 
                FROM payments p 
                INNER JOIN students s ON p.student_id = s.id 
                INNER JOIN users u ON s.user_id = u.id 
                LEFT JOIN enrollments e ON p.enrollment_id = e.id 
                LEFT JOIN batches b ON e.batch_id = b.id 
                LEFT JOIN courses c ON b.course_id = c.id 
                WHERE p.id = ?";
        return $this->db->query($sql)->bind(1, $id)->fetch();
    }
    
    // Get payments by student
    public function getByStudent($studentId) {
        $sql = "SELECT p.*, 
                       r.receipt_no 
                FROM payments p 
                LEFT JOIN receipts r ON p.id = r.payment_id 
                WHERE p.student_id = ? 
                ORDER BY p.payment_date DESC";
        return $this->db->query($sql)->bind(1, $studentId)->fetchAll();
    }

    // Get payments from students in batches or courses assigned to a teacher
    public function getByTeacher($teacherId) {
        $sql = "SELECT DISTINCT p.*, s.student_code, s.first_name, s.last_name,
                       r.receipt_no, b.batch_name, c.name as course_name
                FROM payments p
                INNER JOIN students s ON p.student_id = s.id
                LEFT JOIN receipts r ON p.id = r.payment_id
                INNER JOIN enrollments e ON p.student_id = e.student_id
                    AND e.status = 'active'
                    AND (p.enrollment_id IS NULL OR p.enrollment_id = e.id)
                INNER JOIN batches b ON e.batch_id = b.id
                INNER JOIN courses c ON b.course_id = c.id
                WHERE EXISTS (SELECT 1 FROM timetable_slots ts WHERE ts.batch_id = e.batch_id AND ts.teacher_id = ?)
                   OR EXISTS (SELECT 1 FROM assignments a WHERE a.batch_id = e.batch_id AND a.teacher_id = ?)
                   OR EXISTS (SELECT 1 FROM exams ex WHERE ex.batch_id = e.batch_id AND ex.teacher_id = ?)
                ORDER BY p.payment_date DESC, p.id DESC";
        return $this->db->query($sql)
            ->bind(1, $teacherId)
            ->bind(2, $teacherId)
            ->bind(3, $teacherId)
            ->fetchAll();
    }
    
    // Get all payments with student details
    public function getAllWithDetails($filters = []) {
        $sql = "SELECT p.*, 
                       s.student_code, s.first_name, s.last_name,
                       u.email,
                       r.receipt_no,
                       collector.full_name as collected_by_name 
                FROM payments p 
                INNER JOIN students s ON p.student_id = s.id 
                INNER JOIN users u ON s.user_id = u.id 
                LEFT JOIN receipts r ON p.id = r.payment_id 
                LEFT JOIN users collector ON p.collected_by = collector.id 
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND p.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['payment_type'])) {
            $sql .= " AND p.payment_type = ?";
            $params[] = $filters['payment_type'];
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND p.payment_date >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND p.payment_date <= ?";
            $params[] = $filters['date_to'];
        }
        
        $sql .= " ORDER BY p.payment_date DESC";
        
        $this->db->query($sql);
        foreach ($params as $i => $param) {
            $this->db->bind($i + 1, $param);
        }
        
        return $this->db->fetchAll();
    }
    
    // Get due payments
    public function getDuePayments($studentId = null) {
        $sql = "SELECT s.student_code, s.first_name, s.last_name,
                       c.name as course_name,
                       b.id as batch_id,
                       COALESCE(SUM(p.final_amount), 0) as paid_amount,
                       (c.course_fee + c.registration_fee) as total,
                       (c.course_fee + c.registration_fee) - COALESCE(SUM(p.final_amount), 0) as due_amount
                FROM students s
                INNER JOIN enrollments e ON s.id = e.student_id
                INNER JOIN batches b ON e.batch_id = b.id
                INNER JOIN courses c ON b.course_id = c.id
                LEFT JOIN payments p ON p.student_id = s.id
                    AND p.enrollment_id = e.id
                    AND p.status = 'completed'
                WHERE e.status = 'active'";
        
        if ($studentId) {
            $sql .= " AND s.id = ?";
        }
        
        $sql .= " GROUP BY s.id, b.id
                  HAVING due_amount > 0";
        
        $this->db->query($sql);
        if ($studentId) {
            $this->db->bind(1, $studentId);
        }
        
        return $this->db->fetchAll();
    }

    // Get the current month's monthly fee due for every active enrollment.
    public function getMonthlyDuesByStudent($studentId, $paymentMonth = null) {
        $paymentMonth = $paymentMonth ?: date('Y-m');
        $sql = "SELECT e.id as enrollment_id, s.student_code, s.first_name, s.last_name,
                       c.name as course_name, b.id as batch_id, c.monthly_fee,
                       COALESCE((SELECT SUM(p.final_amount)
                                 FROM payments p
                                 WHERE p.student_id = s.id
                                   AND p.enrollment_id = e.id
                                   AND p.payment_type = 'monthly_fee'
                                   AND p.payment_month = ?
                                   AND p.status = 'completed'), 0) as paid_amount,
                       GREATEST(c.monthly_fee - COALESCE((SELECT SUM(p.final_amount)
                                                          FROM payments p
                                                          WHERE p.student_id = s.id
                                                            AND p.enrollment_id = e.id
                                                            AND p.payment_type = 'monthly_fee'
                                                            AND p.payment_month = ?
                                                            AND p.status = 'completed'), 0), 0) as due_amount
                FROM students s
                INNER JOIN enrollments e ON s.id = e.student_id
                INNER JOIN batches b ON e.batch_id = b.id
                INNER JOIN courses c ON b.course_id = c.id
                WHERE s.id = ? AND e.status = 'active'
                ORDER BY e.enrolled_date DESC";

        return $this->db->query($sql)
            ->bind(1, $paymentMonth)
            ->bind(2, $paymentMonth)
            ->bind(3, $studentId)
            ->fetchAll();
    }

    // Preserve the single-course API used by the demo checkout.
    public function getMonthlyDueByStudent($studentId, $paymentMonth = null) {
        $dues = $this->getMonthlyDuesByStudent($studentId, $paymentMonth);
        return $dues[0] ?? null;
    }
    
    // Get payment summary
    public function getSummary($filters = []) {
        $sql = "SELECT 
                    COUNT(*) as total_payments,
                    SUM(CASE WHEN status = 'completed' THEN final_amount ELSE 0 END) as total_collected,
                    SUM(CASE WHEN status = 'pending' THEN final_amount ELSE 0 END) as total_pending,
                    SUM(final_amount) as total_amount,
                    payment_type
                FROM payments 
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND payment_date >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND payment_date <= ?";
            $params[] = $filters['date_to'];
        }
        
        $sql .= " GROUP BY payment_type";
        
        $this->db->query($sql);
        foreach ($params as $i => $param) {
            $this->db->bind($i + 1, $param);
        }
        
        return $this->db->fetchAll();
    }
}
