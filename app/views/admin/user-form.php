<!-- Admin form for creating or editing a user account. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= isset($user) ? 'Edit User' : 'Create User' ?></h3>
        <a href="<?= BASE_URL ?>/admin/users" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= isset($user) ? BASE_URL . '/admin/edit-user/' . $user['id'] : BASE_URL . '/admin/create-user' ?>">
            <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
            
            <div class="form-group">
                <label class="form-label">Role</label>
                <select name="role_id" class="form-select" required>
                    <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['id'] ?>" <?= isset($user) && $user['role_id'] == $role['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($role['display_name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <?php if (!isset($user)): ?>
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Password</label>
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
                    <option value="active" <?= (isset($user) && $user['status'] === 'active') || !isset($user) ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= isset($user) && $user['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    <option value="suspended" <?= isset($user) && $user['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                </select>
            </div>
            
            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save User
                </button>
                <a href="<?= BASE_URL ?>/admin/users" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
