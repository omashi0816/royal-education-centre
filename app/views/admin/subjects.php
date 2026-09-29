<!-- Admin page for managing subjects. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Subject Management</h3>
        <a href="<?= BASE_URL ?>/admin/create-subject" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> Add Subject
        </a>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>/admin/subjects" style="margin-bottom: 20px;">
            <input type="text" name="search" class="form-control" placeholder="Search subjects..." value="<?= htmlspecialchars($search ?? '') ?>" style="max-width: 300px;">
            <button type="submit" class="btn btn-secondary" style="margin-top: 10px;"><i class="fas fa-search"></i> Search</button>
        </form>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subjects as $subject): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($subject['code']) ?></strong></td>
                        <td><?= htmlspecialchars($subject['name']) ?></td>
                        <td><?= htmlspecialchars($subject['description'] ?? '-') ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/subjects/<?= $subject['id'] ?>" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a>
                            <a href="<?= BASE_URL ?>/admin/edit-subject/<?= $subject['id'] ?>" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="<?= BASE_URL ?>/admin/delete-subject/<?= $subject['id'] ?>" class="btn btn-sm btn-danger btn-delete" title="Delete"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($subjects)): ?>
                    <tr><td colspan="4" style="text-align: center; color: var(--secondary-color);">No subjects found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
