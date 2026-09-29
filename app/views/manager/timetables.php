<!-- Manager page for reviewing timetables. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Timetables</h3>
        <form method="GET" action="<?= BASE_URL ?>/manager/timetables" style="display: inline;">
            <select name="batch_id" class="form-select" style="max-width: 300px;" onchange="this.form.submit()">
                <option value="">All Batches</option>
                <?php foreach ($batches as $batch): ?>
                <option value="<?= $batch['id'] ?>" <?= $selectedBatch == $batch['id'] ? 'selected' : '' ?>><?= htmlspecialchars($batch['batch_name']) ?> - <?= htmlspecialchars($batch['course_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr><th>Day</th><th>Time</th><th>Course</th><th>Batch</th><th>Subject</th><th>Teacher</th><th>Room</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($slots as $slot): ?>
                    <tr>
                        <td><span class="badge badge-info"><?= ucfirst($slot['day_of_week']) ?></span></td>
                        <td><?= date('h:i A', strtotime($slot['start_time'])) ?> - <?= date('h:i A', strtotime($slot['end_time'])) ?></td>
                        <td><?= htmlspecialchars($slot['course_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($slot['batch_name']) ?></td>
                        <td><?= htmlspecialchars($slot['subject_name']) ?></td>
                        <td><?= htmlspecialchars($slot['first_name'] . ' ' . $slot['last_name']) ?></td>
                        <td><?= htmlspecialchars($slot['room'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($slots)): ?>
                    <tr><td colspan="7" style="text-align:center;color:var(--secondary-color);">No timetable slots found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
