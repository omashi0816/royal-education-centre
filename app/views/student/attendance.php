<!-- Student page for viewing attendance records. -->
<div class="card">
    <div class="card-header"><h3 class="card-title">My Attendance</h3></div>
    <div class="card-body">
        <?php if ($stats): ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 30px;">
            <div style="padding: 20px; background: #dcfce7; border-radius: 8px; text-align: center;">
                <h3 style="margin: 0; color: #166534;"><?= $stats['present'] ?? 0 ?></h3>
                <p style="margin: 10px 0 0 0; color: #166534;">Present</p>
            </div>
            <div style="padding: 20px; background: #fee2e2; border-radius: 8px; text-align: center;">
                <h3 style="margin: 0; color: #991b1b;"><?= $stats['absent'] ?? 0 ?></h3>
                <p style="margin: 10px 0 0 0; color: #991b1b;">Absent</p>
            </div>
            <div style="padding: 20px; background: #fef3c7; border-radius: 8px; text-align: center;">
                <h3 style="margin: 0; color: #92400e;"><?= $stats['late'] ?? 0 ?></h3>
                <p style="margin: 10px 0 0 0; color: #92400e;">Late</p>
            </div>
            <div style="padding: 20px; background: #e0f2fe; border-radius: 8px; text-align: center;">
                <h3 style="margin: 0; color: #075985;"><?= ($stats && $stats['total'] > 0) ? round(($stats['present'] / $stats['total']) * 100) : 0 ?>%</h3>
                <p style="margin: 10px 0 0 0; color: #075985;">Attendance</p>
            </div>
        </div>
        <?php endif; ?>
        <h4>Attendance Records</h4>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Attendance Date</th><th>Class / Batch</th><th>Attendance</th></tr></thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                    <tr>
                        <td><?= formatDate($record['date']) ?></td>
                        <td><?= htmlspecialchars($record['batch_name'] ?? '-') ?></td>
                        <td><span class="badge badge-<?= $record['status'] === 'present' ? 'success' : ($record['status'] === 'absent' ? 'danger' : 'warning') ?>"><?= ucfirst($record['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($records)): ?>
                    <tr><td colspan="3" style="text-align:center;color:var(--secondary-color);">No attendance records</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
