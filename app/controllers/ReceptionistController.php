<?php
/**
 * Royal Education Center Management System
 * Receptionist Controller
 */

require_once APP_PATH . '/models/Student.php';
require_once APP_PATH . '/models/Enrollment.php';
require_once APP_PATH . '/models/Inquiry.php';
require_once APP_PATH . '/models/Auth.php';

class ReceptionistController extends Controller {
    private $studentModel;
    private $enrollmentModel;
    private $inquiryModel;
    
    public function __construct() {
        parent::__construct();
        $this->requireRole('receptionist');
        
        $this->studentModel = new Student();
        $this->enrollmentModel = new Enrollment();
        $this->inquiryModel = new Inquiry();
    }
    
    // Receptionist Dashboard
    public function dashboard() {
        $db = Database::getInstance();
        
        $stats = [
            'total_students' => $db->query("SELECT COUNT(*) FROM students")->fetchColumn(),
            'active_enrollments' => $db->query("SELECT COUNT(*) FROM enrollments WHERE status = 'active'")->fetchColumn(),
            'pending_inquiries' => $db->query("SELECT COUNT(*) FROM inquiries WHERE status = 'new'")->fetchColumn(),
            'today_registrations' => $db->query("SELECT COUNT(*) FROM students WHERE DATE(created_at) = CURRENT_DATE")->fetchColumn()
        ];
        
        $this->layout('main', 'receptionist.dashboard', [
            'pageTitle' => 'Receptionist Dashboard',
            'stats' => $stats
        ]);
    }
    
    // Register Student
    public function register() {
        if (isPost()) {
            $this->handleRegistration();
        }
        $db = Database::getInstance();
        $batches = $db->query("SELECT b.id, b.batch_name, c.name as course_name
                               FROM batches b
                               INNER JOIN courses c ON b.course_id = c.id
                               WHERE b.status = 'active'
                               ORDER BY c.name, b.batch_name")->fetchAll();
        $this->layout('main', 'receptionist.register', [
            'pageTitle' => 'Register Student',
            'batches' => $batches
        ]);
    }
    
    private function handleRegistration() {
        $batchId = (int) $this->post('batch_id', 0);
        $userData = [
            'username' => trim($this->post('username')),
            'email' => strtolower(trim($this->post('email'))),
            'password' => $this->post('password'),
            'phone' => $this->post('phone')
        ];
        
        $studentData = [
            'first_name' => $this->post('first_name'),
            'last_name' => $this->post('last_name'),
            'gender' => $this->post('gender'),
            'date_of_birth' => $this->post('date_of_birth'),
            'address' => $this->post('address'),
            'guardian_name' => $this->post('guardian_name'),
            'guardian_phone' => $this->post('guardian_phone'),
            'school' => $this->post('school')
        ];

        $errors = $this->validate($userData, [
            'username' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if (!empty($errors)) {
            setFlash('error', 'Please enter a valid username, email, and password.');
            $this->back();
        }

        if ($batchId <= 0) {
            setFlash('error', 'Please select a class/course for the student.');
            $this->back();
        }

        $authModel = new Auth();
        if ($authModel->usernameExists($userData['username'])) {
            setFlash('error', 'Username already exists. Please choose a different username.');
            $this->back();
        }

        if ($authModel->emailExists($userData['email'])) {
            setFlash('error', 'This email address is already registered. Please use a different email address.');
            $this->back();
        }
        
        $result = $this->studentModel->createWithUser($userData, $studentData);
        
        if ($result['success']) {
            $enrollment = $this->enrollmentModel->enrollStudent($result['student_id'], $batchId);
            if (!$enrollment['success']) {
                setFlash('error', 'Student was registered, but class enrollment failed: ' . $enrollment['error']);
                $this->redirect(BASE_URL . '/receptionist/students');
            }
            logActivity(currentUserId(), 'create', 'students', "Registered student: {$result['student_code']}");
            setFlash('success', 'Student registered and enrolled successfully. Code: ' . $result['student_code']);
            $this->redirect(BASE_URL . '/receptionist/students');
        } else {
            setFlash('error', $result['error'] ?? 'Registration failed');
            $this->back();
        }
    }
    
    // Students List
    public function students() {
        $students = $this->studentModel->getAllWithUser();
        $this->layout('main', 'receptionist.students', [
            'pageTitle' => 'Student Records',
            'students' => $students
        ]);
    }
    
    // Enroll Student
    public function enroll() {
        $db = Database::getInstance();
        $students = $this->studentModel->getAllWithUser();
        $batches = $db->query("SELECT b.*, c.name as course_name FROM batches b INNER JOIN courses c ON b.course_id = c.id WHERE b.status = 'active'")->fetchAll();
        
        if (isPost()) {
            $studentId = $this->post('student_id');
            $batchId = $this->post('batch_id');
            
            $result = $this->enrollmentModel->enrollStudent($studentId, $batchId);
            
            if ($result['success']) {
                logActivity(currentUserId(), 'create', 'enrollments', "Enrolled student $studentId in batch $batchId");
                setFlash('success', 'Student enrolled successfully');
                $this->redirect(BASE_URL . '/receptionist/enroll');
            } else {
                setFlash('error', $result['error']);
                $this->back();
            }
        }
        
        $this->layout('main', 'receptionist.enroll', [
            'pageTitle' => 'Enroll Student',
            'students' => $students,
            'batches' => $batches
        ]);
    }
    
    // Inquiries
    public function inquiries() {
        if (isPost()) {
            $data = [
                'name' => $this->post('name'),
                'phone' => $this->post('phone'),
                'email' => $this->post('email'),
                'subject' => $this->post('subject'),
                'message' => $this->post('message'),
                'status' => 'new'
            ];
            $id = $this->inquiryModel->create($data);
            if ($id) {
                logActivity(currentUserId(), 'create', 'inquiries', "Created inquiry from: {$data['name']}");
                setFlash('success', 'Inquiry recorded successfully');
            } else {
                setFlash('error', 'Failed to record inquiry');
            }
            $this->redirect(BASE_URL . '/receptionist/inquiries');
        }
        
        $inquiries = $this->inquiryModel->getAllWithDetails();
        $db = Database::getInstance();
        $courses = $db->query("SELECT name FROM courses WHERE status = 'active' ORDER BY name")->fetchAll();
        
        $this->layout('main', 'receptionist.inquiries', [
            'pageTitle' => 'Inquiries',
            'inquiries' => $inquiries,
            'courses' => $courses
        ]);
    }
    
    // Handle Inquiry
    public function handleInquiry($id) {
        if ($this->inquiryModel->markHandled($id, currentUserId(), $this->post('notes', ''))) {
            logActivity(currentUserId(), 'update', 'inquiries', "Handled inquiry ID: $id");
            setFlash('success', 'Inquiry marked as handled');
        } else {
            setFlash('error', 'Failed to update inquiry');
        }
        $this->redirect(BASE_URL . '/receptionist/inquiries');
    }
}
