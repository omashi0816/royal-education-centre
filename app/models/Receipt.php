<?php
class Receipt extends Model {
    protected $table = 'receipts';
    protected $primaryKey = 'id';
    
    public function getWithDetails($id) {
        $sql = "SELECT r.*, p.payment_type, p.final_amount, p.payment_method, p.payment_date, p.status as payment_status,
                       s.student_code, s.first_name, s.last_name,
                       u.email, u.phone,
                       issuer.full_name as issued_by_name 
                FROM receipts r 
                INNER JOIN payments p ON r.payment_id = p.id 
                INNER JOIN students s ON r.student_id = s.id 
                INNER JOIN users u ON s.user_id = u.id 
                LEFT JOIN users issuer ON r.issued_by = issuer.id 
                WHERE r.id = ?";
        return $this->db->query($sql)->bind(1, $id)->fetch();
    }
    
    public function getByPayment($paymentId) {
        return $this->where('payment_id', $paymentId)[0] ?? null;
    }
    
    public function getByStudent($studentId) {
        $sql = "SELECT r.*, p.payment_type, p.final_amount, p.payment_date, p.status as payment_status 
                FROM receipts r 
                INNER JOIN payments p ON r.payment_id = p.id 
                WHERE r.student_id = ? 
                ORDER BY r.created_at DESC";
        return $this->db->query($sql)->bind(1, $studentId)->fetchAll();
    }
    
    public function generateReceiptNo($paymentId) {
        return 'RCP-' . date('Y') . '-' . str_pad($paymentId, 5, '0', STR_PAD_LEFT);
    }
}
