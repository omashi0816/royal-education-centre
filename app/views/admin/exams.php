<!-- Admin page for managing examinations. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Examination Management</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Exam Name</th>
                        <th>Batch</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Date</th>
                        <th>Total Marks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exams as $exam): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($exam['title']) ?></strong></td>
                        <td><?= htmlspecialchars($exam['batch_name']) ?></td>
                        <td><?= htmlspecialchars($exam['subject_name']) ?></td>
                        <td><?= htmlspecialchars($exam['first_name'] . ' ' . $exam['last_name']) ?></td>
                        <td><?= formatDateTime($exam['exam_date']) ?></td>
                        <td><?= $exam['total_marks'] ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/exams/<?= $exam['id'] ?>" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($exams)): ?>
                    <tr><td colspan="7" style="text-align: center; color: var(--secondary-color);">No exams found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
