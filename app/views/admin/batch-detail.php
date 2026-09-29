<!-- Admin page for viewing batch details. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Batch Details</h3>
        <a href="<?= BASE_URL ?>/admin/batches" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-bottom: 30px;">
            <div><strong>Batch Name:</strong> <?= htmlspecialchars($batch['batch_name']) ?></div>
            <div><strong>Course:</strong> <?= htmlspecialchars($batch['course_name']) ?></div>
            <div><strong>Start Date:</strong> <?= formatDate($batch['start_date']) ?></div>
            <div><strong>End Date:</strong> <?= formatDate($batch['end_date']) ?></div>
            <div><strong>Max Students:</strong> <?= $batch['max_students'] ?></div>
            <div><strong>Status:</strong> <span class="badge badge-info"><?= ucfirst($batch['status']) ?></span></div>
        </div>
        <h4>Enrolled Students</h4>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Code</th><th>Name</th><th>Enrolled Date</th><th>Status</th></tr></thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($student['student_code']) ?></strong></td>
                        <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                        <td><?= formatDate($student['enrolled_date']) ?></td>
                        <td><span class="badge badge-<?= $student['status'] === 'active' ? 'success' : 'info' ?>"><?= ucfirst($student['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($students)): ?>
                    <tr><td colspan="4" style="text-align:center;color:var(--secondary-color);">No students enrolled</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
