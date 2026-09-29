<!-- Student page for viewing the personal timetable. -->
<div class="card">
    <div class="card-header"><h3 class="card-title">My Timetable</h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Day</th><th>Time</th><th>Batch</th><th>Subject</th><th>Teacher</th><th>Room</th></tr></thead>
                <tbody>
                    <?php foreach ($timetable as $slot): ?>
                    <tr>
                        <td><span class="badge badge-info"><?= ucfirst($slot['day_of_week']) ?></span></td>
                        <td><?= date('h:i A', strtotime($slot['start_time'])) ?> - <?= date('h:i A', strtotime($slot['end_time'])) ?></td>
                        <td><?= htmlspecialchars($slot['batch_name']) ?></td>
                        <td><?= htmlspecialchars($slot['subject_name']) ?></td>
                        <td><?= htmlspecialchars($slot['first_name'] . ' ' . $slot['last_name']) ?></td>
                        <td><?= htmlspecialchars($slot['room'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($timetable)): ?>
                    <tr><td colspan="6" style="text-align:center;color:var(--secondary-color);">No timetable entries</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
