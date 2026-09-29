<!-- Admin page for managing batches. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Batch Management</h3>
        <div style="display: flex; gap: 10px; align-items: center;">
            <form method="GET" action="<?= BASE_URL ?>/admin/batches" style="display: flex; gap: 10px;">
                <input type="text" name="search" class="form-control" placeholder="Search batches..." value="<?= htmlspecialchars($search) ?>" style="max-width: 200px;">
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
            <a href="<?= BASE_URL ?>/admin/create-batch" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Add Batch
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Batch Name</th>
                        <th>Course</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Max Students</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($batches as $batch): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($batch['batch_name']) ?></strong></td>
                        <td><?= htmlspecialchars($batch['course_name']) ?></td>
                        <td><?= formatDate($batch['start_date']) ?></td>
                        <td><?= formatDate($batch['end_date']) ?></td>
                        <td><?= $batch['max_students'] ?></td>
                        <td>
                            <span class="badge badge-<?= $batch['status'] === 'active' ? 'success' : ($batch['status'] === 'pending' ? 'warning' : 'info') ?>">
                                <?= ucfirst($batch['status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/edit-batch/<?= $batch['id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/admin/batches/<?= $batch['id'] ?>" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($batches)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--secondary-color);">No batches found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
