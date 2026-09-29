<!-- Manager form for creating or editing operational staff accounts. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= isset($user) ? 'Edit Staff User' : 'Add Staff User' ?></h3>
        <a href="<?= BASE_URL ?>/manager/users" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= isset($user) ? BASE_URL . '/manager/edit-user/' . (int) $user['id'] : BASE_URL . '/manager/create-user' ?>">
            <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
            <div class="form-group">
                <label class="form-label">Role</label>
                <select name="role_id" class="form-select" required <?= isset($user) ? 'disabled' : '' ?>>
                    <?php foreach ($roles as $roleOption): ?>
                    <option value="<?= (int) $roleOption['id'] ?>" <?= isset($user) && (int) $user['role_id'] === (int) $roleOption['id'] ? 'selected' : '' ?>><?= htmlspecialchars($roleOption['display_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($user)): ?>
                <input type="hidden" name="role_id" value="<?= (int) $user['role_id'] ?>">
                <?php endif; ?>
            </div>
            <?php if (!isset($user)): ?>
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required minlength="3">
            </div>
            <div class="form-group">
                <label class="form-label">Temporary Password</label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>
            <?php endif; ?>
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="active" <?= !isset($user) || $user['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= isset($user) && $user['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    <option value="suspended" <?= isset($user) && $user['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Staff User</button>
            <a href="<?= BASE_URL ?>/manager/users" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
        </form>
    </div>
</div>
