<!-- Admin page for creating and managing system backups. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Database Backup</h3>
    </div>
    <div class="card-body">
        <div style="margin-bottom: 30px;">
            <h4>Create New Backup</h4>
            <p style="color: var(--secondary-color); margin-bottom: 15px;">Download a full backup of the database.</p>
            <form method="POST" action="<?= BASE_URL ?>/admin/backup" style="display: inline;">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="download">
                <button type="submit" class="btn btn-primary"><i class="fas fa-download"></i> Download Backup</button>
            </form>
        </div>
        <h4>Existing Backups</h4>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Filename</th>
                        <th>Size</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($backups as $backup): ?>
                    <tr>
                        <td><?= htmlspecialchars($backup['filename']) ?></td>
                        <td><?= round($backup['size'] / 1024, 2) ?> KB</td>
                        <td><?= date('Y-m-d H:i:s', $backup['date']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($backups)): ?>
                    <tr><td colspan="3" style="text-align: center; color: var(--secondary-color);">No backups found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
