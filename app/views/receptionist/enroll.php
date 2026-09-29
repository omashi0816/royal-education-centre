<!-- Receptionist page for enrolling students in courses and batches. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Enroll Student</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/receptionist/enroll">
            <?= csrfField() ?>
            <div class="form-group">
                <label>Select Student *</label>
                <select name="student_id" class="form-select" required>
                    <option value="">Select Student</option>
                    <?php foreach ($students as $student): ?>
                    <option value="<?= $student['id'] ?>"><?= htmlspecialchars($student['student_code']) ?> - <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Select Batch *</label>
                <select name="batch_id" class="form-select" required>
                    <option value="">Select Batch</option>
                    <?php foreach ($batches as $batch): ?>
                    <option value="<?= $batch['id'] ?>"><?= htmlspecialchars($batch['batch_name']) ?> - <?= htmlspecialchars($batch['course_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enroll Student</button>
        </form>
    </div>
</div>
