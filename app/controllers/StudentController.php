<?php
/**
 * Royal Education Center Management System
 * Student Controller
 */

require_once APP_PATH . '/models/Student.php';
require_once APP_PATH . '/models/Enrollment.php';
require_once APP_PATH . '/models/Assignment.php';
require_once APP_PATH . '/models/Exam.php';
require_once APP_PATH . '/models/Payment.php';
require_once APP_PATH . '/models/StudyMaterial.php';
require_once APP_PATH . '/models/OnlineClass.php';
require_once APP_PATH . '/models/Attendance.php';
require_once APP_PATH . '/models/Timetable.php';
require_once APP_PATH . '/services/SmsService.php';

class StudentController extends Controller {
    private $studentModel;
    private $enrollmentModel;
    private $assignmentModel;
    private $examModel;
    private $paymentModel;
    private $studyMaterialModel;
    private $onlineClassModel;
    private $attendanceModel;
    private $timetableModel;
    
    public function __construct() {
        parent::__construct();
        $this->requireRole('student');
        
        $this->studentModel = new Student();
        $this->enrollmentModel = new Enrollment();
        $this->assignmentModel = new Assignment();
        $this->examModel = new Exam();
        $this->paymentModel = new Payment();
        $this->studyMaterialModel = new StudyMaterial();
        $this->onlineClassModel = new OnlineClass();
        $this->attendanceModel = new Attendance();
        $this->timetableModel = new Timetable();
    }
    
    // Student Dashboard
    public function dashboard() {
        $studentId = $this->getStudentId();
        $student = $this->studentModel->getWithUser($studentId);
        
        $enrollments = $this->enrollmentModel->getByStudent($studentId);
        $availableCourses = $this->getAvailableCourses();
        $registeredBatchIds = array_map('intval', array_column($enrollments, 'batch_id'));
        $assignments = $this->assignmentModel->getByStudent($studentId);
        $exams = $this->examModel->getByStudent($studentId);
        $attendanceRecords = $this->attendanceModel->getByStudent($studentId, 5);
        $attendanceStats = $this->attendanceModel->getAttendanceStats($studentId);
        $attendanceTotal = (int) ($attendanceStats['total'] ?? 0);
        $attendancePercentage = $attendanceTotal > 0
            ? round(((int) ($attendanceStats['present'] ?? 0) / $attendanceTotal) * 100)
            : 0;
        
        $stats = [
            'enrolled_courses' => count($enrollments),
            'pending_assignments' => count(array_filter($assignments, fn($a) => $a['submission_status'] !== 'submitted')),
            'upcoming_exams' => count(array_filter($exams, fn($e) => $e['result_status'] === null)),
            'total_payments' => count($this->paymentModel->getByStudent($studentId)),
            'attendance_percentage' => $attendancePercentage
        ];
        
        $notices = $this->getNotices();
        
        $this->layout('main', 'student.dashboard', [
            'pageTitle' => 'Student Dashboard',
            'student' => $student,
            'stats' => $stats,
            'enrollments' => $enrollments,
            'availableCourses' => $availableCourses,
            'registeredBatchIds' => $registeredBatchIds,
            'assignments' => array_slice($assignments, 0, 5),
            'exams' => array_slice($exams, 0, 5),
            'attendanceRecords' => $attendanceRecords,
            'notices' => $notices
        ]);
    }
    
    // Get current student ID
    private function getStudentId() {
        $db = Database::getInstance();
        $sql = "SELECT id FROM students WHERE user_id = ?";
        return $db->query($sql)->bind(1, currentUserId())->fetchColumn();
    }
    
    // Get notices for student
    private function getNotices() {
        $db = Database::getInstance();
        $sql = "SELECT * FROM notices WHERE target_role = 'all' OR target_role = 'student' ORDER BY is_pinned DESC, created_at DESC LIMIT 5";
        return $db->query($sql)->fetchAll();
    }

    private function getAvailableCourses() {
        $db = Database::getInstance();
        $sql = "SELECT b.id as batch_id, b.batch_name, b.start_date, b.end_date,
                       c.name as course_name, c.code as course_code, c.monthly_fee
                FROM batches b
                INNER JOIN courses c ON b.course_id = c.id
                WHERE b.status = 'active' AND c.status IN ('approved', 'active')
                ORDER BY c.name, b.batch_name";
        return $db->query($sql)->fetchAll();
    }

