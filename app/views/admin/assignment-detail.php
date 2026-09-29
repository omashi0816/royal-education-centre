<!-- Admin page for viewing assignment details. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Assignment Details</h3>
        <a href="<?= BASE_URL ?>/admin/assignments" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <div style="margin-bottom: 20px;">
            <h4><?= htmlspecialchars($assignment['title']) ?></h4>
            <p style="color: var(--secondary-color); margin: 10px 0;"><?= htmlspecialchars($assignment['description'] ?? 'No description') ?></p>
            <div style="display: flex; gap: 20px; font-size: 0.875rem; color: var(--secondary-color);">
                <span><i class="fas fa-layer-group"></i> <?= htmlspecialchars($assignment['batch_name']) ?></span>
                <span><i class="fas fa-book"></i> <?= htmlspecialchars($assignment['subject_name']) ?></span>
                <span><i class="fas fa-user"></i> <?= htmlspecialchars($assignment['first_name'] . ' ' . $assignment['last_name']) ?></span>
                <span><i class="fas fa-calendar"></i> Due: <?= formatDateTime($assignment['due_date']) ?></span>
                <span><i class="fas fa-star"></i> Marks: <?= $assignment['max_marks'] ?></span>
            </div>
        </div>
        <h4>Submissions</h4>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Submitted At</th>
                        <th>Status</th>
                        <th>Marks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($submissions as $sub): ?>
                    <tr>
                        <td><?= htmlspecialchars($sub['first_name'] . ' ' . $sub['last_name']) ?> (<?= htmlspecialchars($sub['student_code']) ?>)</td>
                        <td><?= formatDateTime($sub['submitted_at']) ?></td>
                        <td><span class="badge badge-<?= $sub['status'] === 'graded' ? 'success' : 'warning' ?>"><?= ucfirst($sub['status']) ?></span></td>
                        <td><?= $sub['marks'] ?? '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($submissions)): ?>
                    <tr><td colspan="4" style="text-align: center; color: var(--secondary-color);">No submissions yet</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
