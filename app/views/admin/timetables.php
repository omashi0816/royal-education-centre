<!-- Admin page for managing timetables. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Timetable Management</h3>
        <a href="<?= BASE_URL ?>/admin/create-timetable" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Add Slot</a>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>/admin/timetables" style="margin-bottom: 20px;">
            <select name="batch_id" class="form-select" style="max-width: 300px;" onchange="this.form.submit()">
                <option value="">All Batches</option>
                <?php foreach ($batches as $batch): ?>
                <option value="<?= $batch['id'] ?>" <?= $selectedBatch == $batch['id'] ? 'selected' : '' ?>><?= htmlspecialchars($batch['batch_name']) ?> - <?= htmlspecialchars($batch['course_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Time</th>
                        <th>Course</th>
                        <th>Batch</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Room</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($slots as $slot): ?>
                    <tr>
                        <td><span class="badge badge-info"><?= ucfirst($slot['day_of_week']) ?></span></td>
                        <td><?= date('h:i A', strtotime($slot['start_time'])) ?> - <?= date('h:i A', strtotime($slot['end_time'])) ?></td>
                        <td><?= htmlspecialchars($slot['course_name'] ?? '-') ?><br><small><?= htmlspecialchars($slot['course_code'] ?? '') ?></small></td>
                        <td><?= htmlspecialchars($slot['batch_name']) ?></td>
                        <td><?= htmlspecialchars($slot['subject_name']) ?></td>
                        <td><?= htmlspecialchars($slot['first_name'] . ' ' . $slot['last_name']) ?></td>
                        <td><?= htmlspecialchars($slot['room'] ?? '-') ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/timetables/<?= $slot['id'] ?>" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="<?= BASE_URL ?>/admin/delete-timetable/<?= $slot['id'] ?>" class="btn btn-sm btn-danger btn-delete" title="Delete"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($slots)): ?>
                    <tr><td colspan="8" style="text-align: center; color: var(--secondary-color);">No timetable slots found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
