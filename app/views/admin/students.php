<!-- Admin page for managing students. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Student Management</h3>
        <div>
            <form method="GET" action="<?= BASE_URL ?>/admin/students" style="display: flex; gap: 10px;">
                <input type="text" name="search" class="form-control" placeholder="Search students..." value="<?= htmlspecialchars($search) ?>" style="max-width: 250px;">
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
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($student['student_code']) ?></strong></td>
                        <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                        <td><?= htmlspecialchars($student['email']) ?></td>
                        <td><?= htmlspecialchars($student['phone'] ?? '-') ?></td>
                        <td>
                            <span class="badge badge-<?= $student['status'] === 'active' ? 'success' : 'danger' ?>">
                                <?= ucfirst($student['status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/edit-user/<?= $student['user_id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/admin/students/<?= $student['id'] ?>" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--secondary-color);">No students found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
