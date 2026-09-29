<!-- Admin page for managing notices. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Notice Management</h3>
        <a href="<?= BASE_URL ?>/admin/create-notice" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Notice
        </a>
    </div>
    <div class="card-body">
        <?php foreach ($notices as $notice): ?>
        <div style="padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 15px;">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                <h4 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                    <?= htmlspecialchars($notice['title']) ?>
                    <?php if ($notice['is_pinned']): ?>
                    <span class="badge badge-warning"><i class="fas fa-thumbtack"></i> Pinned</span>
                    <?php endif; ?>
                </h4>
                <div>
                    <a href="<?= BASE_URL ?>/admin/notice/<?= $notice['id'] ?>" class="btn btn-sm btn-secondary">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="<?= BASE_URL ?>/admin/edit-notice/<?= $notice['id'] ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="<?= BASE_URL ?>/admin/delete-notice/<?= $notice['id'] ?>" class="btn btn-sm btn-danger btn-delete">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </div>
            <p style="color: var(--secondary-color); margin-bottom: 10px;"><?= htmlspecialchars($notice['content']) ?></p>
            <div style="display: flex; gap: 15px; font-size: 0.875rem; color: var(--secondary-color);">
                <span><i class="fas fa-user"></i> <?= htmlspecialchars($notice['posted_by_name']) ?></span>
                <span><i class="fas fa-calendar"></i> <?= formatDateTime($notice['created_at']) ?></span>
                <span><i class="fas fa-bullhorn"></i> Target: <?= ucfirst($notice['target_role']) ?></span>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($notices)): ?>
        <p style="color: var(--secondary-color); text-align: center;">No notices found</p>
        <?php endif; ?>
    </div>
</div>