    // Register the logged-in student in an available class/course
    public function registerCourse($batchId = null) {
        if (!isPost() || !verifyCsrfToken($this->post('csrf_token'))) {
            setFlash('error', 'Invalid course registration request.');
            $this->redirect(BASE_URL . '/student/dashboard');
        }

        $batchId = (int) ($batchId ?: $this->post('batch_id', 0));
        $db = Database::getInstance();
        $batchExists = $db->query("SELECT id FROM batches WHERE id = ? AND status = 'active'")
                          ->bind(1, $batchId)->fetch();
        if (!$batchExists) {
            setFlash('error', 'Selected class/course is not available.');
            $this->redirect(BASE_URL . '/student/dashboard');
        }

        $result = $this->enrollmentModel->enrollStudent($this->getStudentId(), $batchId);
        setFlash($result['success'] ? 'success' : 'error', $result['success'] ? 'Course registered successfully.' : $result['error']);
        $this->redirect(BASE_URL . '/student/dashboard');
    }
    
    // My Courses
    public function courses() {
        $studentId = $this->getStudentId();
        $enrollments = $this->enrollmentModel->getByStudent($studentId);
        
        $this->layout('main', 'student.courses', [
            'pageTitle' => 'My Courses',
            'enrollments' => $enrollments,
            'availableCourses' => $this->getAvailableCourses(),
            'registeredBatchIds' => array_map('intval', array_column($enrollments, 'batch_id'))
        ]);
    }
    
