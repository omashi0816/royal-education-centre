<!-- Admin form for creating or editing a timetable entry. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= isset($slot) ? 'Edit Timetable Slot' : 'Create Timetable Slot' ?></h3>
        <a href="<?= BASE_URL ?>/admin/timetables" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/admin/<?= isset($slot) ? 'timetables/' . $slot['id'] : 'create-timetable' ?>">
            <?= csrfField() ?>
            <div class="form-group">
                <label>Batch *</label>
                <select name="batch_id" class="form-select" required>
                    <?php foreach ($batches as $batch): ?>
                    <option value="<?= $batch['id'] ?>" <?= isset($slot) && $slot['batch_id'] == $batch['id'] ? 'selected' : '' ?>><?= htmlspecialchars($batch['batch_name']) ?> - <?= htmlspecialchars($batch['course_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Subject *</label>
                <select name="subject_id" class="form-select" required>
                    <?php foreach ($subjects as $subject): ?>
                    <option value="<?= $subject['id'] ?>" <?= isset($slot) && $slot['subject_id'] == $subject['id'] ? 'selected' : '' ?>><?= htmlspecialchars($subject['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Teacher *</label>
                <select name="teacher_id" class="form-select" required>
                    <?php foreach ($teachers as $teacher): ?>
                    <option value="<?= $teacher['id'] ?>" <?= isset($slot) && $slot['teacher_id'] == $teacher['id'] ? 'selected' : '' ?>><?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Day *</label>
                <select name="day_of_week" class="form-select" required>
                    <?php $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday']; ?>
                    <?php foreach ($days as $day): ?>
                    <option value="<?= $day ?>" <?= isset($slot) && $slot['day_of_week'] == $day ? 'selected' : '' ?>><?= ucfirst($day) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Start Time *</label>
                    <input type="time" name="start_time" class="form-control" value="<?= $slot['start_time'] ?? '' ?>" required>
                </div>
                <div class="form-group">
                    <label>End Time *</label>
                    <input type="time" name="end_time" class="form-control" value="<?= $slot['end_time'] ?? '' ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Room</label>
                <input type="text" name="room" class="form-control" value="<?= htmlspecialchars($slot['room'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Slot</button>
        </form>
    </div>
</div>
