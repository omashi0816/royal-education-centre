<!-- Teacher page for grading an assignment. -->
<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">Grade Assignment</h3>
            <p style="margin: 5px 0 0; color: var(--secondary-color);">
                <?= htmlspecialchars($assignment['title']) ?> — <?= htmlspecialchars($assignment['batch_name']) ?> (Maximum: <?= (int) $assignment['max_marks'] ?>)
            </p>
        </div>
        <a href="<?= BASE_URL ?>/teacher/assignments" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/teacher/save-assignment-grades/<?= $assignment['id'] ?>">
            <?= csrfField() ?>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Student</th><th>Contact</th><th>Submission</th><th>Marks</th><th>Feedback</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($student['student_code']) ?></strong><br><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                            <td><?= htmlspecialchars($student['email'] ?? '-') ?><br><?= htmlspecialchars($student['phone'] ?? '-') ?></td>
                            <td><?= !empty($student['submitted_at']) ? formatDateTime($student['submitted_at']) . '<br><span class="badge badge-success">' . ucfirst($student['submission_status']) . '</span>' : '<span class="badge badge-warning">Not submitted</span>' ?></td>
                            <td><input type="number" name="marks[<?= $student['id'] ?>]" class="form-control" min="0" max="<?= (int) $assignment['max_marks'] ?>" step="0.01" value="<?= $student['marks'] ?? '' ?>" placeholder="0 - <?= (int) $assignment['max_marks'] ?>"></td>
                            <td><input type="text" name="feedback[<?= $student['id'] ?>]" class="form-control" value="<?= htmlspecialchars($student['feedback'] ?? '') ?>" placeholder="Optional feedback"></td>
                            <td>
                                <?php if ($student['marks'] !== null): ?>
                                <button type="submit" formaction="<?= BASE_URL ?>/teacher/delete-assignment-grade/<?= $assignment['id'] ?>" formmethod="post" name="student_id" value="<?= (int) $student['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this assignment result?')" title="Delete result"><i class="fas fa-trash"></i></button>
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
