<!-- Admin page for reviewing system activity logs. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Activity Logs</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= htmlspecialchars($log['username'] ?? 'System') ?></td>
                        <td><strong><?= htmlspecialchars($log['action']) ?></strong></td>
                        <td><?= ucfirst(htmlspecialchars($log['module'])) ?></td>
                        <td><?= htmlspecialchars($log['description'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($log['ip_address'] ?? '-') ?></td>
                        <td><?= formatDateTime($log['created_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--secondary-color);">No activity logs found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
