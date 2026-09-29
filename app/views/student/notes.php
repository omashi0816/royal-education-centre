<!-- Student page for accessing study notes. -->
<div class="card">
    <div class="card-header"><h3 class="card-title">Study Notes</h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Title</th><th>Batch</th><th>Subject</th><th>Teacher</th><th>Date</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach ($materials as $material): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($material['title']) ?></strong></td>
                        <td><?= htmlspecialchars($material['batch_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($material['subject_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($material['teacher_name'] ?? '-') ?></td>
                        <td><?= formatDate($material['created_at']) ?></td>
                        <td>
                            <?php if ($material['file_path']): ?>
                            <a href="<?= htmlspecialchars($material['file_path']) ?>" target="_blank" class="btn btn-sm btn-info"><i class="fas fa-download"></i> Download</a>
                            <?php else: ?>
                            -
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($materials)): ?>
                    <tr><td colspan="6" style="text-align:center;color:var(--secondary-color);">No study materials available</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
