<!-- Admin page for managing student and teacher attendance. -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-header"><h3 class="card-title">Mark Teacher Attendance</h3></div>
    <div class="card-body">
        <form method="post" action="<?= BASE_URL ?>/admin/save-teacher-attendance" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; align-items: end;">
            <?= csrfField() ?>
            <div class="form-group" style="margin: 0;">
                <label>Teacher</label>
                <select name="teacher_id" class="form-select" required>
                    <option value="">Select Teacher</option>
                    <?php foreach ($teacherList as $teacher): ?>
                    <option value="<?= (int) $teacher['id'] ?>"><?= htmlspecialchars(($teacher['teacher_code'] ?? '') . ' - ' . $teacher['first_name'] . ' ' . $teacher['last_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin: 0;">
                <label>Date</label>
                <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group" style="margin: 0;">
                <label>Status</label>
                <select name="status" class="form-select" required>
                    <option value="present">Present</option>
                    <option value="absent">Absent</option>
                    <option value="late">Late</option>
                    <option value="excused">Excused</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Attendance</button>
        </form>
    </div>
</div>

<div class="card" style="margin-bottom: 20px;">
    <div class="card-header"><h3 class="card-title">Student Attendance</h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Date</th><th>Student</th><th>Batch</th><th>Status</th></tr></thead>
                <tbody>
                    <?php foreach ($students as $record): ?>
                    <tr>
                        <td><?= formatDate($record['date']) ?></td>
                        <td><strong><?= htmlspecialchars($record['student_code']) ?></strong><br><?= htmlspecialchars($record['first_name'] . ' ' . $record['last_name']) ?></td>
                        <td><?= htmlspecialchars($record['batch_name']) ?></td>
                        <td><span class="badge badge-<?= $record['status'] === 'present' ? 'success' : ($record['status'] === 'absent' ? 'danger' : 'warning') ?>"><?= ucfirst($record['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($students)): ?>
                    <tr><td colspan="4" style="text-align:center;color:var(--secondary-color);">No student attendance records</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3 class="card-title">Teacher Attendance</h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Date</th><th>Teacher</th><th>Status</th></tr></thead>
                <tbody>
                    <?php foreach ($teachers as $record): ?>
                    <tr>
                        <td><?= formatDate($record['date']) ?></td>
                        <td><strong><?= htmlspecialchars($record['teacher_code']) ?></strong><br><?= htmlspecialchars($record['first_name'] . ' ' . $record['last_name']) ?></td>
                        <td><span class="badge badge-<?= $record['status'] === 'present' ? 'success' : ($record['status'] === 'absent' ? 'danger' : 'warning') ?>"><?= ucfirst($record['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($teachers)): ?>
                    <tr><td colspan="3" style="text-align:center;color:var(--secondary-color);">No teacher attendance records</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
