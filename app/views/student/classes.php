<!-- Student page for accessing online classes. -->
<div class="card">
    <div class="card-header"><h3 class="card-title">Online Classes</h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Title</th><th>Batch</th><th>Subject</th><th>Date & Time</th><th>Link</th></tr></thead>
                <tbody>
                    <?php foreach ($classes as $class): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($class['title']) ?></strong></td>
                        <td><?= htmlspecialchars($class['batch_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($class['subject_name'] ?? '-') ?></td>
                        <td><?= formatDateTime($class['class_date']) ?></td>
                        <td>
                            <?php if ($class['meeting_link']): ?>
                            <a href="<?= htmlspecialchars($class['meeting_link']) ?>" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-video"></i> Join</a>
                            <?php else: ?>
                            -
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($classes)): ?>
                    <tr><td colspan="5" style="text-align:center;color:var(--secondary-color);">No online classes scheduled</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
