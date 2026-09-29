<!-- Admin page for viewing examination details. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Exam Details</h3>
        <a href="<?= BASE_URL ?>/admin/exams" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <div style="margin-bottom: 20px;">
            <h4><?= htmlspecialchars($exam['title']) ?></h4>
            <div style="display: flex; gap: 20px; font-size: 0.875rem; color: var(--secondary-color); margin-top: 10px;">
                <span><i class="fas fa-layer-group"></i> <?= htmlspecialchars($exam['batch_name']) ?></span>
                <span><i class="fas fa-book"></i> <?= htmlspecialchars($exam['subject_name']) ?></span>
                <span><i class="fas fa-user"></i> <?= htmlspecialchars($exam['first_name'] . ' ' . $exam['last_name']) ?></span>
                <span><i class="fas fa-calendar"></i> <?= formatDateTime($exam['exam_date']) ?></span>
                <span><i class="fas fa-star"></i> Total: <?= $exam['total_marks'] ?> | Passing: <?= $exam['passing_marks'] ?></span>
            </div>
        </div>
        <h4>Results</h4>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Marks Obtained</th>
                        <th>Percentage</th>
                        <th>Grade</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $result): ?>
                    <tr>
                        <td><?= htmlspecialchars($result['first_name'] . ' ' . $result['last_name']) ?> (<?= htmlspecialchars($result['student_code']) ?>)</td>
                        <td><?= $result['marks_obtained'] ?> / <?= $exam['total_marks'] ?></td>
                        <td><?= $result['percentage'] ?>%</td>
                        <td><span class="badge badge-info"><?= $result['grade'] ?></span></td>
                        <td><span class="badge badge-<?= $result['status'] === 'passed' ? 'success' : 'danger' ?>"><?= ucfirst($result['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($results)): ?>
                    <tr><td colspan="5" style="text-align: center; color: var(--secondary-color);">No results published yet</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
