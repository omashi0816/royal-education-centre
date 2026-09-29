<!DOCTYPE html>
<!-- Shared application layout containing navigation, header, and page content. -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?>Royal Education Center</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/style.css">
</head>
<body>
    <?php if (isLoggedIn()): ?>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="<?= BASE_URL ?>/dashboard" class="sidebar-brand">
                <i class="fas fa-graduation-cap"></i>
                <span>Royal Edu</span>
            </a>
        </div>
        
        <nav class="sidebar-nav">
            <?php $role = currentUserRole(); ?>
            
            <?php if ($role === 'admin'): ?>
                <div class="nav-section">Main</div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/dashboard" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard' ? 'active' : '' ?>">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                
                <div class="nav-section">Management</div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/users" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/students" class="nav-link">
                        <i class="fas fa-user-graduate"></i>
                        <span>Students</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/teachers" class="nav-link">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Teachers</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/staff" class="nav-link">
                        <i class="fas fa-id-badge"></i>
                        <span>Staff</span>
                    </a>
                </div>
                
                <div class="nav-section">Academic</div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/courses" class="nav-link">
                        <i class="fas fa-book"></i>
                        <span>Courses</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/subjects" class="nav-link">
                        <i class="fas fa-book-open"></i>
                        <span>Subjects</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/batches" class="nav-link">
                        <i class="fas fa-layer-group"></i>
                        <span>Batches</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/timetables" class="nav-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Timetables</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/attendance" class="nav-link">
                        <i class="fas fa-clipboard-check"></i>
                        <span>Attendance</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/assignments" class="nav-link">
                        <i class="fas fa-tasks"></i>
                        <span>Assignments</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/exams" class="nav-link">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Examinations</span>
                    </a>
                </div>
                
                <div class="nav-section">Finance</div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/payments" class="nav-link">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Payments</span>
                    </a>
                </div>
                
                <div class="nav-section">Communication</div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/notices" class="nav-link">
                        <i class="fas fa-bullhorn"></i>
                        <span>Notices</span>
                    </a>
                </div>
                
                <div class="nav-section">System</div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/reports" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reports</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/settings" class="nav-link">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/logs" class="nav-link">
                        <i class="fas fa-history"></i>
                        <span>Activity Logs</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/backup" class="nav-link">
                        <i class="fas fa-database"></i>
                        <span>Backup</span>
                    </a>
                </div>
                
            <?php elseif ($role === 'manager'): ?>
                <div class="nav-section">Main</div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/dashboard" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                
                <div class="nav-section">Operations</div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/manager/students" class="nav-link">
                        <i class="fas fa-user-graduate"></i>
                        <span>Students</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/manager/teachers" class="nav-link">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Teachers</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/manager/users" class="nav-link">
                        <i class="fas fa-users-cog"></i>
                        <span>Staff Users</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/manager/approvals" class="nav-link">
                        <i class="fas fa-check-circle"></i>
                        <span>Approvals</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/manager/timetables" class="nav-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Timetables</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/manager/reports" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reports</span>
                    </a>
                </div>
                
            <?php elseif ($role === 'teacher'): ?>
                <div class="nav-section">Main</div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/dashboard" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                
                <div class="nav-section">Teaching</div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/teacher/courses" class="nav-link">
                        <i class="fas fa-book"></i>
                        <span>My Courses</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/teacher/timetable" class="nav-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Timetable</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/teacher/students" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>My Students</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/teacher/payments" class="nav-link">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Student Payments</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/teacher/attendance" class="nav-link">
                        <i class="fas fa-clipboard-check"></i>
                        <span>Attendance</span>
                    </a>
                </div>
                
                <div class="nav-section">Content</div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/teacher/notes" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        <span>Study Notes</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/teacher/assignments" class="nav-link">
                        <i class="fas fa-tasks"></i>
                        <span>Assignments</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/teacher/results" class="nav-link">
                        <i class="fas fa-pen"></i>
                        <span>Enter Results</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/teacher/exams" class="nav-link">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Exams</span>
                    </a>
                </div>
                
                <div class="nav-section">Reports</div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/teacher/income" class="nav-link">
                        <i class="fas fa-wallet"></i>
                        <span>My Income</span>
                    </a>
                </div>
                
            <?php elseif ($role === 'student'): ?>
                <div class="nav-section">Main</div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/dashboard" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                
                <div class="nav-section">Learning</div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/student/courses" class="nav-link">
                        <i class="fas fa-book"></i>
                        <span>My Courses</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/student/timetable" class="nav-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Timetable</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/student/classes" class="nav-link">
                        <i class="fas fa-video"></i>
                        <span>Online Classes</span>
                    </a>
                </div>
                
                <div class="nav-section">Materials</div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/student/notes" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        <span>Study Notes</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/student/assignments" class="nav-link">
                        <i class="fas fa-tasks"></i>
                        <span>Assignments</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/student/exams" class="nav-link">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Exams</span>
                    </a>
                </div>
                
                <div class="nav-section">Records</div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/student/attendance" class="nav-link">
                        <i class="fas fa-clipboard-check"></i>
                        <span>Attendance</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/student/results" class="nav-link">
                        <i class="fas fa-chart-line"></i>
                        <span>Results</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/student/payments" class="nav-link">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Payments</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/student/checkout" class="nav-link">
                        <i class="fas fa-credit-card"></i>
                        <span>Checkout</span>
                    </a>
                </div>
                
            <?php elseif ($role === 'receptionist'): ?>
                <div class="nav-section">Main</div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/dashboard" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                
                <div class="nav-section">Admissions</div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/receptionist/register" class="nav-link">
                        <i class="fas fa-user-plus"></i>
                        <span>Register Student</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/receptionist/enroll" class="nav-link">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Enrollments</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/receptionist/students" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Student Records</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/receptionist/inquiries" class="nav-link">
                        <i class="fas fa-envelope"></i>
                        <span>Inquiries</span>
                    </a>
                </div>
                
            <?php elseif ($role === 'cashier'): ?>
                <div class="nav-section">Main</div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/dashboard" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                
                <div class="nav-section">Payments</div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/cashier/collect" class="nav-link">
                        <i class="fas fa-cash-register"></i>
                        <span>Collect Payment</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/cashier/payments" class="nav-link">
                        <i class="fas fa-list"></i>
                        <span>All Payments</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/cashier/due" class="nav-link">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Due Payments</span>
                    </a>
                </div>
                
                <div class="nav-section">Reports</div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/cashier/reports" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>Financial Reports</span>
                    </a>
                </div>
            <?php endif; ?>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content" id="main-content">
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <button class="toggle-sidebar" id="toggle-sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search...">
                </div>
            </div>
            
            <div class="header-right">
                <div class="user-dropdown">
                    <button class="user-toggle" id="user-toggle">
                        <img src="<?= ASSETS_URL ?>/img/default-avatar.svg" alt="User profile image" class="user-avatar">
                        <div class="user-info">
                            <div class="user-name"><?= currentUserName() ?></div>
                            <div class="user-role"><?= ucfirst(currentUserRole()) ?></div>
                        </div>
                        <i class="fas fa-chevron-down" style="font-size: 0.75rem; color: var(--secondary-color);"></i>
                    </button>
                    <div class="dropdown-menu" id="user-dropdown-menu">
                        <a href="<?= BASE_URL ?>/dashboard/profile" class="dropdown-item">
                            <i class="fas fa-user"></i> My Profile
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="<?= BASE_URL ?>/logout" class="dropdown-item" style="color: var(--danger-color);">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Content -->
        <div class="content">
            <?php if (hasFlash('success')): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?= getFlash('success') ?></span>
                </div>
            <?php endif; ?>
            
            <?php if (hasFlash('error')): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= getFlash('error') ?></span>
                </div>
            <?php endif; ?>
            
            <?php if (hasFlash('warning')): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span><?= getFlash('warning') ?></span>
                </div>
            <?php endif; ?>
            
            <?php if (hasFlash('info')): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <span><?= getFlash('info') ?></span>
                </div>
            <?php endif; ?>
            
            <?= $content ?>
        </div>
    </main>
    <?php else: ?>
        <?= $content ?>
    <?php endif; ?>
    
    <script src="<?= ASSETS_URL ?>/js/app.js"></script>
</body>
</html>
