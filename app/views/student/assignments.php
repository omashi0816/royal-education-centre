<!-- Student page for viewing assigned coursework. -->
<div class="card">
    <div class="card-header"><h3 class="card-title">My Assignments</h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Title</th><th>Batch</th><th>Subject</th><th>Due Date</th><th>Maximum Marks</th><th>Obtained Marks</th><th>Status</th></tr></thead>
                <tbody>
                    <?php foreach ($assignments as $assignment): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($assignment['title']) ?></strong></td>
                        <td><?= htmlspecialchars($assignment['batch_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($assignment['subject_name'] ?? '-') ?></td>
                        <td><?= formatDateTime($assignment['due_date']) ?></td>
                        <td><?= $assignment['max_marks'] ?></td>
                        <td>
                            <?= $assignment['marks'] !== null ? '<strong>' . htmlspecialchars($assignment['marks']) . '</strong>' : '-' ?>
                        </td>
                        <td>
                            <span class="badge badge-<?= in_array($assignment['submission_status'], ['submitted', 'graded']) ? 'success' : 'warning' ?>">
                                <?= ucfirst($assignment['submission_status'] ?? 'pending') ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($assignments)): ?>
                    <tr><td colspan="7" style="text-align:center;color:var(--secondary-color);">No assignments assigned</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
