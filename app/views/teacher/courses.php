<!-- Teacher page for viewing assigned courses. -->
<div class="card">
    <div class="card-header"><h3 class="card-title">My Courses</h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Batch</th><th>Course</th><th>Subject</th><th>Day</th><th>Time</th></tr></thead>
                <tbody>
                    <?php foreach ($batches as $batch): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($batch['batch_name'] ?? 'N/A') ?></strong></td>
                        <td><?= htmlspecialchars($batch['course_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($batch['subject_name'] ?? 'N/A') ?></td>
                        <td><?= ucfirst($batch['day_of_week'] ?? '-') ?></td>
                        <td><?= isset($batch['start_time']) ? date('h:i A', strtotime($batch['start_time'])) . ' - ' . date('h:i A', strtotime($batch['end_time'])) : '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($batches)): ?>
                    <tr><td colspan="5" style="text-align:center;color:var(--secondary-color);">No courses assigned</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
