<!-- Admin page for viewing subject details. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Subject Details</h3>
        <a href="<?= BASE_URL ?>/admin/subjects" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div><strong>Code:</strong> <?= htmlspecialchars($subject['code']) ?></div>
            <div><strong>Name:</strong> <?= htmlspecialchars($subject['name']) ?></div>
            <div style="grid-column: span 2;"><strong>Description:</strong> <?= htmlspecialchars($subject['description'] ?? 'N/A') ?></div>
        </div>
        <a href="<?= BASE_URL ?>/admin/edit-subject/<?= $subject['id'] ?>" class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a>
        <a href="<?= BASE_URL ?>/admin/delete-subject/<?= $subject['id'] ?>" class="btn btn-danger btn-delete"><i class="fas fa-trash"></i> Delete</a>
    </div>
</div>
