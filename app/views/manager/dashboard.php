<!-- Manager dashboard showing operational summaries. -->
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
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div class="stat-info">
            <h4><?= $stats['total_teachers'] ?></h4>
            <p>Total Teachers</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-book"></i>
        </div>
        <div class="stat-info">
            <h4><?= $stats['active_courses'] ?></h4>
            <p>Active Courses</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon danger">
            <i class="fas fa-layer-group"></i>
        </div>
        <div class="stat-info">
            <h4><?= $stats['active_batches'] ?></h4>
            <p>Active Batches</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <h4><?= $stats['pending_approvals'] ?></h4>
            <p>Pending Approvals</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-info">
            <h4><?= formatCurrency($stats['monthly_revenue']) ?></h4>
            <p>Monthly Revenue</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Recent Enrollments</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Batch</th>
                        <th>Enrolled Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentEnrollments as $enrollment): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($enrollment['student_code']) ?></strong><br>
                            <small><?= htmlspecialchars($enrollment['first_name'] . ' ' . $enrollment['last_name']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($enrollment['batch_name']) ?></td>
                        <td><?= formatDate($enrollment['enrolled_date']) ?></td>
                        <td>
                            <span class="badge badge-<?= $enrollment['status'] === 'active' ? 'success' : 'warning' ?>">
                                <?= ucfirst($enrollment['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentEnrollments)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--secondary-color);">No recent enrollments</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
