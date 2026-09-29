<!-- Shared page for viewing and editing the current user profile. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">My Profile</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/dashboard/profile">
            <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
            
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
                <small class="text-muted">Username cannot be changed</small>
            </div>
            
            <div class="form-group">
                <label class="form-label">Role</label>
                <input type="text" class="form-control" value="<?= ucfirst($user['role_name']) ?>" disabled>
            </div>
            
            <hr style="margin: 30px 0; border: none; border-top: 1px solid #e2e8f0;">
            
            <h4 style="margin-bottom: 20px;">Change Password</h4>
            
            <div class="form-group">
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-control">
            </div>
            
            <div class="form-group">
                <label class="form-label">New Password</label>
                <input type="password" name="new_password" class="form-control" minlength="<?= PASSWORD_MIN_LENGTH ?>">
                <small class="text-muted">Minimum <?= PASSWORD_MIN_LENGTH ?> characters</small>
            </div>
            
            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="javascript:history.back()" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </form>
    </div>
</div>
