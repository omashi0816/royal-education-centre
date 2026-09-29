<!-- Admin page for viewing student details. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Student Details</h3>
        <a href="<?= BASE_URL ?>/admin/students" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-bottom: 30px;">
            <div><strong>Student Code:</strong> <?= htmlspecialchars($student['student_code']) ?></div>
            <div><strong>Name:</strong> <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></div>
            <div><strong>Email:</strong> <?= htmlspecialchars($student['email']) ?></div>
            <div><strong>Phone:</strong> <?= htmlspecialchars($student['phone'] ?? '-') ?></div>
            <div><strong>Gender:</strong> <?= ucfirst($student['gender'] ?? '-') ?></div>
            <div><strong>Date of Birth:</strong> <?= formatDate($student['date_of_birth'] ?? '') ?></div>
            <div><strong>Address:</strong> <?= htmlspecialchars($student['address'] ?? '-') ?></div>
            <div><strong>Guardian:</strong> <?= htmlspecialchars($student['guardian_name'] ?? '-') ?> (<?= htmlspecialchars($student['guardian_phone'] ?? '-') ?>)</div>
            <div><strong>School:</strong> <?= htmlspecialchars($student['school'] ?? '-') ?></div>
            <div><strong>Status:</strong> <span class="badge badge-<?= $student['status'] === 'active' ? 'success' : 'danger' ?>"><?= ucfirst($student['status']) ?></span></div>
        </div>
        <h4>Enrollments</h4>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr><th>Batch</th><th>Course</th><th>Enrolled Date</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($enrollments as $enr): ?>
                    <tr>
                        <td><?= htmlspecialchars($enr['batch_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($enr['course_name'] ?? 'N/A') ?></td>
                        <td><?= formatDate($enr['enrolled_date']) ?></td>
                        <td><span class="badge badge-<?= $enr['status'] === 'active' ? 'success' : 'info' ?>"><?= ucfirst($enr['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($enrollments)): ?>
                    <tr><td colspan="4" style="text-align:center;color:var(--secondary-color);">No enrollments</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
