<!-- Admin form for creating or editing a notice. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= isset($notice) ? 'Edit Notice' : 'Create Notice' ?></h3>
        <a href="<?= BASE_URL ?>/admin/notices" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/admin/<?= isset($notice) ? 'edit-notice/' . $notice['id'] : 'create-notice' ?>">
            <?= csrfField() ?>
            <div class="form-group">
                <label>Title *</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($notice['title'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Content *</label>
                <textarea name="content" class="form-control" rows="5" required><?= htmlspecialchars($notice['content'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label>Target Role</label>
                <select name="target_role" class="form-select">
                    <option value="all" <?= ($notice['target_role'] ?? '') === 'all' ? 'selected' : '' ?>>All</option>
                    <option value="student" <?= ($notice['target_role'] ?? '') === 'student' ? 'selected' : '' ?>>Students</option>
                    <option value="teacher" <?= ($notice['target_role'] ?? '') === 'teacher' ? 'selected' : '' ?>>Teachers</option>
                    <option value="manager" <?= ($notice['target_role'] ?? '') === 'manager' ? 'selected' : '' ?>>Managers</option>
                    <option value="receptionist" <?= ($notice['target_role'] ?? '') === 'receptionist' ? 'selected' : '' ?>>Receptionists</option>
                    <option value="cashier" <?= ($notice['target_role'] ?? '') === 'cashier' ? 'selected' : '' ?>>Cashiers</option>
                </select>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_pinned" value="1" <?= ($notice['is_pinned'] ?? 0) ? 'checked' : '' ?>>
                    Pin this notice
                </label>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Notice</button>
        </form>
    </div>
</div>
