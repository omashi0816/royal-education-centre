<!-- Admin form for creating or editing a subject. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= isset($subject) ? 'Edit Subject' : 'Create Subject' ?></h3>
        <a href="<?= BASE_URL ?>/admin/subjects" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/admin/<?= isset($subject) ? 'edit-subject/' . $subject['id'] : 'create-subject' ?>">
            <?= csrfField() ?>
            <div class="form-group">
                <label>Subject Code *</label>
                <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($subject['code'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Subject Name *</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($subject['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($subject['description'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Subject</button>
        </form>
    </div>
</div>
