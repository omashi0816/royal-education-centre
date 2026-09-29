<!-- Admin page for managing courses. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Course Management</h3>
        <div style="display: flex; gap: 10px; align-items: center;">
            <form method="GET" action="<?= BASE_URL ?>/admin/courses" style="display: flex; gap: 10px;">
                <input type="text" name="search" class="form-control" placeholder="Search courses..." value="<?= htmlspecialchars($search) ?>" style="max-width: 200px;">
                <select name="status" class="form-select" style="max-width: 130px;">
                    <option value="">All Status</option>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
                <button type="submit" class="btn btn-secondary">
                    <i class="fas fa-search"></i>
                </button>
            </form>
            <a href="<?= BASE_URL ?>/admin/create-course" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Add Course
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Fee</th>
                        <th>Duration</th>
                        <th>Subjects</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($course['code']) ?></strong></td>
                        <td><?= htmlspecialchars($course['name']) ?></td>
                        <td><?= formatCurrency($course['course_fee']) ?></td>
                        <td><?= $course['duration_months'] ?> months</td>
                        <td><?= $course['subject_count'] ?? 0 ?></td>
                        <td>
                            <span class="badge badge-<?= $course['status'] === 'active' ? 'success' : ($course['status'] === 'pending' ? 'warning' : 'info') ?>">
                                <?= ucfirst($course['status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/edit-course/<?= $course['id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/admin/courses/<?= $course['id'] ?>" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($courses)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--secondary-color);">No courses found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
