<!-- Teacher page for entering examination results. -->
<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">Enter Exam Marks</h3>
            <p style="margin: 5px 0 0; color: var(--secondary-color);">
                <?= htmlspecialchars($exam['title']) ?> — <?= htmlspecialchars($exam['batch_name']) ?> (Total: <?= (int) $exam['total_marks'] ?>)
            </p>
        </div>
        <a href="<?= BASE_URL ?>/teacher/exams" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/teacher/save-results/<?= $exam['id'] ?>">
            <?= csrfField() ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr><th>Student</th><th>Contact</th><th>Submission</th><th>Marks Obtained</th><th>Current Result</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($student['student_code']) ?></strong><br><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                            <td><?= htmlspecialchars($student['email'] ?? '-') ?><br><?= htmlspecialchars($student['phone'] ?? '-') ?></td>
                            <td><?= !empty($student['submitted_at']) ? formatDateTime($student['submitted_at']) . '<br><span class="badge badge-success">Submitted</span>' : '<span class="badge badge-warning">Not submitted</span>' ?></td>
                            <td><input type="number" name="marks[<?= $student['id'] ?>]" class="form-control" min="0" max="<?= (int) $exam['total_marks'] ?>" step="0.01" value="<?= $student['marks_obtained'] ?? '' ?>" placeholder="0 - <?= (int) $exam['total_marks'] ?>"></td>
                            <td><?= !empty($student['result_status']) ? '<span class="badge badge-' . ($student['result_status'] === 'passed' ? 'success' : 'danger') . '">' . ucfirst($student['result_status']) . '</span>' : '<span style="color: var(--secondary-color);">Not entered</span>' ?></td>
                            <td>
                                <?php if ($student['marks_obtained'] !== null): ?>
                                <button type="submit" formaction="<?= BASE_URL ?>/teacher/delete-exam-result/<?= $exam['id'] ?>" formmethod="post" name="student_id" value="<?= (int) $student['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this exam result?')" title="Delete result"><i class="fas fa-trash"></i></button>
                                <?php else: ?>
                                -
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($students)): ?>
                        <tr><td colspan="6" style="text-align: center; color: var(--secondary-color);">No active students in this batch.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (!empty($students)): ?>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Marks</button>
            <?php endif; ?>
        </form>
    </div>
</div>
