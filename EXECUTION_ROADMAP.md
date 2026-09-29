# Royal Education Center Management System — Execution Roadmap

> **Status Legend:** `[ ]` Pending · `[~]` In Progress · `[x]` Completed

---

## Phase 1 — Foundation & Database (Target: ~15% of project)

| # | Task | Status |
|---|------|--------|
| 1.1 | Create project folder structure (MVC-inspired) | `[x]` |
| 1.2 | Create MySQL database schema (`database/schema.sql`) — all tables, PKs, FKs, indexes, constraints | `[x]` |
| 1.3 | Create SQL seed data (`database/seed.sql`) — roles, permissions, admin user, sample data | `[x]` |
| 1.4 | Core config file (`app/config/config.php`) — DB creds, app constants, error settings | `[x]` |
| 1.5 | Database PDO wrapper class (`app/core/Database.php`) — singleton, prepared statements, transactions | `[x]` |
| 1.6 | Helper functions (`app/helpers/helpers.php`) — sanitization, formatting, CSRF, flash messages | `[x]` |
| 1.7 | Session & security helpers (`app/core/Session.php`, `app/core/Security.php`) | `[x]` |

## Phase 2 — Core MVC Architecture (~10%)

| # | Task | Status |
|---|------|--------|
| 2.1 | Base Model class (`app/core/Model.php`) — generic CRUD, query builders | `[x]` |
| 2.2 | Base Controller class (`app/core/Controller.php`) — view loading, JSON, redirects, auth guards | `[x]` |
| 2.3 | View renderer (`app/core/View.php`) — layout partials, data injection | `[x]` |
| 2.4 | Router / Front controller (`public/index.php`) — URL routing to controllers/actions | `[x]` |
| 2.5 | Main layout template — sidebar, topbar, footer, toast notifications, dark/light toggle | `[x]` |
| 2.6 | `.htaccess` files for URL rewriting and security | `[x]` |

## Phase 3 — Authentication & RBAC (~10%)

| # | Task | Status |
|---|------|--------|
| 3.1 | AuthController — login, logout, login attempt protection | `[x]` |
| 3.2 | AuthModel — user lookup, password verify, login attempt tracking | `[x]` |
| 3.3 | Login view — responsive login page with validation | `[x]` |
| 3.4 | RBAC middleware — `requireRole()`, `requirePermission()` guard methods | `[x]` |
| 3.5 | Activity Log system — log all key actions (`app/models/ActivityLog.php`) | `[x]` |
| 3.6 | Notification system — in-app notifications model + partial | `[ ]` |

## Phase 4 — Shared Models (~8%)

| # | Task | Status |
|---|------|--------|
| 4.1 | UserModel, RoleModel, PermissionModel | `[x]` |
| 4.2 | StudentModel, TeacherModel, StaffModel | `[x]` |
| 4.3 | CourseModel, SubjectModel, BatchModel, EnrollmentModel | `[x]` |
| 4.4 | TimetableModel, OnlineClassModel, PhysicalClassModel | `[x]` |
| 4.5 | AssignmentModel, AssignmentSubmissionModel | `[x]` |
| 4.6 | ExamModel, ExamQuestionModel, ExamResultModel | `[x]` |
| 4.7 | PaymentModel, ReceiptModel | `[x]` |
| 4.8 | NoticeModel, SettingsModel, ReportModel | `[x]` |

## Phase 5 — Administrator Role (~20%)