    // Timetable
    public function timetable() {
        $studentId = $this->getStudentId();
        $db = Database::getInstance();
        
        $batchRows = $db->query("SELECT batch_id FROM enrollments WHERE student_id = ? AND status = 'active'")
                       ->bind(1, $studentId)->fetchAll();
        $batchIds = array_column($batchRows, 'batch_id');
        
        $timetable = [];
        if (!empty($batchIds)) {
            $placeholders = implode(',', array_fill(0, count($batchIds), '?'));
            $sql = "SELECT ts.*, b.batch_name, s.name as subject_name,
                           t.first_name, t.last_name
                    FROM timetable_slots ts
                    INNER JOIN batches b ON ts.batch_id = b.id
                    INNER JOIN subjects s ON ts.subject_id = s.id
                    INNER JOIN teachers t ON ts.teacher_id = t.id
                    WHERE ts.batch_id IN ($placeholders)
                    ORDER BY FIELD(ts.day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday'), ts.start_time";
            $db->query($sql);
            foreach ($batchIds as $i => $bid) {
                $db->bind($i + 1, $bid);
            }
            $timetable = $db->fetchAll();
        }
        
        $this->layout('main', 'student.timetable', [
            'pageTitle' => 'My Timetable',
            'timetable' => $timetable
        ]);
    }
    
    // Online Classes
    public function classes() {
        $studentId = $this->getStudentId();
        $classes = $this->onlineClassModel->getByStudent($studentId);
        
        $this->layout('main', 'student.classes', [
            'pageTitle' => 'Online Classes',
            'classes' => $classes
        ]);
    }
    
    // Study Notes / Materials
    public function notes() {
        $studentId = $this->getStudentId();
        $materials = $this->studyMaterialModel->getByStudent($studentId);
        
        $this->layout('main', 'student.notes', [
            'pageTitle' => 'Study Notes',
            'materials' => $materials
        ]);
    }
    
    // Attendance
    public function attendance() {
        $studentId = $this->getStudentId();
        $records = $this->attendanceModel->getByStudent($studentId);
        $stats = $this->attendanceModel->getAttendanceStats($studentId);
        
        $this->layout('main', 'student.attendance', [
            'pageTitle' => 'My Attendance',
            'records' => $records,
            'stats' => $stats
        ]);
    }
    
    // Assignments
    public function assignments() {
        $studentId = $this->getStudentId();
        $assignments = $this->assignmentModel->getByStudent($studentId);
        
        $this->layout('main', 'student.assignments', [
            'pageTitle' => 'My Assignments',
            'assignments' => $assignments
        ]);
    }
    
    // Exams
    public function exams() {
        $studentId = $this->getStudentId();
        $exams = $this->examModel->getByStudent($studentId);
        
        $this->layout('main', 'student.exams', [
            'pageTitle' => 'My Exams',
            'exams' => $exams
        ]);
    }
    
    // Results
    public function results() {
        $studentId = $this->getStudentId();
        $exams = $this->examModel->getByStudent($studentId);
        $assignments = $this->assignmentModel->getByStudent($studentId);

        $this->layout('main', 'student.results', [
            'pageTitle' => 'My Results',
            'exams' => $exams,
            'assignments' => $assignments
        ]);
    }
    
    public function checkout() {
        $studentId = $this->getStudentId();

        if (isPost()) {
            if (!verifyCsrfToken($this->post('csrf_token'))) {
                setFlash('error', 'Invalid checkout request. Please try again.');
                $this->redirect(BASE_URL . '/student/checkout');
            }

            $amount = (float) $this->post('amount', 0);
            $paymentMethod = $this->post('payment_method', '');
            if ($amount <= 0 || !in_array($paymentMethod, ['card', 'online_banking'], true)) {
                setFlash('error', 'Please enter a valid amount and payment method.');
                $this->redirect(BASE_URL . '/student/checkout');
            }

            $enrollmentId = (int) $this->post('enrollment_id', 0);
            $duePayment = $this->paymentModel->getMonthlyDueByStudent($studentId);
            $enrollments = $this->enrollmentModel->getByStudent($studentId);
            $enrollmentIds = array_map('intval', array_column($enrollments, 'id'));
            $dueAmount = (float) ($duePayment['due_amount'] ?? 0);

            if (!in_array($enrollmentId, $enrollmentIds, true) || $amount > $dueAmount + 0.01) {
                setFlash('error', 'The payment amount cannot exceed your current balance.');
                $this->redirect(BASE_URL . '/student/checkout');
            }

            $transactionId = 'CHK-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(3)));
            $db = Database::getInstance();
            $sql = "INSERT INTO payments (student_id, enrollment_id, payment_type, amount, discount, final_amount,
                    payment_method, transaction_id, payment_date, payment_month, status, remarks, collected_by)
                    VALUES (?, ?, 'monthly_fee', ?, 0, ?, ?, ?, ?, ?, 'pending', ?, ?)";
            $db->query($sql)
                ->bind(1, $studentId)
                ->bind(2, $enrollmentId)
                ->bind(3, $amount)
                ->bind(4, $amount)
                ->bind(5, $paymentMethod === 'online_banking' ? 'bank_transfer' : 'card')
                ->bind(6, $transactionId)
                ->bind(7, date(DATE_FORMAT))
                ->bind(8, date('Y-m'))
                ->bind(9, 'Online checkout payment awaiting cashier verification.')
                ->bind(10, currentUserId());

            if (!$db->execute()) {
                setFlash('error', 'Unable to submit the payment. Please try again.');
                $this->redirect(BASE_URL . '/student/checkout');
            }

            setFlash('success', 'Payment submitted successfully and is awaiting cashier verification.');
            $this->redirect(BASE_URL . '/student/checkout');
        }

        $student = $this->studentModel->getWithUser($studentId);
        $enrollments = $this->enrollmentModel->getByStudent($studentId);
        $duePayment = $this->paymentModel->getMonthlyDueByStudent($studentId);

        $this->layout('main', 'student.checkout', [
            'pageTitle' => 'Checkout',
            'student' => $student,
            'currentEnrollment' => $enrollments[0] ?? null,
            'dueAmount' => (float) ($duePayment['due_amount'] ?? 0)
        ]);
    }

