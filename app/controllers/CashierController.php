<?php
/**
 * Royal Education Center Management System
 * Cashier Controller
 */

require_once APP_PATH . '/models/Student.php';
require_once APP_PATH . '/models/Payment.php';

class CashierController extends Controller {
    private $studentModel;
    private $paymentModel;
    
    public function __construct() {
        parent::__construct();
        $this->requireRole('cashier');
        
        $this->studentModel = new Student();
        $this->paymentModel = new Payment();
    }
    
    // Cashier Dashboard
    public function dashboard() {
        $db = Database::getInstance();
        
        $stats = [
            'today_collection' => $db->query("SELECT COALESCE(SUM(final_amount), 0) FROM payments WHERE status = 'completed' AND payment_date = CURRENT_DATE")->fetchColumn(),
            'month_collection' => $db->query("SELECT COALESCE(SUM(final_amount), 0) FROM payments WHERE status = 'completed' AND MONTH(payment_date) = MONTH(CURRENT_DATE) AND YEAR(payment_date) = YEAR(CURRENT_DATE)")->fetchColumn(),
            'due_payments' => $db->query("SELECT COUNT(*) FROM payments WHERE status = 'pending'")->fetchColumn(),
            'total_transactions' => $db->query("SELECT COUNT(*) FROM payments")->fetchColumn()
        ];
        
        $recentPayments = $db->query("SELECT p.*, s.student_code, s.first_name, s.last_name, r.receipt_no 
                                      FROM payments p 
                                      INNER JOIN students s ON p.student_id = s.id 
                                      LEFT JOIN receipts r ON p.id = r.payment_id 
                                      ORDER BY p.payment_date DESC LIMIT 5")->fetchAll();
        
        $this->layout('main', 'cashier.dashboard', [
            'pageTitle' => 'Cashier Dashboard',
            'stats' => $stats,
            'recentPayments' => $recentPayments
        ]);
    }
    
    // Collect Payment
    public function collect() {
        $students = $this->studentModel->getAllWithUser();
        
        if (isPost()) {
            $this->handlePayment();
        }
        
        $this->layout('main', 'cashier.collect', [
            'pageTitle' => 'Collect Payment',
            'students' => $students
        ]);
    }
    
    private function handlePayment() {
        $db = Database::getInstance();

        if (!verifyCsrfToken($this->post('csrf_token'))) {
            setFlash('error', 'Invalid payment request. Please try again.');
            $this->redirect(BASE_URL . '/cashier/collect');
        }

        $amount = (float) $this->post('amount', 0);
        $discount = (float) $this->post('discount', 0);
        $paymentMethod = $this->post('payment_method');
        if ($amount <= 0 || $discount < 0 || $discount > $amount || !in_array($paymentMethod, ['cash', 'online', 'bank_transfer', 'card'], true)) {
            setFlash('error', 'Please enter a valid amount, discount, and payment method.');
            $this->redirect(BASE_URL . '/cashier/collect');
        }
        
        $data = [
            'student_id' => $this->post('student_id'),
            'payment_type' => $this->post('payment_type'),
            'amount' => $amount,
            'discount' => $discount,
            'final_amount' => $amount - $discount,
            'payment_method' => $paymentMethod,
            'payment_date' => date(DATE_FORMAT),
            'status' => 'completed',
            'collected_by' => currentUserId()
        ];
        
        $sql = "INSERT INTO payments (student_id, payment_type, amount, discount, final_amount, payment_method, payment_date, status, collected_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $db->query($sql);
        $db->bind(1, $data['student_id']);
        $db->bind(2, $data['payment_type']);
        $db->bind(3, $data['amount']);
        $db->bind(4, $data['discount']);
        $db->bind(5, $data['final_amount']);
        $db->bind(6, $data['payment_method']);
        $db->bind(7, $data['payment_date']);
        $db->bind(8, $data['status']);
        $db->bind(9, $data['collected_by']);
        
        if ($db->execute()) {
            $paymentId = $db->lastInsertId();
            
            // Generate receipt
            $receiptNo = 'RCP-' . date('Y') . '-' . str_pad($paymentId, 5, '0', STR_PAD_LEFT);
            $sql = "INSERT INTO receipts (receipt_no, payment_id, student_id, amount, issued_by) 
                    VALUES (?, ?, ?, ?, ?)";
            $db->query($sql);
            $db->bind(1, $receiptNo);
            $db->bind(2, $paymentId);
            $db->bind(3, $data['student_id']);
            $db->bind(4, $data['final_amount']);
            $db->bind(5, currentUserId());
            $db->execute();
            
            logActivity(currentUserId(), 'create', 'payments', "Collected payment: $receiptNo");
            setFlash('success', "Payment collected successfully. Receipt: $receiptNo");
            $this->redirect(BASE_URL . '/cashier/collect');
        } else {
            setFlash('error', 'Failed to collect payment');
            $this->back();
        }
    }
    
    // All Payments
    public function payments() {
        $payments = $this->paymentModel->getAllWithDetails();
        
        $this->layout('main', 'cashier.payments', [
            'pageTitle' => 'All Payments',
            'payments' => $payments
        ]);
    }

    public function approvePayment($paymentId) {
        if (!isPost() || !verifyCsrfToken($this->post('csrf_token'))) {
            setFlash('error', 'Invalid payment verification request.');
            $this->redirect(BASE_URL . '/cashier/payments');
        }

        $paymentId = (int) $paymentId;
        $db = Database::getInstance();

        $payment = $db->query("SELECT * FROM payments WHERE id = ?")->bind(1, $paymentId)->fetch();
        if (!$payment || $payment['status'] !== 'pending') {
            setFlash('error', 'Payment not found.');
            $this->redirect(BASE_URL . '/cashier/payments');
        }

        $db->query("UPDATE payments SET status = 'completed', collected_by = ? WHERE id = ?")
           ->bind(1, currentUserId())
           ->bind(2, $paymentId)
           ->execute();

        $receiptNo = 'RCP-' . date('Y') . '-' . str_pad($paymentId, 5, '0', STR_PAD_LEFT);
        $receiptExists = $db->query("SELECT id FROM receipts WHERE payment_id = ?")->bind(1, $paymentId)->fetch();

        if (!$receiptExists) {
            $filePath = null;
            if (!empty($payment['remarks'])) {
                preg_match('/Slip: ([^|]+)/', $payment['remarks'], $matches);
                if (!empty($matches[1])) {
                    $filePath = trim($matches[1]);
                }
            }

            $db->query("INSERT INTO receipts (receipt_no, payment_id, student_id, amount, issued_by, file_path)
                        VALUES (?, ?, ?, ?, ?, ?)")
               ->bind(1, $receiptNo)
               ->bind(2, $paymentId)
               ->bind(3, $payment['student_id'])
               ->bind(4, $payment['final_amount'])
               ->bind(5, currentUserId())
               ->bind(6, $filePath)
               ->execute();
        }

        setFlash('success', 'Transfer payment approved and receipt generated.');
        $this->redirect(BASE_URL . '/cashier/payments');
    }

    public function rejectPayment($paymentId) {
        if (!isPost() || !verifyCsrfToken($this->post('csrf_token'))) {
            setFlash('error', 'Invalid payment rejection request.');
            $this->redirect(BASE_URL . '/cashier/payments');
        }

        $paymentId = (int) $paymentId;
        $db = Database::getInstance();

        $db->query("UPDATE payments SET status = 'failed', collected_by = ? WHERE id = ? AND status = 'pending'")
           ->bind(1, currentUserId())
           ->bind(2, $paymentId)
           ->execute();

        setFlash('warning', 'Transfer payment was rejected.');
        $this->redirect(BASE_URL . '/cashier/payments');
    }
    
    // Due Payments
    public function due() {
        $duePayments = $this->paymentModel->getDuePayments();
        
        $this->layout('main', 'cashier.due', [
            'pageTitle' => 'Due Payments',
            'duePayments' => $duePayments
        ]);
    }
    
    // Financial Reports
    public function reports() {
        $db = Database::getInstance();
        
        $summary = $this->paymentModel->getSummary();
        
        $dailyCollection = $db->query("SELECT DATE(payment_date) as date, 
                                              COUNT(*) as count, 
                                              SUM(final_amount) as total 
                                       FROM payments WHERE status = 'completed' 
                                       AND payment_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) 
                                       GROUP BY DATE(payment_date) 
                                       ORDER BY date DESC")->fetchAll();
        
        $monthlyCollection = $db->query("SELECT DATE_FORMAT(payment_date, '%Y-%m') as month, 
                                               COUNT(*) as count, 
                                               SUM(final_amount) as total 
                                        FROM payments WHERE status = 'completed' 
                                        GROUP BY month ORDER BY month DESC LIMIT 12")->fetchAll();
        
        $paymentMethods = $db->query("SELECT payment_method, 
                                             COUNT(*) as count, 
                                             SUM(final_amount) as total 
                                      FROM payments WHERE status = 'completed' 
                                      GROUP BY payment_method")->fetchAll();
        
        $this->layout('main', 'cashier.reports', [
            'pageTitle' => 'Financial Reports',
            'summary' => $summary,
            'dailyCollection' => $dailyCollection,
            'monthlyCollection' => $monthlyCollection,
            'paymentMethods' => $paymentMethods
        ]);
    }
}
