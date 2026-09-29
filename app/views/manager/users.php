<!-- Manager page for managing operational staff accounts. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Staff Users</h3>
        <a href="<?= BASE_URL ?>/manager/create-user" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> Add Staff User
        </a>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>/manager/users" style="margin-bottom: 20px; display: flex; gap: 10px;">
            <input type="text" name="search" class="form-control" placeholder="Search staff users..." value="<?= htmlspecialchars($search) ?>" style="max-width: 300px;">
            <select name="role" class="form-select" style="max-width: 180px;">
                <option value="">All managed roles</option>
                <option value="teacher" <?= $role === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                <option value="receptionist" <?= $role === 'receptionist' ? 'selected' : '' ?>>Receptionist</option>
                <option value="cashier" <?= $role === 'cashier' ? 'selected' : '' ?>>Cashier</option>
            </select>
            <button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i> Search</button>
        </form>

        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Username</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><span class="badge badge-info"><?= htmlspecialchars($user['role_display_name']) ?></span></td>
                        <td><span class="badge badge-<?= $user['status'] === 'active' ? 'success' : 'danger' ?>"><?= ucfirst($user['status']) ?></span></td>
                        <td>
                            <a href="<?= BASE_URL ?>/manager/edit-user/<?= (int) $user['id'] ?>" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="<?= BASE_URL ?>/manager/delete-user/<?= (int) $user['id'] ?>" style="display:inline" onsubmit="return confirm('Delete this staff user?');">
                                <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users)): ?>
                    <tr><td colspan="6" style="text-align:center;color:var(--secondary-color);">No staff users found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
