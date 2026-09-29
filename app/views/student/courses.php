<!-- Student page for viewing enrolled courses. -->
<div class="card">
    <div class="card-header"><h3 class="card-title">My Courses</h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Batch</th><th>Course</th><th>Enrolled Date</th><th>Status</th></tr></thead>
                <tbody>
                    <?php foreach ($enrollments as $enrollment): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($enrollment['batch_name'] ?? 'N/A') ?></strong></td>
                        <td><?= htmlspecialchars($enrollment['course_name'] ?? 'N/A') ?></td>
                        <td><?= formatDate($enrollment['enrolled_date']) ?></td>
                        <td><span class="badge badge-<?= $enrollment['status'] === 'active' ? 'success' : 'info' ?>"><?= ucfirst($enrollment['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($enrollments)): ?>
                    <tr><td colspan="4" style="text-align:center;color:var(--secondary-color);">No courses enrolled</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header"><h3 class="card-title">Available Classes & Courses</h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Course</th><th>Class / Batch</th><th>Monthly Fee</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach ($availableCourses as $course): ?>
                    <tr>
                        <td><?= htmlspecialchars($course['course_name']) ?></td>
                        <td><?= htmlspecialchars($course['batch_name']) ?></td>
                        <td><?= formatCurrency($course['monthly_fee']) ?></td>
                        <td>
                            <?php if (in_array((int) $course['batch_id'], $registeredBatchIds, true)): ?>
                                <span class="badge badge-success">Registered</span>
                            <?php else: ?>
                                <form method="post" action="<?= BASE_URL ?>/student/register-course/<?= (int) $course['batch_id'] ?>">
                                    <?= csrfField() ?>
                                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-user-plus"></i> Register</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($availableCourses)): ?>
                    <tr><td colspan="4" style="text-align:center;color:var(--secondary-color);">No active classes or courses available</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
