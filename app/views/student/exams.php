<!-- Student page for viewing upcoming and completed examinations. -->
<div class="card">
    <div class="card-header"><h3 class="card-title">My Exams</h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Exam Name</th><th>Batch</th><th>Subject</th><th>Date</th><th>Total Marks</th><th>Status</th></tr></thead>
                <tbody>
                    <?php foreach ($exams as $exam): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($exam['title']) ?></strong></td>
                        <td><?= htmlspecialchars($exam['batch_name']) ?></td>
                        <td><?= htmlspecialchars($exam['subject_name']) ?></td>
                        <td><?= formatDateTime($exam['exam_date']) ?></td>
                        <td><?= $exam['total_marks'] ?></td>
                        <td>
                            <?php if ($exam['result_status']): ?>
                            <span class="badge badge-<?= $exam['result_status'] === 'passed' ? 'success' : 'danger' ?>">
                                <?= ucfirst($exam['result_status']) ?> (<?= $exam['grade'] ?? 'N/A' ?>)
                            </span>
                            <?php else: ?>
                            <span class="badge badge-info">Pending</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($exams)): ?>
                    <tr><td colspan="6" style="text-align:center;color:var(--secondary-color);">No exams scheduled</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
