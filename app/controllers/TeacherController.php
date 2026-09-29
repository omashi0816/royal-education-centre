<?php
/**
 * Royal Education Center Management System
 * Teacher Controller
 */

require_once APP_PATH . '/models/Teacher.php';
require_once APP_PATH . '/models/Timetable.php';
require_once APP_PATH . '/models/Assignment.php';
require_once APP_PATH . '/models/Exam.php';
require_once APP_PATH . '/models/Attendance.php';
require_once APP_PATH . '/models/StudyMaterial.php';
require_once APP_PATH . '/models/Subject.php';
require_once APP_PATH . '/models/Batch.php';
require_once APP_PATH . '/models/Payment.php';

class TeacherController extends Controller {
    private $teacherModel;
    private $timetableModel;
    private $assignmentModel;
    private $examModel;
    private $attendanceModel;
    private $studyMaterialModel;
    private $subjectModel;
    private $batchModel;
    private $paymentModel;
    
    public function __construct() {
        parent::__construct();
        $this->requireRole('teacher');
        
        $this->teacherModel = new Teacher();
        $this->timetableModel = new Timetable();
        $this->assignmentModel = new Assignment();
        $this->examModel = new Exam();
        $this->attendanceModel = new Attendance();
        $this->studyMaterialModel = new StudyMaterial();
        $this->subjectModel = new Subject();
        $this->batchModel = new Batch();
        $this->paymentModel = new Payment();
    }
    
