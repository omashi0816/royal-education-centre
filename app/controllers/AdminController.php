<?php
/**
 * Royal Education Center Management System
 * Admin Controller - Complete with all modules
 */

require_once APP_PATH . '/models/User.php';
require_once APP_PATH . '/models/Student.php';
require_once APP_PATH . '/models/Teacher.php';
require_once APP_PATH . '/models/Staff.php';
require_once APP_PATH . '/models/Course.php';
require_once APP_PATH . '/models/Subject.php';
require_once APP_PATH . '/models/Batch.php';
require_once APP_PATH . '/models/Enrollment.php';
require_once APP_PATH . '/models/Timetable.php';
require_once APP_PATH . '/models/Assignment.php';
require_once APP_PATH . '/models/Exam.php';
require_once APP_PATH . '/models/Payment.php';
require_once APP_PATH . '/models/Notice.php';
require_once APP_PATH . '/models/Settings.php';
require_once APP_PATH . '/models/ActivityLog.php';
require_once APP_PATH . '/models/Attendance.php';
require_once APP_PATH . '/models/Auth.php';

class AdminController extends Controller {
    private $userModel;
    private $studentModel;
    private $teacherModel;
    private $staffModel;
    private $courseModel;
    private $subjectModel;
    private $batchModel;
    private $enrollmentModel;
    private $timetableModel;
    private $assignmentModel;
    private $examModel;
    private $paymentModel;
    private $noticeModel;
    private $settingsModel;
    private $activityLogModel;
    
    public function __construct() {
        parent::__construct();
        $this->requireRole('admin');
        
        $this->userModel = new User();
        $this->studentModel = new Student();
        $this->teacherModel = new Teacher();
        $this->staffModel = new Staff();
        $this->courseModel = new Course();
        $this->subjectModel = new Subject();
        $this->batchModel = new Batch();
        $this->enrollmentModel = new Enrollment();
        $this->timetableModel = new Timetable();
        $this->assignmentModel = new Assignment();
        $this->examModel = new Exam();
        $this->paymentModel = new Payment();
        $this->noticeModel = new Notice();
        $this->settingsModel = new Settings();
        $this->activityLogModel = new ActivityLog();
    }
    
