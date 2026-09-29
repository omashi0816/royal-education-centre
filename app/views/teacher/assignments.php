<!-- Teacher page for managing assignments. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">My Assignments</h3>
        <button onclick="document.getElementById('assignForm').style.display='block'" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Create Assignment</button>
    </div>
    <div class="card-body">
        <div id="assignForm" style="display: none; margin-bottom: 20px; padding: 20px; background: #f8fafc; border-radius: 8px;">
            <form method="POST" action="<?= BASE_URL ?>/teacher/assignments">
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
                    <label>Title *</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Due Date *</label>
                        <input type="datetime-local" name="due_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Max Marks</label>
                        <input type="number" name="max_marks" class="form-control" value="100">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Create</button>
                <button type="button" onclick="document.getElementById('assignForm').style.display='none'" class="btn btn-secondary">Cancel</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Title</th><th>Batch</th><th>Subject</th><th>Due Date</th><th>Marks</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($assignments as $assignment): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($assignment['title']) ?></strong></td>
                        <td><?= htmlspecialchars($assignment['batch_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($assignment['subject_name'] ?? '-') ?></td>
                        <td><?= formatDateTime($assignment['due_date']) ?></td>
                        <td><?= $assignment['max_marks'] ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/teacher/grade-assignment/<?= $assignment['id'] ?>" class="btn btn-sm btn-primary" title="Enter Marks"><i class="fas fa-pen"></i> Marks</a>
                            <a href="<?= BASE_URL ?>/teacher/delete-assignment/<?= $assignment['id'] ?>" class="btn btn-sm btn-danger btn-delete" title="Delete"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($assignments)): ?>
                    <tr><td colspan="6" style="text-align:center;color:var(--secondary-color);">No assignments created</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
