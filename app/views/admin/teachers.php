<!-- Admin page for managing teachers. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Teacher Management</h3>
        <div>
            <form method="GET" action="<?= BASE_URL ?>/admin/teachers" style="display: flex; gap: 10px;">
                <input type="text" name="search" class="form-control" placeholder="Search teachers..." value="<?= htmlspecialchars($search) ?>" style="max-width: 250px;">
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
                        <th>Specialization</th>
                        <th>Experience</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teachers as $teacher): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($teacher['teacher_code']) ?></strong></td>
                        <td><?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?></td>
                        <td><?= htmlspecialchars($teacher['specialization'] ?? '-') ?></td>
                        <td><?= $teacher['experience_years'] ?> years</td>
                        <td>
                            <span class="badge badge-<?= $teacher['status'] === 'active' ? 'success' : 'danger' ?>">
                                <?= ucfirst($teacher['status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/edit-user/<?= $teacher['user_id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/admin/teachers/<?= $teacher['id'] ?>" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($teachers)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--secondary-color);">No teachers found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
