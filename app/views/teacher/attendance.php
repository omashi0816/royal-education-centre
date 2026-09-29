<!-- Teacher page for recording and reviewing attendance. -->
<div class="card">
    <div class="card-header"><h3 class="card-title">Mark Attendance</h3></div>
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>/teacher/attendance" style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
            <select name="batch_id" class="form-select" style="max-width: 250px;" onchange="this.form.submit()">
                <option value="">Select Batch</option>
                <?php foreach ($batches as $batch): ?>
                <option value="<?= $batch['id'] ?? '' ?>" <?= $selectedBatch == ($batch['id'] ?? '') ? 'selected' : '' ?>><?= htmlspecialchars($batch['batch_name'] ?? $batch['name'] ?? '') ?></option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($date) ?>" style="max-width: 180px;">
            <button type="submit" class="btn btn-secondary"><i class="fas fa-filter"></i> Load</button>
        </form>
        
        <?php if (!empty($students)): ?>
        <form method="POST" action="<?= BASE_URL ?>/teacher/save-attendance">
            <?= csrfField() ?>
            <input type="hidden" name="batch_id" value="<?= htmlspecialchars($selectedBatch) ?>">
            <input type="hidden" name="attendance_date" value="<?= htmlspecialchars($date) ?>">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($student['student_code']) ?></strong></td>
                            <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                            <td>
                                <select name="status[<?= $student['id'] ?>]" class="form-select" style="max-width: 150px;">
                                    <option value="present" <?= ($student['attendance_status'] ?? '') === 'present' ? 'selected' : '' ?>>Present</option>
                                    <option value="absent" <?= ($student['attendance_status'] ?? '') === 'absent' ? 'selected' : '' ?>>Absent</option>
                                    <option value="late" <?= ($student['attendance_status'] ?? '') === 'late' ? 'selected' : '' ?>>Late</option>
                                    <option value="excused" <?= ($student['attendance_status'] ?? '') === 'excused' ? 'selected' : '' ?>>Excused</option>
                                </select>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Attendance</button>
        </form>
        <?php else: ?>
        <p style="color: var(--secondary-color); text-align: center;">Please select a batch to mark attendance.</p>
        <?php endif; ?>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header"><h3 class="card-title">Student Attendance History</h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Date</th><th>Student</th><th>Batch</th><th>Status</th></tr></thead>
                <tbody>
                    <?php foreach ($attendanceHistory as $record): ?>
                    <tr>
                        <td><?= formatDate($record['date']) ?></td>
                        <td><strong><?= htmlspecialchars($record['student_code']) ?></strong><br><?= htmlspecialchars($record['first_name'] . ' ' . $record['last_name']) ?></td>
                        <td><?= htmlspecialchars($record['batch_name']) ?></td>
                        <td><span class="badge badge-<?= $record['status'] === 'present' ? 'success' : ($record['status'] === 'absent' ? 'danger' : 'warning') ?>"><?= ucfirst($record['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($attendanceHistory)): ?>
                    <tr><td colspan="4" style="text-align:center;color:var(--secondary-color);">No student attendance records</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