    // Payments
    public function payments() {
        $studentId = $this->getStudentId();

        if (isPost()) {
            if (!verifyCsrfToken($this->post('csrf_token'))) {
                setFlash('error', 'Invalid request. Please try again.');
                $this->redirect(BASE_URL . '/student/payments');
            }

            $paymentAction = $this->post('payment_action', 'submit_payment');
            if ($paymentAction === 'send_otp') {
                $phone = trim((string) $this->post('otp_phone', ''));
                if (!validatePhone($phone)) {
                    setFlash('error', 'Please enter a valid phone number.');
                    $this->redirect(BASE_URL . '/student/payments');
                }

                $otp = (string) random_int(100000, 999999);
                Session::set('payment_otp_phone', $phone);
                Session::set('payment_otp_hash', password_hash($otp, PASSWORD_DEFAULT));
                Session::set('payment_otp_expires', time() + OTP_EXPIRY_SECONDS);
                Session::set('payment_otp_attempts', 0);
                Session::remove('payment_otp_verified');

                $smsResult = SmsService::sendOtp($phone, $otp);
                if (!$smsResult['success']) {
                    Session::remove('payment_otp_hash');
                    Session::remove('payment_otp_expires');
                    setFlash('error', $smsResult['error']);
                } elseif (!empty($smsResult['demo'])) {
                    Session::set('payment_otp_demo_code', $otp);
                    setFlash('warning', 'Development demo mode: no SMS was sent. Use OTP code ' . $otp . ' to test verification.');
                } else {
                    Session::remove('payment_otp_demo_code');
                    setFlash('success', 'OTP sent to your phone number. It will expire in 5 minutes.');
                }
                $this->redirect(BASE_URL . '/student/payments');
            }

            if ($paymentAction === 'verify_otp') {
                $otp = trim((string) $this->post('otp_code', ''));
                $attempts = (int) Session::get('payment_otp_attempts', 0);
                $expires = (int) Session::get('payment_otp_expires', 0);
                $otpHash = Session::get('payment_otp_hash');

                if ($attempts >= 5 || !$otpHash || time() > $expires || !password_verify($otp, $otpHash)) {
                    Session::set('payment_otp_attempts', $attempts + 1);
                    setFlash('error', 'Invalid or expired OTP. Please request a new code.');
                } else {
                    Session::set('payment_otp_verified', true);
                    setFlash('success', 'Phone number verified successfully. You can now submit the payment.');
                }
                $this->redirect(BASE_URL . '/student/payments');
            }

            if (!Session::get('payment_otp_verified', false)) {
                setFlash('error', 'Please verify your phone number with OTP before submitting the payment.');
                $this->redirect(BASE_URL . '/student/payments');
            }

            $amount = (float) $this->post('amount', 0);
            $paymentType = $this->post('payment_type', 'monthly_fee');
            $enrollmentValue = trim((string) $this->post('enrollment_id', 'all'));
            $paymentMethod = $this->post('payment_method', 'online_banking');
            $bankName = trim((string) $this->post('bank_name', ''));
            $accountName = trim((string) $this->post('account_name', ''));
            $accountNumber = trim((string) $this->post('account_number', ''));
            $transferDate = trim((string) $this->post('transfer_date', ''));
            $transactionId = trim((string) $this->post('transaction_id', ''));
            $paymentPurpose = trim((string) $this->post('payment_purpose', ''));

            if (!in_array($paymentMethod, ['card', 'online_banking'], true)) {
                setFlash('error', 'Please select a valid payment method.');
                $this->redirect(BASE_URL . '/student/payments');
            }

            $monthlyDues = $this->paymentModel->getMonthlyDuesByStudent($studentId);
            $dueByEnrollment = [];
            foreach ($monthlyDues as $monthlyDue) {
                $dueByEnrollment[(int) $monthlyDue['enrollment_id']] = (float) $monthlyDue['due_amount'];
            }

            $enrollmentId = null;
            if ($enrollmentValue !== 'all') {
                $enrollmentId = (int) $enrollmentValue;
                if (!array_key_exists($enrollmentId, $dueByEnrollment)) {
                    setFlash('error', 'Please select one of your active courses.');
                    $this->redirect(BASE_URL . '/student/payments');
                }
                $maximumAmount = $dueByEnrollment[$enrollmentId];
            } else {
                $maximumAmount = array_sum($dueByEnrollment);
            }

            if ($paymentType !== 'monthly_fee' || $amount <= 0 || $amount > $maximumAmount + 0.01) {
                setFlash('error', 'The payment amount must be greater than zero and cannot exceed the selected course due.');
                $this->redirect(BASE_URL . '/student/payments');
            }

            if (empty($bankName) || empty($accountName) || empty($accountNumber) || empty($transferDate) || empty($transactionId) || empty($paymentPurpose)) {
                setFlash('error', 'All transfer details are required before submitting the payment request.');
                $this->redirect(BASE_URL . '/student/payments');
            }

            $db = Database::getInstance();
            $remarksText = 'Online payment request';
            $remarksText .= ' | Bank: ' . $bankName . ' | Acc Name: ' . $accountName . ' | Acc No: ' . $accountNumber . ' | Transfer Date: ' . $transferDate . ' | Purpose: ' . $paymentPurpose . ' | Ref: ' . $transactionId;

            $storedPaymentMethod = $paymentMethod === 'card' ? 'card' : 'bank_transfer';
            $allocations = [];
            if ($enrollmentId !== null) {
                $allocations[] = ['enrollment_id' => $enrollmentId, 'amount' => round($amount, 2)];
            } else {
                $remainingAmount = round($amount, 2);
                foreach ($monthlyDues as $monthlyDue) {
                    $courseDue = round((float) $monthlyDue['due_amount'], 2);
                    if ($courseDue <= 0 || $remainingAmount <= 0) {
                        continue;
                    }
                    $allocation = min($courseDue, $remainingAmount);
                    $allocations[] = ['enrollment_id' => (int) $monthlyDue['enrollment_id'], 'amount' => $allocation];
                    $remainingAmount = round($remainingAmount - $allocation, 2);
                }
            }

            $sql = "INSERT INTO payments (student_id, enrollment_id, payment_type, amount, discount, final_amount, payment_method, transaction_id, payment_date, payment_month, status, remarks, collected_by)
                    VALUES (?, ?, ?, ?, 0, ?, ?, ?, ?, ?, 'pending', ?, ?)";
            $db->beginTransaction();
            $saved = true;
            foreach ($allocations as $allocation) {
                $db->query($sql)
                    ->bind(1, $studentId)
                    ->bind(2, $allocation['enrollment_id'])
                    ->bind(3, $paymentType)
                    ->bind(4, $allocation['amount'])
                    ->bind(5, $allocation['amount'])
                    ->bind(6, $storedPaymentMethod)
                    ->bind(7, $transactionId)
                    ->bind(8, $transferDate)
                    ->bind(9, date('Y-m'))
                    ->bind(10, $remarksText)
                    ->bind(11, currentUserId());
                if (!$db->execute()) {
                    $saved = false;
                    break;
                }
            }

            if ($saved && $allocations) {
                $db->commit();
                Session::remove('payment_otp_phone');
                Session::remove('payment_otp_hash');
                Session::remove('payment_otp_expires');
                Session::remove('payment_otp_attempts');
                Session::remove('payment_otp_verified');
                setFlash('success', 'Your online transfer payment request has been submitted successfully and is pending cashier verification.');
                $this->redirect(BASE_URL . '/student/payments');
            }

            if ($db->getConnection()->inTransaction()) {
                $db->rollback();
            }

            setFlash('error', 'Failed to submit online payment request.');
            $this->redirect(BASE_URL . '/student/payments');
        }

        $student = $this->studentModel->getWithUser($studentId);
        $enrollments = $this->enrollmentModel->getByStudent($studentId);
        $monthlyDues = $this->paymentModel->getMonthlyDuesByStudent($studentId);
        $dueAmount = array_sum(array_column($monthlyDues, 'due_amount'));
        $payments = $this->paymentModel->getByStudent($studentId);
        $latestPayment = $payments[0] ?? null;
        $otpPhone = Session::get('payment_otp_phone', '');
        $otpVerified = Session::get('payment_otp_verified', false);
        $otpDemoCode = Session::get('payment_otp_demo_code', '');

        $this->layout('main', 'student.payments', [
            'pageTitle' => 'My Payments',
            'payments' => $payments,
            'student' => $student,
            'currentEnrollment' => $enrollments[0] ?? null,
            'monthlyDues' => $monthlyDues,
            'dueAmount' => $dueAmount,
            'latestPayment' => $latestPayment,
            'otpPhone' => $otpPhone,
            'otpVerified' => $otpVerified,
            'otpDemoCode' => $otpDemoCode
        ]);
    }
}
