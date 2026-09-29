<!-- Admin form for creating or editing a batch. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= isset($batch) ? 'Edit Batch' : 'Create Batch' ?></h3>
        <a href="<?= BASE_URL ?>/admin/batches" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/admin/<?= isset($batch) ? 'edit-batch/' . $batch['id'] : 'create-batch' ?>">
            <?= csrfField() ?>
            <div class="form-group">
                <label>Course *</label>
                <select name="course_id" class="form-select" required>
                    <option value="">Select Course</option>
                    <?php foreach ($courses as $course): ?>
                    <option value="<?= $course['id'] ?>" <?= isset($batch) && $batch['course_id'] == $course['id'] ? 'selected' : '' ?>><?= htmlspecialchars($course['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Batch Name *</label>
                <input type="text" name="batch_name" class="form-control" value="<?= htmlspecialchars($batch['batch_name'] ?? '') ?>" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Start Date *</label>
                    <input type="date" name="start_date" class="form-control" value="<?= $batch['start_date'] ?? '' ?>" required>
                </div>
                <div class="form-group">
                    <label>End Date *</label>
                    <input type="date" name="end_date" class="form-control" value="<?= $batch['end_date'] ?? '' ?>" required>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Max Students *</label>
                    <input type="number" name="max_students" class="form-control" value="<?= $batch['max_students'] ?? '30' ?>" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-select">
                        <option value="pending" <?= ($batch['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= ($batch['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="active" <?= ($batch['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="completed" <?= ($batch['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="cancelled" <?= ($batch['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Batch</button>
        </form>
    </div>
</div>
