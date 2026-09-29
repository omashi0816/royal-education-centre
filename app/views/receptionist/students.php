<!-- Receptionist page for reviewing student records. -->
<div class="card">
    <div class="card-header"><h3 class="card-title">Student Records</h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Code</th><th>Name</th><th>Email</th><th>Phone</th><th>Status</th></tr></thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($student['student_code']) ?></strong></td>
                        <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                        <td><?= htmlspecialchars($student['email']) ?></td>
                        <td><?= htmlspecialchars($student['phone'] ?? '-') ?></td>
                        <td><span class="badge badge-<?= $student['status'] === 'active' ? 'success' : 'danger' ?>"><?= ucfirst($student['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($students)): ?>
                    <tr><td colspan="5" style="text-align:center;color:var(--secondary-color);">No students found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
