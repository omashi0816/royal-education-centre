<!-- Admin page for managing user accounts. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">User Management</h3>
        <div>
            <a href="<?= BASE_URL ?>/admin/users" class="btn btn-sm btn-secondary">
                <i class="fas fa-sync"></i> Refresh
            </a>
            <a href="<?= BASE_URL ?>/admin/create-user" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Add User
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>/admin/users" style="margin-bottom: 20px; display: flex; gap: 10px;">
            <input type="text" name="search" class="form-control" placeholder="Search users..." value="<?= htmlspecialchars($search) ?>" style="max-width: 300px;">
            <select name="role" class="form-select" style="max-width: 150px;">
                <option value="">All Roles</option>
                <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="manager" <?= $role === 'manager' ? 'selected' : '' ?>>Manager</option>
                <option value="teacher" <?= $role === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                <option value="student" <?= $role === 'student' ? 'selected' : '' ?>>Student</option>
                <option value="receptionist" <?= $role === 'receptionist' ? 'selected' : '' ?>>Receptionist</option>
                <option value="cashier" <?= $role === 'cashier' ? 'selected' : '' ?>>Cashier</option>
            </select>
            <button type="submit" class="btn btn-secondary">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <span class="badge badge-info"><?= ucfirst($user['role_name']) ?></span>
                        </td>
                        <td>
                            <span class="badge badge-<?= $user['status'] === 'active' ? 'success' : 'danger' ?>">
                                <?= ucfirst($user['status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/edit-user/<?= $user['id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if ($user['id'] != currentUserId()): ?>
                            <button onclick="toggleUserStatus(<?= $user['id'] ?>)" class="btn btn-sm btn-warning" title="Toggle Status">
                                <i class="fas fa-power-off"></i>
                            </button>
                            <form method="POST" action="<?= BASE_URL ?>/admin/delete-user/<?= $user['id'] ?>" style="display:inline" onsubmit="return confirm('Delete this user?');">
                                <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--secondary-color);">No users found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleUserStatus(userId) {
    if (!confirm('Are you sure you want to toggle this user status?')) return;
    
    const csrfToken = '<?= getCsrfToken() ?>';
        fetch(`<?= BASE_URL ?>/admin/toggle-user-status/${userId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
        },
            body: `csrf_token=${encodeURIComponent(csrfToken)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Failed to update user status');
        }
    })
    .catch(error => {
        alert('An error occurred');
    });
}
</script>
