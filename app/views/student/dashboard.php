<!-- Student dashboard showing learning and payment summaries. -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-header">
        <h3 class="card-title">Student Details</h3>
    </div>
    <div class="card-body" style="display: flex; gap: 28px; flex-wrap: wrap;">
        <div>
            <small style="color: var(--secondary-color);">Student ID</small>
            <div style="font-size: 1.25rem; font-weight: 700;"><?= htmlspecialchars($student['id'] ?? '-') ?></div>
        </div>
        <div>
            <small style="color: var(--secondary-color);">Student Code</small>
            <div style="font-size: 1.25rem; font-weight: 700;"><?= htmlspecialchars($student['student_code'] ?? '-') ?></div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom: 20px;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;">
        <h3 class="card-title">Quick Actions</h3>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="<?= BASE_URL ?>/student/payments" class="btn btn-primary">
                <i class="fas fa-credit-card"></i> Pay by Online Transfer
            </a>
            <a href="tel:+94112345678" class="btn btn-success" title="Call Education Center: +94 11 234 5678">
                <i class="fas fa-phone"></i> Call Center: +94 11 234 5678
            </a>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom: 20px;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;">
        <h3 class="card-title">Available Classes & Courses</h3>
        <a href="<?= BASE_URL ?>/student/payments" class="btn btn-sm btn-primary"><i class="fas fa-credit-card"></i> Payments & Status</a>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            <?php foreach ($availableCourses as $course): ?>
            <div style="padding: 16px; border: 1px solid #e2e8f0; border-radius: 8px;">
                <strong><?= htmlspecialchars($course['course_name']) ?></strong>
                <p style="margin: 6px 0; color: var(--secondary-color);">Class: <?= htmlspecialchars($course['batch_name']) ?></p>
                <p style="margin: 6px 0;">Monthly Fee: <?= formatCurrency($course['monthly_fee']) ?></p>
                <?php if (in_array((int) $course['batch_id'], $registeredBatchIds, true)): ?>
                    <span class="badge badge-success"><i class="fas fa-check"></i> Registered</span>
                <?php else: ?>
                    <form method="post" action="<?= BASE_URL ?>/student/register-course/<?= (int) $course['batch_id'] ?>" style="margin-top: 10px;">
                        <?= csrfField() ?>
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-user-plus"></i> Register</button>
                    </form>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            <?php if (empty($availableCourses)): ?>
            <p style="color: var(--secondary-color);">No active classes or courses available.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-book"></i>
        </div>
        <div class="stat-info">
            <h4><?= $stats['enrolled_courses'] ?></h4>
            <p>Enrolled Courses</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-tasks"></i>
        </div>
        <div class="stat-info">
            <h4><?= $stats['pending_assignments'] ?></h4>
            <p>Pending Assignments</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon danger">
            <i class="fas fa-clipboard-list"></i>
        </div>
        <div class="stat-info">
            <h4><?= $stats['upcoming_exams'] ?></h4>
            <p>Upcoming Exams</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-info">
            <h4><?= $stats['total_payments'] ?></h4>
            <p>Total Payments</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-info">
            <h4><?= $stats['attendance_percentage'] ?>%</h4>
            <p>Attendance</p>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom: 20px;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;">
        <h3 class="card-title">Recent Attendance</h3>
        <a href="<?= BASE_URL ?>/student/attendance" class="btn btn-sm btn-secondary"><i class="fas fa-calendar-check"></i> View All</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Attendance Date</th><th>Class / Batch</th><th>Attendance</th></tr></thead>
                <tbody>
                    <?php foreach ($attendanceRecords as $record): ?>
                    <tr>
                        <td><?= formatDate($record['date']) ?></td>
                        <td><?= htmlspecialchars($record['batch_name'] ?? '-') ?></td>
                        <td><span class="badge badge-<?= $record['status'] === 'present' ? 'success' : ($record['status'] === 'absent' ? 'danger' : 'warning') ?>"><?= ucfirst($record['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($attendanceRecords)): ?>
                    <tr><td colspan="3" style="text-align:center;color:var(--secondary-color);">No attendance records yet</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">My Enrollments</h3>
        </div>
        <div class="card-body">
            <?php foreach ($enrollments as $enrollment): ?>
            <div style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                <strong><?= htmlspecialchars($enrollment['course_name']) ?></strong>
                <p style="color: var(--secondary-color); font-size: 0.875rem;"><?= htmlspecialchars($enrollment['batch_name']) ?></p>
                <span class="badge badge-<?= $enrollment['enrollment_status'] === 'active' ? 'success' : 'warning' ?>">
                    <?= ucfirst($enrollment['enrollment_status']) ?>
                </span>
            </div>
            <?php endforeach; ?>
            <?php if (empty($enrollments)): ?>
            <p style="color: var(--secondary-color);">No enrollments</p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Notices</h3>
        </div>
        <div class="card-body">
            <?php foreach ($notices as $notice): ?>
            <div style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                <strong><?= htmlspecialchars($notice['title']) ?></strong>
                <?php if ($notice['is_pinned']): ?>
                <span class="badge badge-warning"><i class="fas fa-thumbtack"></i></span>
                <?php endif; ?>
                <p style="color: var(--secondary-color); font-size: 0.875rem;"><?= truncate(htmlspecialchars($notice['content']), 80) ?></p>
                <small><?= formatDate($notice['created_at']) ?></small>
            </div>
            <?php endforeach; ?>
            <?php if (empty($notices)): ?>
            <p style="color: var(--secondary-color);">No notices</p>
            <?php endif; ?>
        </div>
    </div>
</div>
