<!-- Teacher page for managing examinations. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">My Exams</h3>
        <button onclick="document.getElementById('examForm').style.display='block'" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Create Exam</button>
    </div>
    <div class="card-body">
        <div id="examForm" style="display: none; margin-bottom: 20px; padding: 20px; background: #f8fafc; border-radius: 8px;">
            <form method="POST" action="<?= BASE_URL ?>/teacher/exams">
                <?= csrfField() ?>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Batch</label>
                        <select name="batch_id" class="form-select">
                            <option value="">Select Batch</option>
                            <?php foreach ($batches as $batch): ?>
                            <option value="<?= $batch['id'] ?? '' ?>"><?= htmlspecialchars($batch['batch_name'] ?? $batch['name'] ?? '') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Subject</label>
                        <select name="subject_id" class="form-select">
                            <option value="">Select Subject</option>
                            <?php foreach ($subjects as $subject): ?>
                            <option value="<?= $subject['id'] ?>"><?= htmlspecialchars($subject['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Exam Title *</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Exam Date *</label>
                        <input type="datetime-local" name="exam_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Duration (minutes)</label>
                        <input type="number" name="duration_minutes" class="form-control" value="120">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Total Marks</label>
                        <input type="number" name="total_marks" class="form-control" value="100">
                    </div>
                    <div class="form-group">
                        <label>Passing Marks</label>
                        <input type="number" name="passing_marks" class="form-control" value="40">
                    </div>
                    <div class="form-group">
                        <label>Type</label>
                        <select name="is_online" class="form-select">
                            <option value="0">Written</option>
                            <option value="1">Online</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Create</button>
                <button type="button" onclick="document.getElementById('examForm').style.display='none'" class="btn btn-secondary">Cancel</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Exam Name</th><th>Batch</th><th>Subject</th><th>Date</th><th>Duration</th><th>Marks</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($exams as $exam): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($exam['title']) ?></strong></td>
                        <td><?= htmlspecialchars($exam['batch_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($exam['subject_name'] ?? '-') ?></td>
                        <td><?= formatDateTime($exam['exam_date']) ?></td>
                        <td><?= $exam['duration_minutes'] ?> min</td>
                        <td><?= $exam['total_marks'] ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/teacher/mark-results/<?= $exam['id'] ?>" class="btn btn-sm btn-primary" title="Enter Marks">
                                <i class="fas fa-pen"></i> Marks
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($exams)): ?>
                    <tr><td colspan="7" style="text-align:center;color:var(--secondary-color);">No exams created</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
