<!-- Admin dashboard showing system statistics and recent activity. -->
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
            <i class="fas fa-id-badge"></i>
        </div>
        <div class="stat-info">
            <h4><?= $stats['total_staff'] ?></h4>
            <p>Total Staff</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon danger">
            <i class="fas fa-book"></i>
        </div>
        <div class="stat-info">
            <h4><?= $stats['total_courses'] ?></h4>
            <p>Total Courses</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-layer-group"></i>
        </div>
        <div class="stat-info">
            <h4><?= $stats['total_batches'] ?></h4>
            <p>Active Batches</p>
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
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-info">
            <h4><?= formatCurrency($stats['total_payments']) ?></h4>
            <p>Total Revenue</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon danger">
            <i class="fas fa-calendar"></i>
        </div>
        <div class="stat-info">
            <h4><?= formatCurrency($stats['this_month_payments']) ?></h4>
            <p>This Month</p>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <!-- Recent Payments -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Payments</h3>
            <a href="<?= BASE_URL ?>/admin/payments" class="btn btn-sm btn-secondary">View All</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentPayments as $payment): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($payment['student_code']) ?></strong><br>
                                <small><?= htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']) ?></small>
                            </td>
                            <td><?= formatCurrency($payment['final_amount']) ?></td>
                            <td><?= formatDate($payment['payment_date']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentPayments)): ?>
                        <tr>
                            <td colspan="3" style="text-align: center; color: var(--secondary-color);">No recent payments</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Upcoming Exams -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Upcoming Exams</h3>
            <a href="<?= BASE_URL ?>/admin/exams" class="btn btn-sm btn-secondary">View All</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Batch</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcomingExams as $exam): ?>
                        <tr>
                            <td><?= htmlspecialchars($exam['subject_name']) ?></td>
                            <td><?= htmlspecialchars($exam['batch_name']) ?></td>
                            <td><?= formatDateTime($exam['exam_date']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($upcomingExams)): ?>
                        <tr>
                            <td colspan="3" style="text-align: center; color: var(--secondary-color);">No upcoming exams</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px;">
    <!-- Recent Notices -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Notices</h3>
            <a href="<?= BASE_URL ?>/admin/notices" class="btn btn-sm btn-secondary">View All</a>
        </div>
        <div class="card-body">
            <?php foreach ($recentNotices as $notice): ?>
            <div style="padding: 15px 0; border-bottom: 1px solid #e2e8f0;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 5px;">
                    <strong><?= htmlspecialchars($notice['title']) ?></strong>
                    <?php if ($notice['is_pinned']): ?>
                    <span class="badge badge-warning"><i class="fas fa-thumbtack"></i> Pinned</span>
                    <?php endif; ?>
                </div>
                <p style="color: var(--secondary-color); font-size: 0.875rem; margin-bottom: 5px;">
                    <?= truncate(htmlspecialchars($notice['content']), 100) ?>
                </p>
                <small style="color: var(--secondary-color);">
                    By <?= htmlspecialchars($notice['posted_by_name']) ?> · <?= formatDate($notice['created_at']) ?>
                </small>
            </div>
            <?php endforeach; ?>
            <?php if (empty($recentNotices)): ?>
            <p style="color: var(--secondary-color); text-align: center;">No notices</p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Activity</h3>
            <a href="<?= BASE_URL ?>/admin/logs" class="btn btn-sm btn-secondary">View All</a>
        </div>
        <div class="card-body">
            <?php foreach ($recentActivities as $activity): ?>
            <div style="padding: 10px 0; border-bottom: 1px solid #e2e8f0;">
                <div style="display: flex; gap: 10px;">
                    <div style="width: 8px; height: 8px; background: var(--primary-color); border-radius: 50%; margin-top: 6px;"></div>
                    <div>
                        <p style="margin-bottom: 3px;">
                            <strong><?= htmlspecialchars($activity['username'] ?? 'System') ?></strong>
                            <?= htmlspecialchars($activity['action']) ?> in 
                            <strong><?= htmlspecialchars($activity['module']) ?></strong>
                        </p>
                        <?php if ($activity['description']): ?>
                        <p style="color: var(--secondary-color); font-size: 0.875rem;">
                            <?= htmlspecialchars($activity['description']) ?>
                        </p>
                        <?php endif; ?>
                        <small style="color: var(--secondary-color);"><?= formatDateTime($activity['created_at']) ?></small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($recentActivities)): ?>
            <p style="color: var(--secondary-color); text-align: center;">No recent activity</p>
            <?php endif; ?>
        </div>
    </div>
</div>
