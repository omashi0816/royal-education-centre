<!-- Manager page for reviewing pending approvals. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Pending Approvals</h3>
    </div>
    <div class="card-body">
        <h4 style="margin-bottom: 20px;">Courses</h4>
        <?php foreach ($pendingCourses as $course): ?>
        <div style="padding: 15px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 15px;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <strong><?= htmlspecialchars($course['name']) ?></strong> (<?= htmlspecialchars($course['code']) ?>)
                    <p style="color: var(--secondary-color); margin: 5px 0;"><?= htmlspecialchars($course['description']) ?></p>
                    <small>Fee: <?= formatCurrency($course['course_fee']) ?> | Duration: <?= $course['duration_months'] ?> months</small>
                </div>
                <div>
                    <form method="post" action="<?= BASE_URL ?>/manager/approve-course/<?= $course['id'] ?>" style="display:inline-block;">
                        <?= csrfField() ?>
                        <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check"></i> Approve</button>
                    </form>
                    <form method="post" action="<?= BASE_URL ?>/manager/reject-course/<?= $course['id'] ?>" style="display:inline-block;">
                        <?= csrfField() ?>
                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-times"></i> Reject</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($pendingCourses)): ?>
        <p style="color: var(--secondary-color);">No pending course approvals</p>
        <?php endif; ?>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #e2e8f0;">
        
        <h4 style="margin-bottom: 20px;">Batches</h4>
        <?php foreach ($pendingBatches as $batch): ?>
        <div style="padding: 15px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 15px;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <strong><?= htmlspecialchars($batch['batch_name']) ?></strong>
                    <p style="color: var(--secondary-color); margin: 5px 0;">Course: <?= htmlspecialchars($batch['course_name']) ?></p>
                    <small><?= formatDate($batch['start_date']) ?> - <?= formatDate($batch['end_date']) ?></small>
                </div>
                <div>
                    <form method="post" action="<?= BASE_URL ?>/manager/approve-batch/<?= $batch['id'] ?>" style="display:inline-block;">
                        <?= csrfField() ?>
                        <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check"></i> Approve</button>
                    </form>
                    <form method="post" action="<?= BASE_URL ?>/manager/reject-batch/<?= $batch['id'] ?>" style="display:inline-block;">
                        <?= csrfField() ?>
                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-times"></i> Reject</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($pendingBatches)): ?>
        <p style="color: var(--secondary-color);">No pending batch approvals</p>
        <?php endif; ?>
    </div>
</div>