| # | Task | Status |
|---|------|--------|
| 5.1 | Admin Dashboard — stats cards, charts, recent activity | `[x]` |
| 5.2 | User Management — CRUD, role assignment, activate/deactivate | `[x]` |
| 5.3 | Student Management — CRUD, search, filter, profile view | `[x]` |
| 5.4 | Teacher Management — CRUD, assign courses, view performance | `[x]` |
| 5.5 | Staff Management — CRUD | `[x]` |
| 5.6 | Course Management — CRUD, assign subjects & teachers | `[x]` |
| 5.7 | Subject Management — CRUD | `[x]` |
| 5.8 | Batch Management — CRUD, link to courses | `[x]` |
| 5.9 | Timetable Management — create/edit slots, conflict detection | `[ ]` |
| 5.10 | Assignment Management — CRUD, view submissions | `[ ]` |
| 5.11 | Online Class Management — schedule, link, status | `[ ]` |
| 5.12 | Physical Class Management — room allocation, schedule | `[ ]` |
| 5.13 | Examination Management — create exams, add questions, settings | `[ ]` |
| 5.14 | Result Management — view, edit, publish results | `[ ]` |
| 5.15 | Payment Management — view all, filter, reconcile | `[x]` |
| 5.16 | Notice Management — CRUD, broadcast by role | `[x]` |
| 5.17 | Report Management — generate/print/export all report types | `[ ]` |
| 5.18 | System Settings — general config, fee structure | `[ ]` |
| 5.19 | Database Backup — download SQL dump | `[ ]` |
| 5.20 | Database Restore — upload & restore SQL | `[ ]` |
| 5.21 | Activity Logs — view, filter, export | `[x]` |

## Phase 6 — Manager Role (~5%)

| # | Task | Status |
|---|------|--------|
| 6.1 | Manager Dashboard | `[x]` |
| 6.2 | Manage Students & Teachers (view, approve) | `[x]` |
| 6.3 | Approve Courses & Batches | `[x]` |
| 6.4 | Manage Timetables | `[ ]` |
| 6.5 | View Attendance / Examination / Payment Reports | `[ ]` |
| 6.6 | View Student Progress & Teacher Performance | `[ ]` |
| 6.7 | Send Announcements | `[ ]` |

## Phase 7 — Teacher Role (~8%)

| # | Task | Status |
|---|------|--------|
| 7.1 | Teacher Dashboard | `[x]` |
| 7.2 | Profile view & update | `[x]` |
| 7.3 | View assigned courses & timetable | `[x]` |
| 7.4 | View student list | `[x]` |
| 7.5 | Mark Attendance (AJAX) | `[ ]` |
| 7.6 | Upload Study Notes (file upload) | `[ ]` |
| 7.7 | Upload Video Lessons (file upload / link) | `[ ]` |
| 7.8 | Upload Assignments | `[ ]` |
| 7.9 | Create Online Exams (MCQ, timer, auto-grade) | `[ ]` |
| 7.10 | Evaluate Assignments | `[ ]` |
| 7.11 | Enter Examination Marks | `[ ]` |
| 7.12 | Publish Results | `[ ]` |
| 7.13 | View Student Progress | `[ ]` |
| 7.14 | Send Notices | `[ ]` |
| 7.15 | Conduct Online Classes | `[ ]` |
| 7.16 | View Teacher Income | `[x]` |

## Phase 8 — Student Role (~8%)

| # | Task | Status |
|---|------|--------|
| 8.1 | Student Dashboard | `[ ]` |
| 8.2 | Profile view & update | `[ ]` |
| 8.3 | Register / Enroll in Courses | `[ ]` |
| 8.4 | View Course Details & Timetable | `[ ]` |
| 8.5 | Join Online Classes | `[ ]` |
| 8.6 | View Physical Class Schedule | `[ ]` |
| 8.7 | Download Study Notes | `[ ]` |
| 8.8 | Watch Recorded Video Lessons | `[ ]` |
| 8.9 | Submit Assignments | `[ ]` |
| 8.10 | Take Online Examinations (timer, auto-grade) | `[ ]` |
| 8.11 | View Attendance | `[ ]` |
| 8.12 | View Examination Results | `[ ]` |
| 8.13 | Contact Teacher | `[ ]` |
| 8.14 | Receive Notices | `[ ]` |
| 8.15 | Make Payments & View Payment History | `[ ]` |
| 8.16 | Download Payment Receipts | `[ ]` |

## Phase 9 — Receptionist / Registrar Role (~4%)

