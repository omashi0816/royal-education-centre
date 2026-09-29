<!-- Receptionist dashboard showing admissions and inquiry summaries. -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-user-graduate"></i>
        </div>
        <div class="stat-info">
            <h4><?= $stats['total_students'] ?></h4>
            <p>Total Students</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h4><?= $stats['active_enrollments'] ?></h4>
            <p>Active Enrollments</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-envelope"></i>
        </div>
        <div class="stat-info">
            <h4><?= $stats['pending_inquiries'] ?></h4>
            <p>Pending Inquiries</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon danger">
            <i class="fas fa-user-plus"></i>
        </div>
        <div class="stat-info">
            <h4><?= $stats['today_registrations'] ?></h4>
            <p>Today's Registrations</p>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
    <a href="<?= BASE_URL ?>/receptionist/register" class="card" style="text-decoration: none; color: inherit; display: block;">
        <div class="card-body" style="text-align: center;">
            <i class="fas fa-user-plus" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 15px;"></i>
            <h4>Register Student</h4>
            <p style="color: var(--secondary-color);">Register a new student</p>
        </div>
    </a>
    
    <a href="<?= BASE_URL ?>/receptionist/enroll" class="card" style="text-decoration: none; color: inherit; display: block;">
        <div class="card-body" style="text-align: center;">
            <i class="fas fa-clipboard-list" style="font-size: 3rem; color: var(--success-color); margin-bottom: 15px;"></i>
            <h4>Enroll Student</h4>
            <p style="color: var(--secondary-color);">Enroll in a course</p>
        </div>
    </a>
    
    <a href="<?= BASE_URL ?>/receptionist/students" class="card" style="text-decoration: none; color: inherit; display: block;">
        <div class="card-body" style="text-align: center;">
            <i class="fas fa-users" style="font-size: 3rem; color: var(--warning-color); margin-bottom: 15px;"></i>
            <h4>Student Records</h4>
            <p style="color: var(--secondary-color);">View all students</p>
        </div>
    </a>
</div>
