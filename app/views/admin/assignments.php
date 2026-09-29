<!-- Admin page for managing assignments. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Assignment Management</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Batch</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Due Date</th>
                        <th>Marks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assignments as $assignment): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($assignment['title']) ?></strong></td>
                        <td><?= htmlspecialchars($assignment['batch_name']) ?></td>
                        <td><?= htmlspecialchars($assignment['subject_name']) ?></td>
                        <td><?= htmlspecialchars($assignment['first_name'] . ' ' . $assignment['last_name']) ?></td>
                        <td><?= formatDateTime($assignment['due_date']) ?></td>
                        <td><?= $assignment['max_marks'] ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/assignments/<?= $assignment['id'] ?>" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($assignments)): ?>
                    <tr><td colspan="7" style="text-align: center; color: var(--secondary-color);">No assignments found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
