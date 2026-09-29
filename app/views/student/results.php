<!-- Student page for viewing examination and assignment results. -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-header"><h3 class="card-title">Assignment Results</h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Assignment</th><th>Subject</th><th>Marks</th><th>Max Marks</th><th>Status</th></tr></thead>
                <tbody>
                    <?php foreach ($assignments as $assignment): ?>
                    <?php if ($assignment['marks'] !== null || $assignment['submission_status'] === 'graded'): ?>
                    <tr>
                        <td><?= htmlspecialchars($assignment['title']) ?></td>
                        <td><?= htmlspecialchars($assignment['subject_name']) ?></td>
                        <td><strong><?= $assignment['marks'] !== null ? htmlspecialchars($assignment['marks']) : '-' ?></strong></td>
                        <td><?= $assignment['max_marks'] ?></td>
                        <td>
                            <span class="badge badge-<?= $assignment['submission_status'] === 'graded' ? 'success' : 'warning' ?>">
                                <?= ucfirst($assignment['submission_status'] ?? 'pending') ?>
                            </span>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if (empty(array_filter($assignments, fn($a) => $a['marks'] !== null || $a['submission_status'] === 'graded'))): ?>
                    <tr><td colspan="5" style="text-align:center;color:var(--secondary-color);">No assignment results published yet</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3 class="card-title">Exam Results</h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Exam</th><th>Subject</th><th>Marks Obtained</th><th>Total</th><th>Percentage</th><th>Grade</th><th>Status</th></tr></thead>
                <tbody>
                    <?php foreach ($exams as $exam): ?>
                    <?php if ($exam['result_status']): ?>
                    <tr>
                        <td><?= htmlspecialchars($exam['title']) ?></td>
                        <td><?= htmlspecialchars($exam['subject_name']) ?></td>
                        <td><strong><?= $exam['marks_obtained'] ?></strong></td>
                        <td><?= $exam['total_marks'] ?></td>
                        <td><?= $exam['percentage'] ?>%</td>
                        <td><span class="badge badge-info"><?= $exam['grade'] ?></span></td>
                        <td><span class="badge badge-<?= $exam['result_status'] === 'passed' ? 'success' : 'danger' ?>"><?= ucfirst($exam['result_status']) ?></span></td>
                    </tr>
                    <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if (empty(array_filter($exams, fn($e) => $e['result_status']))): ?>
                    <tr><td colspan="7" style="text-align:center;color:var(--secondary-color);">No exam results published yet</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
