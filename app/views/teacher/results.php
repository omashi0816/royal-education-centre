<!-- Teacher page for entering and managing student results. -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-header">
        <div>
            <h3 class="card-title">Enter Student Results</h3>
            <p style="margin: 5px 0 0; color: var(--secondary-color);">Select an exam or assignment to enter, update, or delete student marks.</p>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom: 20px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-alt"></i> Exam Results</h3>
        <a href="<?= BASE_URL ?>/teacher/exams" class="btn btn-sm btn-secondary"><i class="fas fa-plus"></i> Create Exam</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Exam</th><th>Batch</th><th>Subject</th><th>Date</th><th>Marks</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach ($exams as $exam): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($exam['title']) ?></strong></td>
                        <td><?= htmlspecialchars($exam['batch_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($exam['subject_name'] ?? '-') ?></td>
                        <td><?= formatDateTime($exam['exam_date']) ?></td>
                        <td><?= (int) $exam['total_marks'] ?></td>
                        <td><a href="<?= BASE_URL ?>/teacher/mark-results/<?= (int) $exam['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-pen"></i> Enter Marks</a></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($exams)): ?>
                    <tr><td colspan="6" style="text-align:center;color:var(--secondary-color);">No exams available.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-tasks"></i> Assignment Results</h3>
        <a href="<?= BASE_URL ?>/teacher/assignments" class="btn btn-sm btn-secondary"><i class="fas fa-plus"></i> Create Assignment</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Assignment</th><th>Batch</th><th>Subject</th><th>Due Date</th><th>Max Marks</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach ($assignments as $assignment): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($assignment['title']) ?></strong></td>
                        <td><?= htmlspecialchars($assignment['batch_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($assignment['subject_name'] ?? '-') ?></td>
                        <td><?= formatDateTime($assignment['due_date']) ?></td>
                        <td><?= (int) $assignment['max_marks'] ?></td>
                        <td><a href="<?= BASE_URL ?>/teacher/grade-assignment/<?= (int) $assignment['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-pen"></i> Enter Marks</a></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($assignments)): ?>
                    <tr><td colspan="6" style="text-align:center;color:var(--secondary-color);">No assignments available.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
