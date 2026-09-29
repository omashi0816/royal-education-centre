<!-- Admin page for viewing teacher details. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Teacher Details</h3>
        <a href="<?= BASE_URL ?>/admin/teachers" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-bottom: 30px;">
            <div><strong>Teacher Code:</strong> <?= htmlspecialchars($teacher['teacher_code']) ?></div>
            <div><strong>Name:</strong> <?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?></div>
            <div><strong>Email:</strong> <?= htmlspecialchars($teacher['email']) ?></div>
            <div><strong>Phone:</strong> <?= htmlspecialchars($teacher['phone'] ?? '-') ?></div>
            <div><strong>Specialization:</strong> <?= htmlspecialchars($teacher['specialization'] ?? '-') ?></div>
            <div><strong>Experience:</strong> <?= $teacher['experience_years'] ?? 0 ?> years</div>
            <div><strong>Qualification:</strong> <?= htmlspecialchars($teacher['qualification'] ?? '-') ?></div>
            <div><strong>Status:</strong> <span class="badge badge-<?= $teacher['status'] === 'active' ? 'success' : 'danger' ?>"><?= ucfirst($teacher['status']) ?></span></div>
        </div>
        <h4>Assigned Batches</h4>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Batch</th><th>Course</th><th>Subject</th><th>Day</th><th>Time</th></tr></thead>
                <tbody>
                    <?php foreach ($batches as $batch): ?>
                    <tr>
                        <td><?= htmlspecialchars($batch['batch_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($batch['course_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($batch['subject_name'] ?? 'N/A') ?></td>
                        <td><?= ucfirst($batch['day_of_week'] ?? '-') ?></td>
                        <td><?= isset($batch['start_time']) ? date('h:i A', strtotime($batch['start_time'])) : '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($batches)): ?>
                    <tr><td colspan="5" style="text-align:center;color:var(--secondary-color);">No batches assigned</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