    // Admin Dashboard
    public function dashboard() {
        $db = Database::getInstance();
        
        // Get statistics
        $stats = [
            'total_students' => $db->query("SELECT COUNT(*) FROM students")->fetchColumn(),
            'total_teachers' => $db->query("SELECT COUNT(*) FROM teachers")->fetchColumn(),
            'total_staff' => $db->query("SELECT COUNT(*) FROM staff")->fetchColumn(),
            'total_courses' => $db->query("SELECT COUNT(*) FROM courses")->fetchColumn(),
            'total_batches' => $db->query("SELECT COUNT(*) FROM batches WHERE status = 'active'")->fetchColumn(),
            'active_enrollments' => $db->query("SELECT COUNT(*) FROM enrollments WHERE status = 'active'")->fetchColumn(),
            'total_payments' => $db->query("SELECT COALESCE(SUM(final_amount), 0) FROM payments WHERE status = 'completed'")->fetchColumn(),
            'this_month_payments' => $db->query("SELECT COALESCE(SUM(final_amount), 0) FROM payments WHERE status = 'completed' AND MONTH(payment_date) = MONTH(CURRENT_DATE) AND YEAR(payment_date) = YEAR(CURRENT_DATE)")->fetchColumn()
        ];
        
        // Get recent activities
        $recentActivities = $db->query("SELECT al.*, u.username, u.full_name 
                                        FROM activity_logs al 
                                        LEFT JOIN users u ON al.user_id = u.id 
                                        ORDER BY al.created_at DESC LIMIT 10")->fetchAll();
        
        // Get recent payments
        $recentPayments = $db->query("SELECT p.*, s.student_code, s.first_name, s.last_name 
                                      FROM payments p 
                                      INNER JOIN students s ON p.student_id = s.id 
                                      ORDER BY p.payment_date DESC LIMIT 5")->fetchAll();
        
        // Get upcoming exams
        $upcomingExams = $db->query("SELECT e.*, b.batch_name, s.name as subject_name 
                                     FROM exams e 
                                     INNER JOIN batches b ON e.batch_id = b.id 
                                     INNER JOIN subjects s ON e.subject_id = s.id 
                                     WHERE e.exam_date > NOW() 
                                     ORDER BY e.exam_date ASC LIMIT 5")->fetchAll();
        
        // Get recent notices
        $recentNotices = $this->noticeModel->getRecent(5);
        
        $this->layout('main', 'admin.dashboard', [
            'pageTitle' => 'Admin Dashboard',
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'recentPayments' => $recentPayments,
            'upcomingExams' => $upcomingExams,
            'recentNotices' => $recentNotices
        ]);
    }

    // Student and teacher attendance
    public function attendance() {
        $db = Database::getInstance();
        $studentAttendance = $db->query("SELECT a.date, a.status,
                                                s.student_code, s.first_name, s.last_name,
                                                b.batch_name
                                         FROM attendance a
                                         INNER JOIN enrollments e ON a.enrollment_id = e.id
                                         INNER JOIN students s ON e.student_id = s.id
                                         INNER JOIN batches b ON e.batch_id = b.id
                                         ORDER BY a.date DESC, s.first_name, s.last_name")->fetchAll();
        $teacherAttendance = $db->query("SELECT ta.*, t.teacher_code, t.first_name, t.last_name
                                         FROM teacher_attendance ta
                                         INNER JOIN teachers t ON ta.teacher_id = t.id
                                         ORDER BY ta.date DESC, t.first_name, t.last_name")->fetchAll();

        $this->layout('main', 'admin.attendance', [
            'pageTitle' => 'Attendance Management',
            'students' => $studentAttendance,
            'teachers' => $teacherAttendance,
            'teacherList' => $this->teacherModel->getAllWithUser('active')
        ]);
    }

    public function saveTeacherAttendance() {
        if (!isPost() || !verifyCsrfToken($this->post('csrf_token'))) {
            setFlash('error', 'Invalid attendance request.');
            $this->redirect(BASE_URL . '/admin/attendance');
        }

        $teacherId = (int) $this->post('teacher_id', 0);
        $date = trim((string) $this->post('date', ''));
        $status = $this->post('status', 'present');
        if ($teacherId <= 0 || !DateTime::createFromFormat('Y-m-d', $date) || !in_array($status, ['present', 'absent', 'late', 'excused'], true)) {
            setFlash('error', 'Please provide valid teacher attendance details.');
            $this->redirect(BASE_URL . '/admin/attendance');
        }

        $db = Database::getInstance();
        $sql = "INSERT INTO teacher_attendance (teacher_id, date, status, marked_by)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE status = VALUES(status), marked_by = VALUES(marked_by)";
        $saved = $db->query($sql)->bind(1, $teacherId)->bind(2, $date)->bind(3, $status)->bind(4, currentUserId())->execute();
        setFlash($saved ? 'success' : 'error', $saved ? 'Teacher attendance saved successfully.' : 'Failed to save teacher attendance.');
        $this->redirect(BASE_URL . '/admin/attendance');
    }
    
    // Users Management
    public function users() {
        $page = $this->get('page', 1);
        $search = $this->get('search', '');
        $role = $this->get('role', '');
        
        $users = $this->userModel->getAllWithUser();
        
        if ($search) {
            $users = $this->userModel->search($search);
        }
        
        if ($role) {
            $users = $this->userModel->getByRole($role);
        }
        
        $this->layout('main', 'admin.users', [
            'pageTitle' => 'User Management',
            'users' => $users,
            'search' => $search,
            'role' => $role
        ]);
    }
    
    // Create User
    public function createUser() {
        if (isPost()) {
            $this->handleCreateUser();
        }
        
        $db = Database::getInstance();
        $roles = $db->query("SELECT * FROM roles ORDER BY id")->fetchAll();
        
        $this->layout('main', 'admin.user-form', [
            'pageTitle' => 'Create User',
            'roles' => $roles
        ]);
    }
    
    private function handleCreateUser() {
        if (!verifyCsrfToken($this->post('csrf_token'))) {
            setFlash('error', 'Invalid request. Please try again.');
            $this->redirect(BASE_URL . '/admin/create-user');
        }

        $data = [
            'role_id' => $this->post('role_id'),
            'username' => $this->post('username'),
            'email' => $this->post('email'),
            'password' => $this->post('password'),
            'full_name' => $this->post('full_name'),
            'phone' => $this->post('phone'),
            'status' => $this->post('status', 'active')
        ];
        
        $errors = $this->validate($data, [
            'username' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'full_name' => 'required'
        ]);
        
        if (!empty($errors)) {
            setFlash('error', 'Please fix the errors below');
            $this->back();
        }
        
        $authModel = new Auth();
        
        // Check if username exists
        if ($authModel->usernameExists($data['username'])) {
            setFlash('error', 'Username already exists');
            $this->back();
        }
        
        // Check if email exists
        if ($authModel->emailExists($data['email'])) {
            setFlash('error', 'Email already exists');
            $this->back();
        }
        
        $role = Database::getInstance()->query("SELECT name FROM roles WHERE id = ?")
            ->bind(1, (int) $data['role_id'])
            ->fetch();
        if (!$role) {
            setFlash('error', 'Please select a valid role.');
            $this->back();
        }

        $userId = $authModel->createUserWithProfile($data, $role['role_name']);
        
        if ($userId) {
            logActivity(currentUserId(), 'create', 'users', "Created user: {$data['username']}");
            setFlash('success', 'User created successfully');
            $this->redirect(BASE_URL . '/admin/users');
        } else {
            setFlash('error', 'Failed to create user');
            $this->back();
        }
    }
    
    // Edit User
    public function editUser($id) {
        $user = $this->userModel->getUserWithRole($id);
        
        if (!$user) {
            setFlash('error', 'User not found');
            $this->redirect(BASE_URL . '/admin/users');
        }
        
        if (isPost()) {
            $this->handleEditUser($id);
        }
        
        $db = Database::getInstance();
        $roles = $db->query("SELECT * FROM roles ORDER BY id")->fetchAll();
        
        $this->layout('main', 'admin.user-form', [
            'pageTitle' => 'Edit User',
            'user' => $user,
            'roles' => $roles
        ]);
    }
    
    private function handleEditUser($id) {
        if (!verifyCsrfToken($this->post('csrf_token'))) {
            setFlash('error', 'Invalid request. Please try again.');
            $this->redirect(BASE_URL . '/admin/edit-user/' . $id);
        }

        $data = [
            'role_id' => $this->post('role_id'),
            'email' => $this->post('email'),
            'full_name' => $this->post('full_name'),
            'phone' => $this->post('phone'),
            'status' => $this->post('status')
        ];
        
        $errors = $this->validate($data, [
            'email' => 'required|email',
            'full_name' => 'required'
        ]);
        
        if (!empty($errors)) {
            setFlash('error', 'Please fix the errors below');
            $this->back();
        }
        
        $sql = "UPDATE users SET role_id = ?, email = ?, full_name = ?, phone = ?, status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $db = Database::getInstance();
        
        $result = $db->query($sql)
                      ->bind(1, $data['role_id'])
                      ->bind(2, $data['email'])
                      ->bind(3, $data['full_name'])
                      ->bind(4, $data['phone'])
                      ->bind(5, $data['status'])
                      ->bind(6, $id)
                      ->execute();
        
        if ($result) {
            logActivity(currentUserId(), 'update', 'users', "Updated user ID: $id");
            setFlash('success', 'User updated successfully');
            $this->redirect(BASE_URL . '/admin/users');
        } else {
            setFlash('error', 'Failed to update user');
            $this->back();
        }
    }
    
    // Delete User
    public function deleteUser($id) {
        if (!isPost() || !verifyCsrfToken($this->post('csrf_token'))) {
            setFlash('error', 'Invalid request.');
            $this->redirect(BASE_URL . '/admin/users');
        }

        if ($id == currentUserId()) {
            setFlash('error', 'You cannot delete your own account');
            $this->redirect(BASE_URL . '/admin/users');
        }
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            setFlash('error', 'User not found');
            $this->redirect(BASE_URL . '/admin/users');
        }
        
        if ($this->userModel->delete($id)) {
            logActivity(currentUserId(), 'delete', 'users', "Deleted user ID: $id");
            setFlash('success', 'User deleted successfully');
        } else {
            setFlash('error', 'Failed to delete user');
        }
        
        $this->redirect(BASE_URL . '/admin/users');
    }
    
    // Toggle User Status
    public function toggleUserStatus($id) {
        if (!isPost() || !verifyCsrfToken($this->post('csrf_token'))) {
            $this->error('Invalid request.', 400);
        }

        $user = $this->userModel->find($id);
        
        if (!$user) {
            $this->error('User not found');
        }
        
        $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
        
        if ($this->userModel->updateStatus($id, $newStatus)) {
            logActivity(currentUserId(), 'update', 'users', "Changed user ID $id status to $newStatus");
            $this->success('User status updated');
        } else {
            $this->error('Failed to update user status');
        }
    }
    
    // ==================== STUDENT MANAGEMENT ====================
    
    public function students($id = null) {
        if ($id) return $this->viewStudent($id);
        
        $search = $this->get('search', '');
        $students = $search ? $this->studentModel->search($search) : $this->studentModel->getAllWithUser();
        
        $this->layout('main', 'admin.students', [
            'pageTitle' => 'Student Management',
            'students' => $students,
            'search' => $search
        ]);
    }
    
    public function viewStudent($id) {
        $student = $this->studentModel->getWithUser($id);
        if (!$student) {
            setFlash('error', 'Student not found');
            $this->redirect(BASE_URL . '/admin/students');
        }
        $enrollments = $this->studentModel->getEnrollments($id);
        
        $this->layout('main', 'admin.student-detail', [
            'pageTitle' => 'Student Details',
            'student' => $student,
            'enrollments' => $enrollments
        ]);
    }
    
    // ==================== TEACHER MANAGEMENT ====================
    
    public function teachers($id = null) {
        if ($id) return $this->viewTeacher($id);
        
        $search = $this->get('search', '');
        $teachers = $search ? $this->teacherModel->search($search) : $this->teacherModel->getAllWithUser();
        
        $this->layout('main', 'admin.teachers', [
            'pageTitle' => 'Teacher Management',
            'teachers' => $teachers,
            'search' => $search
        ]);
    }
    
    public function viewTeacher($id) {
        $teacher = $this->teacherModel->getWithUser($id);
        if (!$teacher) {
            setFlash('error', 'Teacher not found');
            $this->redirect(BASE_URL . '/admin/teachers');
        }
        $batches = $this->teacherModel->getAssignedBatches($id);
        
        $this->layout('main', 'admin.teacher-detail', [
            'pageTitle' => 'Teacher Details',
            'teacher' => $teacher,
            'batches' => $batches
        ]);
    }
    
    // ==================== STAFF MANAGEMENT ====================
    
    public function staff($id = null) {
        if ($id) return $this->viewStaffMember($id);
        
        $search = $this->get('search', '');
        $staff = $search ? $this->staffModel->search($search) : $this->staffModel->getAllWithUser();
        
        $this->layout('main', 'admin.staff', [
            'pageTitle' => 'Staff Management',
            'staff' => $staff,
            'search' => $search
        ]);
    }
    
    public function viewStaffMember($id) {
        $staff = $this->staffModel->getWithUser($id);
        if (!$staff) {
            setFlash('error', 'Staff member not found');
            $this->redirect(BASE_URL . '/admin/staff');
        }
        
        $this->layout('main', 'admin.staff-detail', [
            'pageTitle' => 'Staff Details',
            'staff' => $staff
        ]);
    }
    
    // ==================== COURSE MANAGEMENT ====================
    
    public function courses($id = null) {
        if ($id) return $this->viewCourse($id);
        
        $search = $this->get('search', '');
        $status = $this->get('status', '');
        
        $courses = $search ? $this->courseModel->search($search) : $this->courseModel->getAllWithSubjectCount($status ?: null);
        
        $this->layout('main', 'admin.courses', [
            'pageTitle' => 'Course Management',
            'courses' => $courses,
            'search' => $search,
            'status' => $status
        ]);
    }
    
    public function viewCourse($id) {
        $course = $this->courseModel->find($id);
        if (!$course) {
            setFlash('error', 'Course not found');
            $this->redirect(BASE_URL . '/admin/courses');
        }
        $subjects = $this->courseModel->getSubjects($id);
        $batches = $this->courseModel->getBatches($id);
        
        $this->layout('main', 'admin.course-detail', [
            'pageTitle' => 'Course Details',
            'course' => $course,
            'subjects' => $subjects,
            'batches' => $batches
        ]);
    }
    
    public function createCourse() {
        if (isPost()) {
            $data = [
                'code' => $this->post('code'),
                'name' => $this->post('name'),
                'description' => $this->post('description'),
                'course_fee' => $this->post('course_fee'),
                'registration_fee' => $this->post('registration_fee', 0),
                'monthly_fee' => $this->post('monthly_fee', 0),
                'duration_months' => $this->post('duration_months', 1),
                'status' => $this->post('status', 'active')
            ];
            $errors = $this->validate($data, ['code' => 'required', 'name' => 'required', 'course_fee' => 'required|numeric']);
            if (!empty($errors)) {
                setFlash('error', 'Please fix the errors');
                $this->back();
            }
            $courseId = $this->courseModel->create($data);
            if ($courseId) {
                logActivity(currentUserId(), 'create', 'courses', "Created course: {$data['code']}");
                setFlash('success', 'Course created successfully');
                $this->redirect(BASE_URL . '/admin/courses');
            } else {
                setFlash('error', 'Failed to create course');
                $this->back();
            }
        }
        
        $subjects = $this->subjectModel->all('name ASC');
        $teachers = $this->teacherModel->getAllWithUser();
        
        $this->layout('main', 'admin.course-form', [
            'pageTitle' => 'Create Course',
            'subjects' => $subjects,
            'teachers' => $teachers
        ]);
    }
    
    public function editCourse($id) {
        $course = $this->courseModel->find($id);
        if (!$course) {
            setFlash('error', 'Course not found');
            $this->redirect(BASE_URL . '/admin/courses');
        }
        
        if (isPost()) {
            $data = [
                'code' => $this->post('code'),
                'name' => $this->post('name'),
                'description' => $this->post('description'),
                'course_fee' => $this->post('course_fee'),
                'registration_fee' => $this->post('registration_fee', 0),
                'monthly_fee' => $this->post('monthly_fee', 0),
                'duration_months' => $this->post('duration_months', 1),
                'status' => $this->post('status', 'active')
            ];
            $this->courseModel->update($id, $data);
            logActivity(currentUserId(), 'update', 'courses', "Updated course ID: $id");
            setFlash('success', 'Course updated successfully');
            $this->redirect(BASE_URL . '/admin/courses');
        }
        
        $subjects = $this->subjectModel->all('name ASC');
        $teachers = $this->teacherModel->getAllWithUser();
        $courseSubjects = $this->courseModel->getSubjects($id);
        
        $this->layout('main', 'admin.course-form', [
            'pageTitle' => 'Edit Course',
            'course' => $course,
            'subjects' => $subjects,
            'teachers' => $teachers,
            'courseSubjects' => $courseSubjects
        ]);
    }
    
    // ==================== SUBJECT MANAGEMENT ====================
    
    public function subjects($id = null) {
        if ($id) {
            $subject = $this->subjectModel->find($id);
            if (!$subject) {
                setFlash('error', 'Subject not found');
                $this->redirect(BASE_URL . '/admin/subjects');
            }
            $this->layout('main', 'admin.subject-detail', [
                'pageTitle' => 'Subject Details',
                'subject' => $subject
            ]);
            return;
        }
        
        $search = $this->get('search', '');
        $subjects = $search ? $this->subjectModel->search($search) : $this->subjectModel->all('name ASC');
        
        $this->layout('main', 'admin.subjects', [
            'pageTitle' => 'Subject Management',
            'subjects' => $subjects,
            'search' => $search
        ]);
    }
    
    public function createSubject() {
        if (isPost()) {
            $data = [
                'code' => $this->post('code'),
                'name' => $this->post('name'),
                'description' => $this->post('description')
            ];
            $errors = $this->validate($data, ['code' => 'required', 'name' => 'required']);
            if (!empty($errors)) {
                setFlash('error', 'Please fix the errors');
                $this->back();
            }
            $id = $this->subjectModel->create($data);
            if ($id) {
                logActivity(currentUserId(), 'create', 'subjects', "Created subject: {$data['code']}");
                setFlash('success', 'Subject created successfully');
                $this->redirect(BASE_URL . '/admin/subjects');
            } else {
                setFlash('error', 'Failed to create subject');
                $this->back();
            }
        }
        
        $this->layout('main', 'admin.subject-form', ['pageTitle' => 'Create Subject']);
    }
    
    public function editSubject($id) {
        $subject = $this->subjectModel->find($id);
        if (!$subject) {
            setFlash('error', 'Subject not found');
            $this->redirect(BASE_URL . '/admin/subjects');
        }
        
        if (isPost()) {
            $data = [
                'code' => $this->post('code'),
                'name' => $this->post('name'),
                'description' => $this->post('description')
            ];
            $this->subjectModel->update($id, $data);
            logActivity(currentUserId(), 'update', 'subjects', "Updated subject ID: $id");
            setFlash('success', 'Subject updated successfully');
            $this->redirect(BASE_URL . '/admin/subjects');
        }
        
        $this->layout('main', 'admin.subject-form', [
            'pageTitle' => 'Edit Subject',
            'subject' => $subject
        ]);
    }
    
    public function deleteSubject($id) {
        if ($this->subjectModel->delete($id)) {
            logActivity(currentUserId(), 'delete', 'subjects', "Deleted subject ID: $id");
            setFlash('success', 'Subject deleted successfully');
        } else {
            setFlash('error', 'Failed to delete subject');
        }
        $this->redirect(BASE_URL . '/admin/subjects');
    }
    
    // ==================== BATCH MANAGEMENT ====================
    
    public function batches($id = null) {
        if ($id) return $this->viewBatch($id);
        
        $search = $this->get('search', '');
        $status = $this->get('status', '');
        
        $batches = $search ? $this->batchModel->search($search) : $this->batchModel->getAllWithCourse($status ?: null);
        
        $this->layout('main', 'admin.batches', [
            'pageTitle' => 'Batch Management',
            'batches' => $batches,
            'search' => $search,
            'status' => $status
        ]);
    }
    
    public function viewBatch($id) {
        $batch = $this->batchModel->getWithCourse($id);
        if (!$batch) {
            setFlash('error', 'Batch not found');
            $this->redirect(BASE_URL . '/admin/batches');
        }
        $students = $this->batchModel->getStudents($id);
        $timetable = $this->timetableModel->getByBatch($id);
        
        $this->layout('main', 'admin.batch-detail', [
            'pageTitle' => 'Batch Details',
            'batch' => $batch,
            'students' => $students,
            'timetable' => $timetable
        ]);
    }
    
    public function createBatch() {
        if (isPost()) {
            $data = [
                'course_id' => $this->post('course_id'),
                'batch_name' => $this->post('batch_name'),
                'start_date' => $this->post('start_date'),
                'end_date' => $this->post('end_date'),
                'max_students' => $this->post('max_students', 50),
                'status' => $this->post('status', 'pending')
            ];
            $errors = $this->validate($data, ['course_id' => 'required', 'batch_name' => 'required', 'start_date' => 'required', 'end_date' => 'required']);
            if (!empty($errors)) {
                setFlash('error', 'Please fix the errors');
                $this->back();
            }
            $id = $this->batchModel->create($data);
            if ($id) {
                logActivity(currentUserId(), 'create', 'batches', "Created batch: {$data['batch_name']}");
                setFlash('success', 'Batch created successfully');
                $this->redirect(BASE_URL . '/admin/batches');
            } else {
                setFlash('error', 'Failed to create batch');
                $this->back();
            }
        }
        
        $courses = $this->courseModel->all('name ASC');
        
        $this->layout('main', 'admin.batch-form', [
            'pageTitle' => 'Create Batch',
            'courses' => $courses
        ]);
    }
    
    public function editBatch($id) {
        $batch = $this->batchModel->find($id);
        if (!$batch) {
            setFlash('error', 'Batch not found');
            $this->redirect(BASE_URL . '/admin/batches');
        }
        
        if (isPost()) {
            $data = [
                'course_id' => $this->post('course_id'),
                'batch_name' => $this->post('batch_name'),
                'start_date' => $this->post('start_date'),
                'end_date' => $this->post('end_date'),
                'max_students' => $this->post('max_students', 50),
                'status' => $this->post('status', 'active')
            ];
            $this->batchModel->update($id, $data);
            logActivity(currentUserId(), 'update', 'batches', "Updated batch ID: $id");
            setFlash('success', 'Batch updated successfully');
            $this->redirect(BASE_URL . '/admin/batches');
        }
        
        $courses = $this->courseModel->all('name ASC');
        
        $this->layout('main', 'admin.batch-form', [
            'pageTitle' => 'Edit Batch',
            'batch' => $batch,
            'courses' => $courses
        ]);
    }
    
    // ==================== TIMETABLE MANAGEMENT ====================
    
    public function timetables($id = null) {
        $db = Database::getInstance();
        
        if ($id) return $this->editTimetable($id);
        
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
        
        $this->layout('main', 'admin.timetables', [
            'pageTitle' => 'Timetable Management',
            'slots' => $slots,
            'batches' => $batches,
            'selectedBatch' => $batchId
        ]);
    }
    
    public function createTimetable() {
        $db = Database::getInstance();
        
        if (isPost()) {
            $data = [
                'batch_id' => $this->post('batch_id'),
                'subject_id' => $this->post('subject_id'),
                'teacher_id' => $this->post('teacher_id'),
                'day_of_week' => $this->post('day_of_week'),
                'start_time' => $this->post('start_time'),
                'end_time' => $this->post('end_time'),
                'room' => $this->post('room')
            ];
            
            if ($this->timetableModel->hasTimeConflict($data['batch_id'], $data['day_of_week'], $data['start_time'], $data['end_time'])) {
                setFlash('error', 'Time conflict detected for this batch');
                $this->back();
            }
            
            $id = $this->timetableModel->create($data);
            if ($id) {
                logActivity(currentUserId(), 'create', 'timetables', "Created timetable slot ID: $id");
                setFlash('success', 'Timetable slot created successfully');
                $this->redirect(BASE_URL . '/admin/timetables');
            } else {
                setFlash('error', 'Failed to create timetable slot');
                $this->back();
            }
        }
        
        $batches = $db->query("SELECT b.*, c.name as course_name FROM batches b INNER JOIN courses c ON b.course_id = c.id WHERE b.status = 'active'")->fetchAll();
        $subjects = $this->subjectModel->all('name ASC');
        $teachers = $this->teacherModel->getAllWithUser();
        
        $this->layout('main', 'admin.timetable-form', [
            'pageTitle' => 'Create Timetable Slot',
            'batches' => $batches,
            'subjects' => $subjects,
            'teachers' => $teachers
        ]);
    }
    
    public function editTimetable($id) {
        $slot = $this->timetableModel->find($id);
        if (!$slot) {
            setFlash('error', 'Timetable slot not found');
            $this->redirect(BASE_URL . '/admin/timetables');
        }
        
        if (isPost()) {
            $data = [
                'batch_id' => $this->post('batch_id'),
                'subject_id' => $this->post('subject_id'),
                'teacher_id' => $this->post('teacher_id'),
                'day_of_week' => $this->post('day_of_week'),
                'start_time' => $this->post('start_time'),
                'end_time' => $this->post('end_time'),
                'room' => $this->post('room')
            ];
            $this->timetableModel->update($id, $data);
            logActivity(currentUserId(), 'update', 'timetables', "Updated timetable slot ID: $id");
            setFlash('success', 'Timetable slot updated successfully');
            $this->redirect(BASE_URL . '/admin/timetables');
        }
        
        $db = Database::getInstance();
        $batches = $db->query("SELECT b.*, c.name as course_name FROM batches b INNER JOIN courses c ON b.course_id = c.id WHERE b.status = 'active'")->fetchAll();
        $subjects = $this->subjectModel->all('name ASC');
        $teachers = $this->teacherModel->getAllWithUser();
        
        $this->layout('main', 'admin.timetable-form', [
            'pageTitle' => 'Edit Timetable Slot',
            'slot' => $slot,
            'batches' => $batches,
            'subjects' => $subjects,
            'teachers' => $teachers
        ]);
    }
    
    public function deleteTimetable($id) {
        if ($this->timetableModel->delete($id)) {
            logActivity(currentUserId(), 'delete', 'timetables', "Deleted timetable slot ID: $id");
            setFlash('success', 'Timetable slot deleted');
        } else {
            setFlash('error', 'Failed to delete timetable slot');
        }
        $this->redirect(BASE_URL . '/admin/timetables');
    }
    
    // ==================== ASSIGNMENT MANAGEMENT ====================
    
    public function assignments($id = null) {
        $db = Database::getInstance();
        
        if ($id) return $this->viewAssignment($id);
        
        $assignments = $db->query("SELECT a.*, b.batch_name, s.name as subject_name, 
                                          t.first_name, t.last_name 
                                   FROM assignments a 
                                   INNER JOIN batches b ON a.batch_id = b.id 
                                   INNER JOIN subjects s ON a.subject_id = s.id 
                                   INNER JOIN teachers t ON a.teacher_id = t.id 
                                   ORDER BY a.created_at DESC")->fetchAll();
        
        $this->layout('main', 'admin.assignments', [
            'pageTitle' => 'Assignment Management',
            'assignments' => $assignments
        ]);
    }
    
    public function viewAssignment($id) {
        $assignment = $this->assignmentModel->getWithDetails($id);
        if (!$assignment) {
            setFlash('error', 'Assignment not found');
            $this->redirect(BASE_URL . '/admin/assignments');
        }
        $submissions = $this->assignmentModel->getSubmissions($id);
        
        $this->layout('main', 'admin.assignment-detail', [
            'pageTitle' => 'Assignment Details',
            'assignment' => $assignment,
            'submissions' => $submissions
        ]);
    }
    
    // ==================== EXAM MANAGEMENT ====================
    
    public function exams($id = null) {
        $db = Database::getInstance();
        
        if ($id) return $this->viewExam($id);
        
        $exams = $db->query("SELECT e.*, b.batch_name, s.name as subject_name, 
                                    t.first_name, t.last_name 
                             FROM exams e 
                             INNER JOIN batches b ON e.batch_id = b.id 
                             INNER JOIN subjects s ON e.subject_id = s.id 
                             INNER JOIN teachers t ON e.teacher_id = t.id 
                             ORDER BY e.exam_date DESC")->fetchAll();
        
        $this->layout('main', 'admin.exams', [
            'pageTitle' => 'Examination Management',
            'exams' => $exams
        ]);
    }
    
    public function viewExam($id) {
        $exam = $this->examModel->getWithDetails($id);
        if (!$exam) {
            setFlash('error', 'Exam not found');
            $this->redirect(BASE_URL . '/admin/exams');
        }
        $questions = $this->examModel->getQuestions($id);
        $results = $this->examModel->getResults($id);
        
        $this->layout('main', 'admin.exam-detail', [
            'pageTitle' => 'Exam Details',
            'exam' => $exam,
            'questions' => $questions,
            'results' => $results
        ]);
    }
    
    // ==================== PAYMENT MANAGEMENT ====================
    
    public function payments() {
        $status = $this->get('status', '');
        $dateFrom = $this->get('date_from', '');
        $dateTo = $this->get('date_to', '');
        
        $filters = [];
        if ($status) $filters['status'] = $status;
        if ($dateFrom) $filters['date_from'] = $dateFrom;
        if ($dateTo) $filters['date_to'] = $dateTo;
        
        $payments = $this->paymentModel->getAllWithDetails($filters);
        
        $this->layout('main', 'admin.payments', [
            'pageTitle' => 'Payment Management',
            'payments' => $payments,
            'filters' => ['status' => $status, 'date_from' => $dateFrom, 'date_to' => $dateTo]
        ]);
    }
    
    // ==================== NOTICE MANAGEMENT ====================
    
    public function notices($id = null) {
        if ($id) return $this->viewNotice($id);
        
        $notices = $this->noticeModel->getAllWithDetails();
        
        $this->layout('main', 'admin.notices', [
            'pageTitle' => 'Notice Management',
            'notices' => $notices
        ]);
    }
    
    public function viewNotice($id) {
        $notice = $this->noticeModel->getWithDetails($id);
        if (!$notice) {
            setFlash('error', 'Notice not found');
            $this->redirect(BASE_URL . '/admin/notices');
        }
        $this->layout('main', 'admin.notice-detail', [
            'pageTitle' => 'Notice Details',
            'notice' => $notice
        ]);
    }
    
    public function createNotice() {
        if (isPost()) {
            $data = [
                'title' => $this->post('title'),
                'content' => $this->post('content'),
                'target_role' => $this->post('target_role', 'all'),
                'is_pinned' => $this->post('is_pinned', 0) ? 1 : 0,
                'posted_by' => currentUserId()
            ];
            $errors = $this->validate($data, ['title' => 'required', 'content' => 'required']);
            if (!empty($errors)) {
                setFlash('error', 'Please fix the errors');
                $this->back();
            }
            $id = $this->noticeModel->create($data);
            if ($id) {
                logActivity(currentUserId(), 'create', 'notices', "Created notice: {$data['title']}");
                setFlash('success', 'Notice created successfully');
                $this->redirect(BASE_URL . '/admin/notices');
            } else {
                setFlash('error', 'Failed to create notice');
                $this->back();
            }
        }
        
        $this->layout('main', 'admin.notice-form', ['pageTitle' => 'Create Notice']);
    }
    
    public function editNotice($id) {
        $notice = $this->noticeModel->find($id);
        if (!$notice) {
            setFlash('error', 'Notice not found');
            $this->redirect(BASE_URL . '/admin/notices');
        }
        
        if (isPost()) {
            $data = [
                'title' => $this->post('title'),
                'content' => $this->post('content'),
                'target_role' => $this->post('target_role', 'all'),
                'is_pinned' => $this->post('is_pinned', 0) ? 1 : 0
            ];
            $this->noticeModel->update($id, $data);
            logActivity(currentUserId(), 'update', 'notices', "Updated notice ID: $id");
            setFlash('success', 'Notice updated successfully');
            $this->redirect(BASE_URL . '/admin/notices');
        }
        
        $this->layout('main', 'admin.notice-form', [
            'pageTitle' => 'Edit Notice',
            'notice' => $notice
        ]);
    }
    
    public function deleteNotice($id) {
        if ($this->noticeModel->delete($id)) {
            logActivity(currentUserId(), 'delete', 'notices', "Deleted notice ID: $id");
            setFlash('success', 'Notice deleted successfully');
        } else {
            setFlash('error', 'Failed to delete notice');
        }
        $this->redirect(BASE_URL . '/admin/notices');
    }
    
    // ==================== REPORTS ====================
    
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
            'top_students' => $db->query("SELECT s.student_code, s.first_name, s.last_name, 
                                                 AVG(er.percentage) as avg_score 
                                          FROM students s 
                                          INNER JOIN exam_results er ON s.id = er.student_id 
                                          GROUP BY s.id ORDER BY avg_score DESC LIMIT 10")->fetchAll(),
            'batch_performance' => $db->query("SELECT b.batch_name, c.name as course_name, 
                                                     COUNT(e.id) as enrolled 
                                              FROM batches b 
                                              INNER JOIN courses c ON b.course_id = c.id 
                                              LEFT JOIN enrollments e ON b.id = e.batch_id 
                                              GROUP BY b.id ORDER BY enrolled DESC")->fetchAll()
        ];
        
        $this->layout('main', 'admin.reports', [
            'pageTitle' => 'Reports',
            'reportData' => $reportData
        ]);
    }
    
    // ==================== SETTINGS ====================
    
    public function settings() {
        if (isPost()) {
            $settings = $this->post();
            unset($settings['csrf_token']);
            foreach ($settings as $key => $value) {
                $this->settingsModel->set($key, $value);
            }
            logActivity(currentUserId(), 'update', 'settings', "Updated system settings");
            setFlash('success', 'Settings updated successfully');
            $this->back();
        }
        
        $allSettings = $this->settingsModel->getAll();
        $grouped = [];
        foreach ($allSettings as $setting) {
            $grouped[$setting['setting_group']][] = $setting;
        }
        
        $this->layout('main', 'admin.settings', [
            'pageTitle' => 'System Settings',
            'settings' => $grouped
        ]);
    }
    
    // ==================== BACKUP ====================
    
    public function backup() {
        if (isPost() && $this->post('action') === 'download') {
            $this->downloadBackup();
        }
        
        $backups = glob(UPLOAD_BACKUPS . '/*.sql');
        $backupList = [];
        if ($backups) {
            foreach (array_slice(array_reverse($backups), 0, 20) as $file) {
                $backupList[] = [
                    'filename' => basename($file),
                    'size' => filesize($file),
                    'date' => filemtime($file)
                ];
            }
        }
        
        $this->layout('main', 'admin.backup', [
            'pageTitle' => 'Database Backup',
            'backups' => $backupList
        ]);
    }
    
    private function downloadBackup() {
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = UPLOAD_BACKUPS . '/' . $filename;
        
        if (!is_dir(UPLOAD_BACKUPS)) {
            mkdir(UPLOAD_BACKUPS, 0755, true);
        }
        
        $command = sprintf('mysqldump --host=%s --user=%s --password=%s %s > "%s" 2>&1',
            escapeshellarg(DB_HOST),
            escapeshellarg(DB_USER),
            escapeshellarg(DB_PASS),
            escapeshellarg(DB_NAME),
            $filepath
        );
        
        exec($command, $output, $returnVar);
        
        if (file_exists($filepath) && filesize($filepath) > 0) {
            logActivity(currentUserId(), 'backup', 'database', "Created backup: $filename");
            
            header('Content-Type: application/sql');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit;
        } else {
            setFlash('error', 'Backup failed. Please check mysqldump is available.');
            $this->redirect(BASE_URL . '/admin/backup');
        }
    }
    
    // ==================== ACTIVITY LOGS ====================
    
    public function logs() {
        $action = $this->get('action', '');
        $module = $this->get('module', '');
        
        $filters = [];
        if ($action) $filters['action'] = $action;
        if ($module) $filters['module'] = $module;
        
        $logs = $this->activityLogModel->getAllWithUsers(100, $filters);
        
        $this->layout('main', 'admin.logs', [
            'pageTitle' => 'Activity Logs',
            'logs' => $logs,
            'filterAction' => $action,
            'filterModule' => $module
        ]);
    }
}
