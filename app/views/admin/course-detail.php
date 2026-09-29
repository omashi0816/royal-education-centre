<!-- Admin page for viewing course details. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Course Details</h3>
        <a href="<?= BASE_URL ?>/admin/courses" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-bottom: 30px;">
            <div><strong>Code:</strong> <?= htmlspecialchars($course['code']) ?></div>
            <div><strong>Name:</strong> <?= htmlspecialchars($course['name']) ?></div>
            <div><strong>Course Fee:</strong> <?= formatCurrency($course['course_fee']) ?></div>
            <div><strong>Registration Fee:</strong> <?= formatCurrency($course['registration_fee']) ?></div>
            <div><strong>Duration:</strong> <?= $course['duration_months'] ?> months</div>
            <div><strong>Status:</strong> <span class="badge badge-info"><?= ucfirst($course['status']) ?></span></div>
            <div style="grid-column: span 2;"><strong>Description:</strong> <?= htmlspecialchars($course['description'] ?? 'N/A') ?></div>
        </div>
        <h4>Subjects</h4>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Code</th><th>Name</th></tr></thead>
                <tbody>
                    <?php foreach ($subjects as $subject): ?>
                    <tr><td><strong><?= htmlspecialchars($subject['code']) ?></strong></td><td><?= htmlspecialchars($subject['name']) ?></td></tr>
                    <?php endforeach; ?>
                    <?php if (empty($subjects)): ?>
                    <tr><td colspan="2" style="text-align:center;color:var(--secondary-color);">No subjects assigned</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <h4 style="margin-top: 30px;">Batches</h4>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Batch Name</th><th>Start Date</th><th>End Date</th><th>Students</th><th>Status</th></tr></thead>
                <tbody>
                    <?php foreach ($batches as $batch): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($batch['batch_name']) ?></strong></td>
                        <td><?= formatDate($batch['start_date']) ?></td>
                        <td><?= formatDate($batch['end_date']) ?></td>
                        <td><?= $batch['student_count'] ?? 0 ?></td>
                        <td><span class="badge badge-<?= $batch['status'] === 'active' ? 'success' : 'info' ?>"><?= ucfirst($batch['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($batches)): ?>
                    <tr><td colspan="5" style="text-align:center;color:var(--secondary-color);">No batches</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