    // Teacher Dashboard
    public function dashboard() {
        $db = Database::getInstance();
        $teacherId = $this->getTeacherId();
        
        $stats = [
            'assigned_batches' => $db->query("SELECT COUNT(DISTINCT batch_id) FROM timetable_slots WHERE teacher_id = ?")->bind(1, $teacherId)->fetchColumn(),
            'total_students' => $db->query("SELECT COUNT(DISTINCT e.student_id) FROM enrollments e INNER JOIN timetable_slots ts ON e.batch_id = ts.batch_id WHERE ts.teacher_id = ?")->bind(1, $teacherId)->fetchColumn(),
            'active_assignments' => $db->query("SELECT COUNT(*) FROM assignments WHERE teacher_id = ? AND due_date > NOW()")->bind(1, $teacherId)->fetchColumn(),
            'upcoming_exams' => $db->query("SELECT COUNT(*) FROM exams WHERE teacher_id = ? AND exam_date > NOW()")->bind(1, $teacherId)->fetchColumn()
        ];
        
        $upcomingClasses = $db->query("SELECT ts.*, b.batch_name, s.name as subject_name 
                                      FROM timetable_slots ts 
                                      INNER JOIN batches b ON ts.batch_id = b.id 
                                      INNER JOIN subjects s ON ts.subject_id = s.id 
                                      WHERE ts.teacher_id = ? 
                                      ORDER BY FIELD(ts.day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday'), ts.start_time LIMIT 5")
                                      ->bind(1, $teacherId)->fetchAll();

        $exams = $this->examModel->getByTeacher($teacherId);
        $assignments = $this->assignmentModel->getByTeacher($teacherId);
        
        $this->layout('main', 'teacher.dashboard', [
            'pageTitle' => 'Teacher Dashboard',
            'stats' => $stats,
            'upcomingClasses' => $upcomingClasses,
            'exams' => $exams,
            'assignments' => $assignments
        ]);
    }

    // Results and marks entry center
    public function results() {
        $teacherId = $this->getTeacherId();

        $this->layout('main', 'teacher.results', [
            'pageTitle' => 'Enter Results',
            'exams' => $this->examModel->getByTeacher($teacherId),
            'assignments' => $this->assignmentModel->getByTeacher($teacherId)
        ]);
    }
    
    // Get current teacher ID
    private function getTeacherId() {
        $db = Database::getInstance();
        $sql = "SELECT id FROM teachers WHERE user_id = ?";
        return $db->query($sql)->bind(1, currentUserId())->fetchColumn();
    }
    
    // My Courses
    public function courses() {
        $teacherId = $this->getTeacherId();
        $batches = $this->teacherModel->getAssignedBatches($teacherId);
        
        $this->layout('main', 'teacher.courses', [
            'pageTitle' => 'My Courses',
            'batches' => $batches
        ]);
    }
    
    // Timetable
    public function timetable() {
        $teacherId = $this->getTeacherId();
        $timetable = $this->timetableModel->getByTeacher($teacherId);
        
        $this->layout('main', 'teacher.timetable', [
            'pageTitle' => 'My Timetable',
            'timetable' => $timetable
        ]);
    }
    
    // My Students
    public function students() {
        $teacherId = $this->getTeacherId();
        $db = Database::getInstance();
        
        $students = $db->query("SELECT DISTINCT s.*, u.email 
                                FROM students s 
                                INNER JOIN users u ON s.user_id = u.id 
                                INNER JOIN enrollments e ON s.id = e.student_id 
                                INNER JOIN timetable_slots ts ON e.batch_id = ts.batch_id 
                                WHERE ts.teacher_id = ? 
                                ORDER BY s.first_name")->bind(1, $teacherId)->fetchAll();
        
        $this->layout('main', 'teacher.students', [
            'pageTitle' => 'My Students',
            'students' => $students
        ]);
    }

    // Payments from my students
    public function payments() {
        $teacherId = $this->getTeacherId();
        $payments = $this->paymentModel->getByTeacher($teacherId);

        $this->layout('main', 'teacher.payments', [
            'pageTitle' => 'Student Payments',
            'payments' => $payments
        ]);
    }
    
    // Attendance
    public function attendance() {
        $teacherId = $this->getTeacherId();
        $batches = $this->teacherModel->getAssignedBatches($teacherId);
        
        $batchId = $this->get('batch_id', '');
        $date = $this->get('date', date('Y-m-d'));
        $students = [];
        
        if ($batchId) {
            $db = Database::getInstance();
            $students = $db->query("SELECT s.*, u.email,
                                          COALESCE(a.status, 'not_marked') as attendance_status
                                   FROM students s 
                                   INNER JOIN users u ON s.user_id = u.id 
                                   INNER JOIN enrollments e ON s.id = e.student_id 
                                   LEFT JOIN attendance a ON a.enrollment_id = e.id AND a.date = ?
                                   WHERE e.batch_id = ? AND e.status = 'active'
                                   ORDER BY s.first_name")
                          ->bind(1, $date)->bind(2, $batchId)->fetchAll();
        }
        
        $this->layout('main', 'teacher.attendance', [
            'pageTitle' => 'Mark Attendance',
            'batches' => $batches,
            'selectedBatch' => $batchId,
            'date' => $date,
            'students' => $students,
            'attendanceHistory' => $this->attendanceModel->getByTeacher($teacherId)
        ]);
    }
    
    // Save Attendance
    public function saveAttendance() {
        if (!isPost()) {
            $this->redirect(BASE_URL . '/teacher/attendance');
        }
        
        $batchId = $this->post('batch_id');
        $date = $this->post('attendance_date');
        $statuses = $this->post('status', []);
        
        $result = $this->attendanceModel->saveAttendance($batchId, $date, $statuses);
        
        if ($result['success']) {
            logActivity(currentUserId(), 'create', 'attendance', "Marked attendance for batch $batchId on $date");
            setFlash('success', 'Attendance saved successfully');
        } else {
            setFlash('error', 'Failed to save attendance: ' . ($result['error'] ?? ''));
        }
        $this->redirect(BASE_URL . '/teacher/attendance?batch_id=' . $batchId . '&date=' . $date);
    }
    
    // Study Notes / Materials
    public function notes() {
        $teacherId = $this->getTeacherId();
        
        if (isPost()) {
            $data = [
                'batch_id' => $this->post('batch_id'),
                'subject_id' => $this->post('subject_id'),
                'teacher_id' => $teacherId,
                'title' => $this->post('title'),
                'description' => $this->post('description'),
                'file_path' => $this->post('file_path', ''),
                'file_type' => $this->post('file_type', 'link')
            ];
            $id = $this->studyMaterialModel->create($data);
            if ($id) {
                logActivity(currentUserId(), 'create', 'study_materials', "Created study material: {$data['title']}");
                setFlash('success', 'Study material added successfully');
            } else {
                setFlash('error', 'Failed to add study material');
            }
            $this->redirect(BASE_URL . '/teacher/notes');
        }
        
        $materials = $this->studyMaterialModel->getByTeacher($teacherId);
        $batches = $this->teacherModel->getAssignedBatches($teacherId);
        $subjects = $this->subjectModel->all('name ASC');
        
        $this->layout('main', 'teacher.notes', [
            'pageTitle' => 'Study Notes',
            'materials' => $materials,
            'batches' => $batches,
            'subjects' => $subjects
        ]);
    }
    
    // Delete Study Material
    public function deleteNote($id) {
        if ($this->studyMaterialModel->delete($id)) {
            logActivity(currentUserId(), 'delete', 'study_materials', "Deleted study material ID: $id");
            setFlash('success', 'Study material deleted');
        } else {
            setFlash('error', 'Failed to delete study material');
        }
        $this->redirect(BASE_URL . '/teacher/notes');
    }
    
    // Assignments
    public function assignments() {
        $teacherId = $this->getTeacherId();
        
        if (isPost()) {
            $data = [
                'batch_id' => $this->post('batch_id'),
                'subject_id' => $this->post('subject_id'),
                'teacher_id' => $teacherId,
                'title' => $this->post('title'),
                'description' => $this->post('description'),
                'due_date' => $this->post('due_date'),
                'max_marks' => $this->post('max_marks', 100)
            ];
            $id = $this->assignmentModel->create($data);
            if ($id) {
                logActivity(currentUserId(), 'create', 'assignments', "Created assignment: {$data['title']}");
                setFlash('success', 'Assignment created successfully');
            } else {
                setFlash('error', 'Failed to create assignment');
            }
            $this->redirect(BASE_URL . '/teacher/assignments');
        }
        
        $assignments = $this->assignmentModel->getByTeacher($teacherId);
        $batches = $this->teacherModel->getAssignedBatches($teacherId);
        $subjects = $this->subjectModel->all('name ASC');
        
        $this->layout('main', 'teacher.assignments', [
            'pageTitle' => 'My Assignments',
            'assignments' => $assignments,
            'batches' => $batches,
            'subjects' => $subjects
        ]);
    }
    
    // Delete Assignment
    public function deleteAssignment($id) {
        if ($this->assignmentModel->delete($id)) {
            logActivity(currentUserId(), 'delete', 'assignments', "Deleted assignment ID: $id");
            setFlash('success', 'Assignment deleted');
        } else {
            setFlash('error', 'Failed to delete assignment');
        }
        $this->redirect(BASE_URL . '/teacher/assignments');
    }

    // Enter or update marks for an assignment
    public function gradeAssignment($assignmentId) {
        $teacherId = $this->getTeacherId();
        $assignment = $this->assignmentModel->getWithDetails($assignmentId);
        if (!$assignment || (int) $assignment['teacher_id'] !== (int) $teacherId) {
            setFlash('error', 'Assignment not found or access denied');
            $this->redirect(BASE_URL . '/teacher/assignments');
        }

        $db = Database::getInstance();
         $students = $db->query("SELECT s.id, s.student_code, s.first_name, s.last_name,
                            u.email, u.phone, asub.submitted_at,
                            asub.marks, asub.feedback, asub.status AS submission_status
                                FROM enrollments e
                                INNER JOIN students s ON s.id = e.student_id
                        INNER JOIN users u ON u.id = s.user_id
                                LEFT JOIN assignment_submissions asub ON asub.assignment_id = ? AND asub.student_id = s.id
                                WHERE e.batch_id = ? AND e.status = 'active'
                                ORDER BY s.first_name, s.last_name")
                       ->bind(1, $assignmentId)->bind(2, $assignment['batch_id'])->fetchAll();

        $this->layout('main', 'teacher.grade-assignment', [
            'pageTitle' => 'Grade Assignment',
            'assignment' => $assignment,
            'students' => $students
        ]);
    }

    // Save assignment marks and feedback
    public function saveAssignmentGrades($assignmentId) {
        if (!isPost()) {
            $this->redirect(BASE_URL . '/teacher/assignments');
        }

        $teacherId = $this->getTeacherId();
        $assignment = $this->assignmentModel->getWithDetails($assignmentId);
        if (!$assignment || (int) $assignment['teacher_id'] !== (int) $teacherId) {
            setFlash('error', 'Assignment not found or access denied');
            $this->redirect(BASE_URL . '/teacher/assignments');
        }

        $marks = $this->post('marks', []);
        $feedback = $this->post('feedback', []);
        $db = Database::getInstance();
        $enrolledStudents = $db->query("SELECT student_id FROM enrollments WHERE batch_id = ? AND status = 'active'")
                              ->bind(1, $assignment['batch_id'])->fetchAll();
        $enrolledStudentIds = array_flip(array_map('intval', array_column($enrolledStudents, 'student_id')));
        $db->beginTransaction();

        try {
            foreach ($marks as $studentId => $mark) {
                if (!isset($enrolledStudentIds[(int) $studentId]) || $mark === '' || !is_numeric($mark) || $mark < 0 || $mark > $assignment['max_marks']) {
                    continue;
                }

                $sql = "INSERT INTO assignment_submissions (assignment_id, student_id, marks, feedback, graded_by, graded_at, status)
                        VALUES (?, ?, ?, ?, ?, NOW(), 'graded')
                        ON DUPLICATE KEY UPDATE marks = VALUES(marks), feedback = VALUES(feedback),
                            graded_by = VALUES(graded_by), graded_at = VALUES(graded_at), status = 'graded'";
                $db->query($sql)->bind(1, $assignmentId)->bind(2, (int) $studentId)->bind(3, (float) $mark)
                   ->bind(4, $feedback[$studentId] ?? null)->bind(5, currentUserId())->execute();
            }
            $db->commit();
            logActivity(currentUserId(), 'create', 'assignment_grades', "Entered marks for assignment ID: $assignmentId");
            setFlash('success', 'Assignment marks saved successfully');
        } catch (Exception $e) {
            $db->rollback();
            setFlash('error', 'Failed to save assignment marks');
        }

        $this->redirect(BASE_URL . '/teacher/grade-assignment/' . $assignmentId);
    }

    // Delete one assignment result
    public function deleteAssignmentGrade($assignmentId) {
        if (!isPost() || !verifyCsrfToken($this->post('csrf_token'))) {
            setFlash('error', 'Invalid result deletion request');
            $this->redirect(BASE_URL . '/teacher/grade-assignment/' . $assignmentId);
        }

        $teacherId = $this->getTeacherId();
        $assignment = $this->assignmentModel->getWithDetails($assignmentId);
        $studentId = (int) $this->post('student_id', 0);

        if (!$assignment || (int) $assignment['teacher_id'] !== (int) $teacherId || $studentId <= 0) {
            setFlash('error', 'Assignment result not found or access denied');
            $this->redirect(BASE_URL . '/teacher/assignments');
        }

        $db = Database::getInstance();
        $deleted = $db->query("DELETE FROM assignment_submissions WHERE assignment_id = ? AND student_id = ?")
                      ->bind(1, $assignmentId)
                      ->bind(2, $studentId)
                      ->execute();

        setFlash($deleted ? 'success' : 'error', $deleted ? 'Assignment result deleted' : 'Failed to delete assignment result');
        $this->redirect(BASE_URL . '/teacher/grade-assignment/' . $assignmentId);
    }
    
    // Exams
    public function exams() {
        $teacherId = $this->getTeacherId();
        
        if (isPost()) {
            $data = [
                'batch_id' => $this->post('batch_id'),
                'subject_id' => $this->post('subject_id'),
                'teacher_id' => $teacherId,
                'title' => $this->post('title'),
                'exam_date' => $this->post('exam_date'),
                'total_marks' => $this->post('total_marks', 100),
                'passing_marks' => $this->post('passing_marks', 40),
                'is_online' => $this->post('is_online', 0),
                'duration_minutes' => $this->post('duration_minutes', 120)
            ];
            $id = $this->examModel->create($data);
            if ($id) {
                logActivity(currentUserId(), 'create', 'exams', "Created exam: {$data['title']}");
                setFlash('success', 'Exam created successfully');
            } else {
                setFlash('error', 'Failed to create exam');
            }
            $this->redirect(BASE_URL . '/teacher/exams');
        }
        
        $exams = $this->examModel->getByTeacher($teacherId);
        $batches = $this->teacherModel->getAssignedBatches($teacherId);
        $subjects = $this->subjectModel->all('name ASC');
        
        $this->layout('main', 'teacher.exams', [
            'pageTitle' => 'My Exams',
            'exams' => $exams,
            'batches' => $batches,
            'subjects' => $subjects
        ]);
    }

    // Enter or update marks for an exam
    public function markResults($examId) {
        $teacherId = $this->getTeacherId();
        $exam = $this->examModel->getWithDetails($examId);

        if (!$exam || (int) $exam['teacher_id'] !== (int) $teacherId) {
            setFlash('error', 'Exam not found or access denied');
            $this->redirect(BASE_URL . '/teacher/exams');
        }

        $db = Database::getInstance();
         $students = $db->query("SELECT s.id, s.student_code, s.first_name, s.last_name,
                            u.email, u.phone, er.submitted_at,
                            er.marks_obtained, er.status AS result_status
                                FROM enrollments e
                                INNER JOIN students s ON s.id = e.student_id
                        INNER JOIN users u ON u.id = s.user_id
                                LEFT JOIN exam_results er ON er.exam_id = ? AND er.student_id = s.id
                                WHERE e.batch_id = ? AND e.status = 'active'
                                ORDER BY s.first_name, s.last_name")
                       ->bind(1, $examId)->bind(2, $exam['batch_id'])->fetchAll();

        $this->layout('main', 'teacher.mark-results', [
            'pageTitle' => 'Enter Exam Marks',
            'exam' => $exam,
            'students' => $students
        ]);
    }

    // Save exam marks
    public function saveResults($examId) {
        if (!isPost()) {
            $this->redirect(BASE_URL . '/teacher/exams');
        }

        $teacherId = $this->getTeacherId();
        $exam = $this->examModel->getWithDetails($examId);
        if (!$exam || (int) $exam['teacher_id'] !== (int) $teacherId) {
            setFlash('error', 'Exam not found or access denied');
            $this->redirect(BASE_URL . '/teacher/exams');
        }

        $marks = $this->post('marks', []);
        $db = Database::getInstance();
        $enrolledStudents = $db->query("SELECT student_id FROM enrollments WHERE batch_id = ? AND status = 'active'")
                              ->bind(1, $exam['batch_id'])->fetchAll();
        $enrolledStudentIds = array_flip(array_map('intval', array_column($enrolledStudents, 'student_id')));
        $db->beginTransaction();

        try {
            foreach ($marks as $studentId => $mark) {
                if (!isset($enrolledStudentIds[(int) $studentId]) || $mark === '' || !is_numeric($mark) || $mark < 0 || $mark > $exam['total_marks']) {
                    continue;
                }

                $percentage = ((float) $mark / (float) $exam['total_marks']) * 100;
                $grade = $this->examModel->calculateGrade($percentage, $exam['passing_marks']);
                $status = $percentage >= $exam['passing_marks'] ? 'passed' : 'failed';
                $sql = "INSERT INTO exam_results (exam_id, student_id, marks_obtained, total_marks, percentage, grade, status, submitted_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                        ON DUPLICATE KEY UPDATE marks_obtained = VALUES(marks_obtained), total_marks = VALUES(total_marks),
                            percentage = VALUES(percentage), grade = VALUES(grade), status = VALUES(status), submitted_at = VALUES(submitted_at)";
                $db->query($sql)->bind(1, $examId)->bind(2, (int) $studentId)->bind(3, (float) $mark)
                   ->bind(4, (int) $exam['total_marks'])->bind(5, $percentage)->bind(6, $grade)->bind(7, $status)->execute();
            }
            $db->commit();
            logActivity(currentUserId(), 'create', 'exam_results', "Entered marks for exam ID: $examId");
            setFlash('success', 'Exam marks saved successfully');
        } catch (Exception $e) {
            $db->rollback();
            setFlash('error', 'Failed to save exam marks');
        }

        $this->redirect(BASE_URL . '/teacher/mark-results/' . $examId);
    }

    // Delete one exam result
    public function deleteExamResult($examId) {
        if (!isPost() || !verifyCsrfToken($this->post('csrf_token'))) {
            setFlash('error', 'Invalid result deletion request');
            $this->redirect(BASE_URL . '/teacher/mark-results/' . $examId);
        }

        $teacherId = $this->getTeacherId();
        $exam = $this->examModel->getWithDetails($examId);
        $studentId = (int) $this->post('student_id', 0);

        if (!$exam || (int) $exam['teacher_id'] !== (int) $teacherId || $studentId <= 0) {
            setFlash('error', 'Exam result not found or access denied');
            $this->redirect(BASE_URL . '/teacher/exams');
        }

        $db = Database::getInstance();
        $deleted = $db->query("DELETE FROM exam_results WHERE exam_id = ? AND student_id = ?")
                      ->bind(1, $examId)
                      ->bind(2, $studentId)
                      ->execute();

        setFlash($deleted ? 'success' : 'error', $deleted ? 'Exam result deleted' : 'Failed to delete exam result');
        $this->redirect(BASE_URL . '/teacher/mark-results/' . $examId);
    }
    
    // Income
    public function income() {
        $teacherId = $this->getTeacherId();
        $teacher = $this->teacherModel->getWithUser($teacherId);
        
        $payments = $this->paymentModel->getByTeacher($teacherId);
        $totalIncome = array_sum(array_map(
            fn($payment) => $payment['status'] === 'completed' ? (float) $payment['final_amount'] : 0,
            $payments
        ));
        
        $this->layout('main', 'teacher.income', [
            'pageTitle' => 'My Income',
            'teacher' => $teacher,
            'payments' => $payments,
            'totalIncome' => $totalIncome
        ]);
    }
}
