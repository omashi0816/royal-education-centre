<!-- Teacher dashboard showing teaching and income summaries. -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-layer-group"></i>
        </div>
        <div class="stat-info">
            <h4><?= $stats['assigned_batches'] ?></h4>
            <p>Assigned Batches</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h4><?= $stats['total_students'] ?></h4>
            <p>Total Students</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-tasks"></i>
        </div>
        <div class="stat-info">
            <h4><?= $stats['active_assignments'] ?></h4>
            <p>Active Assignments</p>
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
</div>

<div class="card" style="margin-bottom: 20px;">
    <div class="card-header">
        <h3 class="card-title">Exam Marks</h3>
        <div style="display: flex; gap: 8px;">
            <a href="<?= BASE_URL ?>/teacher/results" class="btn btn-sm btn-primary"><i class="fas fa-pen"></i> Enter Results</a>
            <a href="<?= BASE_URL ?>/teacher/exams" class="btn btn-sm btn-secondary"><i class="fas fa-plus"></i> Create Exam</a>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($exams)): ?>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Exam</th><th>Batch</th><th>Date</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach ($exams as $exam): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($exam['title']) ?></strong></td>
                        <td><?= htmlspecialchars($exam['batch_name']) ?></td>
                        <td><?= formatDateTime($exam['exam_date']) ?></td>
                        <td><a href="<?= BASE_URL ?>/teacher/mark-results/<?= $exam['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-pen"></i> Enter Marks</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p style="margin: 0; color: var(--secondary-color);">No exams have been created for your account yet. Create an exam first, then its <strong>Enter Marks</strong> button will appear here.</p>
        <?php endif; ?>
    </div>
</div>

<div class="card" style="margin-bottom: 20px;">
    <div class="card-header">
        <h3 class="card-title">Assignment Results</h3>
        <a href="<?= BASE_URL ?>/teacher/results" class="btn btn-sm btn-primary"><i class="fas fa-pen"></i> Enter Assignment Results</a>
    </div>
    <div class="card-body">
        <?php if (!empty($assignments)): ?>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Assignment</th><th>Batch</th><th>Due Date</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach ($assignments as $assignment): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($assignment['title']) ?></strong></td>
                        <td><?= htmlspecialchars($assignment['batch_name'] ?? '-') ?></td>
                        <td><?= formatDateTime($assignment['due_date']) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/teacher/grade-assignment/<?= (int) $assignment['id'] ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-pen"></i> Upload Results
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p style="margin: 0; color: var(--secondary-color);">No assignments have been created for your account yet.</p>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Upcoming Classes</h3>
        <div style="display: flex; gap: 8px;">
            <a href="<?= BASE_URL ?>/teacher/exams" class="btn btn-sm btn-primary">
                <i class="fas fa-pen"></i> Enter Marks
            </a>
            <a href="<?= BASE_URL ?>/teacher/attendance" class="btn btn-sm btn-secondary">
                <i class="fas fa-clipboard-check"></i> Attendance
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Subject</th>
                        <th>Batch</th>
                        <th>Time</th>
                        <th>Room</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($upcomingClasses as $class): ?>
                    <tr>
                        <td><?= ucfirst($class['day_of_week']) ?></td>
                        <td><?= htmlspecialchars($class['subject_name']) ?></td>
                        <td><?= htmlspecialchars($class['batch_name']) ?></td>
                        <td><?= date('h:i A', strtotime($class['start_time'])) ?> - <?= date('h:i A', strtotime($class['end_time'])) ?></td>
                        <td><?= htmlspecialchars($class['room'] ?? 'TBD') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($upcomingClasses)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--secondary-color);">No classes scheduled</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
