<!-- Admin page for viewing management reports. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Reports & Analytics</h3>
        <button onclick="window.print()" class="btn btn-sm btn-secondary"><i class="fas fa-print"></i> Print</button>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px;">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Students by Course</h3></div>
                <div class="card-body">
                    <table class="table">
                        <thead><tr><th>Course</th><th>Students</th></tr></thead>
                        <tbody>
                            <?php foreach ($reportData['students_by_course'] as $row): ?>
                            <tr><td><?= htmlspecialchars($row['name']) ?></td><td><?= $row['count'] ?></td></tr>
                            <?php endforeach; ?>
                            <?php if (empty($reportData['students_by_course'])): ?>
                            <tr><td colspan="2" style="text-align:center;color:var(--secondary-color);">No data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3 class="card-title">Revenue by Month</h3></div>
                <div class="card-body">
                    <table class="table">
                        <thead><tr><th>Month</th><th>Revenue</th></tr></thead>
                        <tbody>
                            <?php foreach ($reportData['revenue_by_month'] as $row): ?>
                            <tr><td><?= $row['month'] ?></td><td><?= formatCurrency($row['total']) ?></td></tr>
                            <?php endforeach; ?>
                            <?php if (empty($reportData['revenue_by_month'])): ?>
                            <tr><td colspan="2" style="text-align:center;color:var(--secondary-color);">No data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3 class="card-title">Top Students</h3></div>
                <div class="card-body">
                    <table class="table">
                        <thead><tr><th>Student</th><th>Avg Score</th></tr></thead>
                        <tbody>
                            <?php foreach ($reportData['top_students'] as $row): ?>
                            <tr><td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td><td><?= number_format($row['avg_score'], 1) ?>%</td></tr>
                            <?php endforeach; ?>
                            <?php if (empty($reportData['top_students'])): ?>
                            <tr><td colspan="2" style="text-align:center;color:var(--secondary-color);">No data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3 class="card-title">Batch Enrollment</h3></div>
                <div class="card-body">
                    <table class="table">
                        <thead><tr><th>Batch</th><th>Course</th><th>Enrolled</th></tr></thead>
                        <tbody>
                            <?php foreach ($reportData['batch_performance'] as $row): ?>
                            <tr><td><?= htmlspecialchars($row['batch_name']) ?></td><td><?= htmlspecialchars($row['course_name']) ?></td><td><?= $row['enrolled'] ?></td></tr>
                            <?php endforeach; ?>
                            <?php if (empty($reportData['batch_performance'])): ?>
                            <tr><td colspan="3" style="text-align:center;color:var(--secondary-color);">No data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
