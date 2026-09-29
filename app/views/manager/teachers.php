<!-- Manager page for reviewing teacher records. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Teacher Records</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr><th>Code</th><th>Name</th><th>Specialization</th><th>Experience</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($teachers as $teacher): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($teacher['teacher_code']) ?></strong></td>
                        <td><?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?></td>
                        <td><?= htmlspecialchars($teacher['specialization'] ?? '-') ?></td>
                        <td><?= $teacher['experience_years'] ?> years</td>
                        <td><span class="badge badge-<?= $teacher['status'] === 'active' ? 'success' : 'danger' ?>"><?= ucfirst($teacher['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($teachers)): ?>
                    <tr><td colspan="5" style="text-align:center;color:var(--secondary-color);">No teachers found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
