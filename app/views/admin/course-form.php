<!-- Admin form for creating or editing a course. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= isset($course) ? 'Edit Course' : 'Create Course' ?></h3>
        <a href="<?= BASE_URL ?>/admin/courses" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/admin/<?= isset($course) ? 'edit-course/' . $course['id'] : 'create-course' ?>">
            <?= csrfField() ?>
            <div class="form-group">
                <label>Course Code *</label>
                <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($course['code'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Course Name *</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($course['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($course['description'] ?? '') ?></textarea>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Course Fee *</label>
                    <input type="number" step="0.01" name="course_fee" class="form-control" value="<?= $course['course_fee'] ?? '0' ?>" required>
                </div>
                <div class="form-group">
                    <label>Registration Fee</label>
                    <input type="number" step="0.01" name="registration_fee" class="form-control" value="<?= $course['registration_fee'] ?? '0' ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Monthly Fee</label>
                <input type="number" step="0.01" name="monthly_fee" class="form-control" value="<?= $course['monthly_fee'] ?? '0' ?>">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Duration (months) *</label>
                    <input type="number" name="duration_months" class="form-control" value="<?= $course['duration_months'] ?? '1' ?>" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-select">
                        <option value="pending" <?= ($course['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= ($course['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="active" <?= ($course['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="completed" <?= ($course['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="cancelled" <?= ($course['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Course</button>
        </form>
    </div>
</div>
