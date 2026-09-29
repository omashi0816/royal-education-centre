<!-- Admin page for managing staff members. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Staff Management</h3>
        <div>
            <form method="GET" action="<?= BASE_URL ?>/admin/staff" style="display: flex; gap: 10px;">
                <input type="text" name="search" class="form-control" placeholder="Search staff..." value="<?= htmlspecialchars($search) ?>" style="max-width: 250px;">
                <button type="submit" class="btn btn-secondary">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($staff as $member): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($member['staff_code']) ?></strong></td>
                        <td><?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?></td>
                        <td><?= htmlspecialchars($member['designation'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($member['department'] ?? '-') ?></td>
                        <td>
                            <span class="badge badge-<?= $member['status'] === 'active' ? 'success' : 'danger' ?>">
                                <?= ucfirst($member['status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/edit-user/<?= $member['user_id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/admin/staff/<?= $member['id'] ?>" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($staff)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--secondary-color);">No staff found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