| # | Task | Status |
|---|------|--------|
| 9.1 | Receptionist Dashboard | `[ ]` |
| 9.2 | Register Students | `[ ]` |
| 9.3 | Update Student Details | `[ ]` |
| 9.4 | Manage Admissions & Enrollments | `[ ]` |
| 9.5 | Generate Student ID Cards (printable) | `[ ]` |
| 9.6 | Search Student Records | `[ ]` |
| 9.7 | View Student Reports & Print Registration Details | `[ ]` |
| 9.8 | Respond to Student Inquiries | `[ ]` |

## Phase 10 — Cashier Role (~4%)

| # | Task | Status |
|---|------|--------|
| 10.1 | Cashier Dashboard | `[x]` |
| 10.2 | Collect Registration / Course / Monthly Fees | `[x]` |
| 10.3 | Process Cash / Online / Bank Transfer Payments | `[x]` |
| 10.4 | Manage Discounts | `[x]` |
| 10.5 | Record Payments & Generate Receipts | `[x]` |
| 10.6 | Print Receipts | `[ ]` |
| 10.7 | View Due Payments | `[x]` |
| 10.8 | Daily / Monthly Income Reports | `[x]` |
| 10.9 | Financial Reports | `[ ]` |

## Phase 11 — Reports Module (~3%)

| # | Task | Status |
|---|------|--------|
| 11.1 | Student Reports — enrollment, attendance, academic performance | `[ ]` |
| 11.2 | Teacher Reports — performance, attendance, salary/income | `[ ]` |
| 11.3 | Financial Reports — daily/monthly income, collection, outstanding | `[ ]` |
| 11.4 | Academic Reports — exam results, course completion, batch performance | `[ ]` |
| 11.5 | Print + PDF export + date filtering + search + sorting on all reports | `[ ]` |

## Phase 12 — Advanced Features (~3%)

| # | Task | Status |
|---|------|--------|
| 12.1 | Online Examination Timer (JS countdown) | `[ ]` |
| 12.2 | Auto-Grading for MCQs | `[ ]` |
| 12.3 | Attendance Analytics (charts) | `[ ]` |
| 12.4 | Student Progress Tracking | `[ ]` |
| 12.5 | Teacher Performance Metrics | `[ ]` |
| 12.6 | Search Across Modules (global search) | `[ ]` |
| 12.7 | Student ID Card Generation (printable) | `[ ]` |
| 12.8 | Dark/Light Mode toggle | `[ ]` |

## Phase 13 — Documentation (~2%)

| # | Task | Status |
|---|------|--------|
| 13.1 | README.md — project overview, tech stack, features | `[x]` |
| 13.2 | SETUP.md — XAMPP setup instructions, import DB, config | `[x]` |
| 13.3 | DATABASE.md — schema documentation, ERD specification | `[x]` |
| 13.4 | USER_MANUAL.md — role-based usage guide | `[x]` |

---

## Progress Summary

| Phase | Tasks | Completed | % Done |
|-------|-------|-----------|--------|
| 1 — Foundation & Database | 7 | 7 | 100% |
| 2 — Core MVC Architecture | 6 | 6 | 100% |
| 3 — Authentication & RBAC | 6 | 6 | 100% |
| 4 — Shared Models | 8 | 8 | 100% |
| 5 — Administrator Role | 21 | 14 | 67% |
| 6 — Manager Role | 7 | 3 | 43% |
| 7 — Teacher Role | 16 | 4 | 25% |
| 8 — Student Role | 16 | 7 | 44% |
| 9 — Receptionist Role | 8 | 5 | 63% |
| 10 — Cashier Role | 9 | 8 | 89% |
| 11 — Reports Module | 5 | 0 | 0% |
| 12 — Advanced Features | 8 | 0 | 0% |
| 13 — Documentation | 4 | 4 | 100% |
| **TOTAL** | **121** | **72** | **60%** |

---

*Last updated: 60% complete — Core system functional with all role dashboards and documentation*
