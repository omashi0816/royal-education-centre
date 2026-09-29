<?php
/**
 * Royal Education Center Management System
 * Manager Controller
 */

require_once APP_PATH . '/models/Student.php';
require_once APP_PATH . '/models/Teacher.php';
require_once APP_PATH . '/models/Course.php';
require_once APP_PATH . '/models/Batch.php';
require_once APP_PATH . '/models/Payment.php';
require_once APP_PATH . '/models/Timetable.php';
require_once APP_PATH . '/models/User.php';
require_once APP_PATH . '/models/Auth.php';

class ManagerController extends Controller {
    private $studentModel;
    private $teacherModel;
    private $courseModel;
    private $batchModel;
    private $paymentModel;
    private $timetableModel;
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->requireRole('manager');
        
        $this->studentModel = new Student();
        $this->teacherModel = new Teacher();
        $this->courseModel = new Course();
        $this->batchModel = new Batch();
        $this->paymentModel = new Payment();
        $this->timetableModel = new Timetable();
        $this->userModel = new User();
    }
    
    // Manager Dashboard
    public function dashboard() {
        $db = Database::getInstance();
        
        $stats = [
            'total_students' => $db->query("SELECT COUNT(*) FROM students")->fetchColumn(),
            'total_teachers' => $db->query("SELECT COUNT(*) FROM teachers")->fetchColumn(),
            'active_courses' => $db->query("SELECT COUNT(*) FROM courses WHERE status IN ('approved', 'active')")->fetchColumn(),
            'active_batches' => $db->query("SELECT COUNT(*) FROM batches WHERE status = 'active'")->fetchColumn(),
            'pending_approvals' => $db->query("SELECT COUNT(*) FROM courses WHERE status = 'pending'")->fetchColumn() + 
                                     $db->query("SELECT COUNT(*) FROM batches WHERE status = 'pending'")->fetchColumn(),
            'monthly_revenue' => $db->query("SELECT COALESCE(SUM(final_amount), 0) FROM payments WHERE status = 'completed' AND MONTH(payment_date) = MONTH(CURRENT_DATE) AND YEAR(payment_date) = YEAR(CURRENT_DATE)")->fetchColumn()
        ];
        
        $recentEnrollments = $db->query("SELECT e.*, s.student_code, s.first_name, s.last_name, b.batch_name 
                                        FROM enrollments e 
                                        INNER JOIN students s ON e.student_id = s.id 
                                        INNER JOIN batches b ON e.batch_id = b.id 
                                        ORDER BY e.enrolled_date DESC LIMIT 5")->fetchAll();
        
        $this->layout('main', 'manager.dashboard', [
            'pageTitle' => 'Manager Dashboard',
            'stats' => $stats,
            'recentEnrollments' => $recentEnrollments
        ]);
    }
    
    // Students Management (view only)
    public function students() {
        $students = $this->studentModel->getAllWithUser();
        $this->layout('main', 'manager.students', [
            'pageTitle' => 'Students',
            'students' => $students
        ]);
    }
    
    // Teachers Management (view only)
    public function teachers() {
        $teachers = $this->teacherModel->getAllWithUser();
        $this->layout('main', 'manager.teachers', [
            'pageTitle' => 'Teachers',
            'teachers' => $teachers
        ]);
    }

    // Operational user management. Managers cannot create or modify admin, manager, or student accounts.
    public function users() {
        $role = $this->get('role', '');
        $search = trim($this->get('search', ''));
        $allowedRoles = ['teacher', 'receptionist', 'cashier'];
        $role = in_array($role, $allowedRoles, true) ? $role : '';

        $db = Database::getInstance();
        $conditions = ['r.name IN (?, ?, ?)'];
        $params = $allowedRoles;
        if ($role) {
            $conditions[] = 'r.name = ?';
            $params[] = $role;
        }
        if ($search !== '') {
            $conditions[] = '(u.username LIKE ? OR u.email LIKE ? OR u.full_name LIKE ?)';
            $term = '%' . $search . '%';
            array_push($params, $term, $term, $term);
        }

        $query = $db->query('SELECT u.*, r.name AS role_name, r.display_name AS role_display_name
                             FROM users u INNER JOIN roles r ON u.role_id = r.id
                             WHERE ' . implode(' AND ', $conditions) . ' ORDER BY u.id DESC');
        foreach ($params as $index => $param) {
            $query->bind($index + 1, $param);
        }

        $this->layout('main', 'manager.users', [
            'pageTitle' => 'Staff Users',
            'users' => $query->fetchAll(),
            'role' => $role,
            'search' => $search
        ]);
    }

    public function createUser() {
        $roles = $this->managedRoles();
        if (isPost()) {
            $this->saveManagedUser(null, $roles);
        }
        $this->layout('main', 'manager.user-form', [
            'pageTitle' => 'Add Staff User',
            'roles' => $roles
        ]);
    }

    public function editUser($id) {
        $user = $this->userModel->getUserWithRole($id);
        if (!$user || !$this->isManagedRole($user['role_name'])) {
            setFlash('error', 'Staff user not found.');
            $this->redirect(BASE_URL . '/manager/users');
        }
        if (isPost()) {
            $this->saveManagedUser($id, $this->managedRoles());
        }
        $this->layout('main', 'manager.user-form', [
            'pageTitle' => 'Edit Staff User',
            'roles' => $this->managedRoles(),
            'user' => $user
        ]);
    }

    public function deleteUser($id) {
        if (!isPost() || !verifyCsrfToken($this->post('csrf_token'))) {
            setFlash('error', 'Invalid request.');
            $this->redirect(BASE_URL . '/manager/users');
        }
        $user = $this->userModel->getUserWithRole($id);
        if (!$user || !$this->isManagedRole($user['role_name']) || (int) $id === (int) currentUserId()) {
            setFlash('error', 'This staff user cannot be deleted.');
            $this->redirect(BASE_URL . '/manager/users');
        }
        if ($this->userModel->delete($id)) {
            logActivity(currentUserId(), 'delete', 'users', "Deleted staff user ID: $id");
            setFlash('success', 'Staff user deleted successfully.');
        } else {
            setFlash('error', 'Failed to delete staff user.');
        }
        $this->redirect(BASE_URL . '/manager/users');
    }

    private function managedRoles() {
        $db = Database::getInstance();
        return $db->query("SELECT * FROM roles WHERE name IN ('teacher', 'receptionist', 'cashier') ORDER BY FIELD(name, 'teacher', 'receptionist', 'cashier')")->fetchAll();
    }

    private function isManagedRole($roleName) {
        return in_array($roleName, ['teacher', 'receptionist', 'cashier'], true);
    }

    private function saveManagedUser($id, $roles) {
        if (!verifyCsrfToken($this->post('csrf_token'))) {
            setFlash('error', 'Invalid request. Please try again.');
            $this->back();
        }

        $roleId = (int) $this->post('role_id', 0);
        $role = null;
        foreach ($roles as $candidate) {
            if ((int) $candidate['id'] === $roleId) {
                $role = $candidate;
                break;
            }
        }
        $data = [
            'role_id' => $roleId,
            'username' => trim($this->post('username', '')),
            'email' => trim($this->post('email', '')),
            'password' => $this->post('password', ''),
            'full_name' => trim($this->post('full_name', '')),
            'phone' => trim($this->post('phone', '')),
            'status' => $this->post('status', 'active')
        ];

        $rules = ['email' => 'required|email', 'full_name' => 'required'];
        if ($id === null) {
            $rules['username'] = 'required|min:3';
            $rules['password'] = 'required|min:6';
        }
        if (!$role || !empty($this->validate($data, $rules))) {
            setFlash('error', 'Please provide valid staff account details.');
            $this->back();
        }

        if ($id !== null) {
            $existingUser = $this->userModel->getUserWithRole($id);
            if (!$existingUser || (int) $existingUser['role_id'] !== $roleId) {
                setFlash('error', 'A staff user role cannot be changed after creation.');
                $this->back();
            }
        }

        $authModel = new Auth();
        if ($authModel->emailExists($data['email'], $id) || ($id === null && $authModel->usernameExists($data['username']))) {
            setFlash('error', 'The username or email is already in use.');
            $this->back();
        }

        if ($id === null) {
            $userId = $authModel->createUserWithProfile($data, $role['name']);
            if (!$userId) {
                setFlash('error', 'Failed to create staff user.');
                $this->back();
            }
            logActivity(currentUserId(), 'create', 'users', "Created staff user: {$data['username']}");
            setFlash('success', 'Staff user created successfully.');
        } else {
            $updated = $this->userModel->update($id, [
                'role_id' => $role['id'],
                'email' => $data['email'],
                'full_name' => $data['full_name'],
                'phone' => $data['phone'],
                'status' => $data['status']
            ]);
            if (!$updated) {
                setFlash('error', 'Failed to update staff user.');
                $this->back();
            }
            logActivity(currentUserId(), 'update', 'users', "Updated staff user ID: $id");
            setFlash('success', 'Staff user updated successfully.');
        }
        $this->redirect(BASE_URL . '/manager/users');
    }
    
    // Approvals (courses and batches)
    public function approvals() {
        $db = Database::getInstance();
        
        $pendingCourses = $db->query("SELECT * FROM courses WHERE status = 'pending'")->fetchAll();
        $pendingBatches = $db->query("SELECT b.*, c.name as course_name FROM batches b INNER JOIN courses c ON b.course_id = c.id WHERE b.status = 'pending'")->fetchAll();
        
        $this->layout('main', 'manager.approvals', [
            'pageTitle' => 'Approvals',
            'pendingCourses' => $pendingCourses,
            'pendingBatches' => $pendingBatches
        ]);
    }
    
    // Approve Course
    public function approveCourse($id) {
        if (!isPost() || !verifyCsrfToken($this->post('csrf_token'))) {
            setFlash('error', 'Invalid approval request.');
            $this->redirect(BASE_URL . '/manager/approvals');
        }
        $db = Database::getInstance();
        $db->query("UPDATE courses SET status = 'approved' WHERE id = ?")->bind(1, $id)->execute();
        logActivity(currentUserId(), 'approve', 'courses', "Approved course ID: $id");
        setFlash('success', 'Course approved');
        $this->back();
    }
    
    // Reject Course
    public function rejectCourse($id) {
        if (!isPost() || !verifyCsrfToken($this->post('csrf_token'))) {
            setFlash('error', 'Invalid rejection request.');
            $this->redirect(BASE_URL . '/manager/approvals');
        }
        $db = Database::getInstance();
        $db->query("UPDATE courses SET status = 'cancelled' WHERE id = ?")->bind(1, $id)->execute();
        logActivity(currentUserId(), 'reject', 'courses', "Rejected course ID: $id");
        setFlash('success', 'Course rejected');
        $this->back();
    }
    
    // Approve Batch
    public function approveBatch($id) {
        if (!isPost() || !verifyCsrfToken($this->post('csrf_token'))) {
            setFlash('error', 'Invalid approval request.');
            $this->redirect(BASE_URL . '/manager/approvals');
        }
        $db = Database::getInstance();
        $db->query("UPDATE batches SET status = 'active' WHERE id = ?")->bind(1, $id)->execute();
        logActivity(currentUserId(), 'approve', 'batches', "Approved batch ID: $id");
        setFlash('success', 'Batch approved');
        $this->back();
    }
    
    // Reject Batch
    public function rejectBatch($id) {
        if (!isPost() || !verifyCsrfToken($this->post('csrf_token'))) {
            setFlash('error', 'Invalid rejection request.');
            $this->redirect(BASE_URL . '/manager/approvals');
        }
        $db = Database::getInstance();
        $db->query("UPDATE batches SET status = 'cancelled' WHERE id = ?")->bind(1, $id)->execute();
        logActivity(currentUserId(), 'reject', 'batches', "Rejected batch ID: $id");
        setFlash('success', 'Batch rejected');
        $this->back();
    }
    
    // Timetables (view only)
    public function timetables() {
        $db = Database::getInstance();
        $batchId = $this->get('batch_id', '');
        
        if ($batchId) {
            $slots = $this->timetableModel->getByBatch($batchId);
        } else {
                 $slots = $db->query("SELECT ts.*, b.batch_name, c.name as course_name, c.code as course_code, s.name as subject_name, 
                                        t.first_name, t.last_name 
                                 FROM timetable_slots ts 
                                 INNER JOIN batches b ON ts.batch_id = b.id 
                             INNER JOIN courses c ON b.course_id = c.id
                                 INNER JOIN subjects s ON ts.subject_id = s.id 
                                 INNER JOIN teachers t ON ts.teacher_id = t.id 
                                 ORDER BY ts.day_of_week, ts.start_time")->fetchAll();
        }
        
        $batches = $db->query("SELECT b.*, c.name as course_name FROM batches b INNER JOIN courses c ON b.course_id = c.id WHERE b.status = 'active' ORDER BY b.batch_name")->fetchAll();
        
        $this->layout('main', 'manager.timetables', [
            'pageTitle' => 'Timetables',
            'slots' => $slots,
            'batches' => $batches,
            'selectedBatch' => $batchId
        ]);
    }
    
    // Reports
    public function reports() {
        $db = Database::getInstance();
        
        $reportData = [
            'students_by_course' => $db->query("SELECT c.name, COUNT(e.id) as count 
                                                FROM courses c 
                                                LEFT JOIN batches b ON c.id = b.course_id 
                                                LEFT JOIN enrollments e ON b.id = e.batch_id 
                                                GROUP BY c.id ORDER BY count DESC")->fetchAll(),
            'revenue_by_month' => $db->query("SELECT DATE_FORMAT(payment_date, '%Y-%m') as month, 
                                                    SUM(final_amount) as total 
                                             FROM payments WHERE status = 'completed' 
                                             GROUP BY month ORDER BY month DESC LIMIT 12")->fetchAll(),
            'batch_enrollment' => $db->query("SELECT b.batch_name, c.name as course_name, 
                                                     COUNT(e.id) as enrolled 
                                              FROM batches b 
                                              INNER JOIN courses c ON b.course_id = c.id 
                                              LEFT JOIN enrollments e ON b.id = e.batch_id 
                                              GROUP BY b.id ORDER BY enrolled DESC")->fetchAll()
        ];
        
        $this->layout('main', 'manager.reports', [
            'pageTitle' => 'Reports',
            'reportData' => $reportData
        ]);
    }
}
