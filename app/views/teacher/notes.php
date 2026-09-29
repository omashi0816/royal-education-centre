<!-- Teacher page for managing study notes. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Study Notes</h3>
        <button onclick="document.getElementById('noteForm').style.display='block'" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Add Note</button>
    </div>
    <div class="card-body">
        <div id="noteForm" style="display: none; margin-bottom: 20px; padding: 20px; background: #f8fafc; border-radius: 8px;">
            <form method="POST" action="<?= BASE_URL ?>/teacher/notes">
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
                <div class="form-group">
                    <label>File Path / Link</label>
                    <input type="text" name="file_path" class="form-control" placeholder="URL or file path">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
                <button type="button" onclick="document.getElementById('noteForm').style.display='none'" class="btn btn-secondary">Cancel</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Title</th><th>Batch</th><th>Subject</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($materials as $material): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($material['title']) ?></strong></td>
                        <td><?= htmlspecialchars($material['batch_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($material['subject_name'] ?? '-') ?></td>
                        <td><?= formatDate($material['created_at']) ?></td>
                        <td>
                            <a href="<?= htmlspecialchars($material['file_path'] ?? '#') ?>" target="_blank" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a>
                            <a href="<?= BASE_URL ?>/teacher/delete-note/<?= $material['id'] ?>" class="btn btn-sm btn-danger btn-delete" title="Delete"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($materials)): ?>
                    <tr><td colspan="5" style="text-align:center;color:var(--secondary-color);">No study materials uploaded</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
