<!-- Admin page for viewing notice details. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Notice Details</h3>
        <div>
            <a href="<?= BASE_URL ?>/admin/edit-notice/<?= $notice['id'] ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="<?= BASE_URL ?>/admin/notices" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div style="margin-bottom: 20px;">
            <h4><?= htmlspecialchars($notice['title']) ?></h4>
            <div style="display: flex; gap: 20px; font-size: 0.875rem; color: var(--secondary-color); margin-top: 10px; flex-wrap: wrap;">
                <span><i class="fas fa-user"></i> <?= htmlspecialchars($notice['posted_by_name']) ?></span>
                <span><i class="fas fa-calendar"></i> <?= formatDateTime($notice['created_at']) ?></span>
                <span><i class="fas fa-users"></i> Target: <?= ucfirst($notice['target_role']) ?></span>
                <?php if ($notice['is_pinned']): ?>
                <span><i class="fas fa-thumbtack"></i> Pinned</span>
                <?php endif; ?>
            </div>
        </div>
        <div style="background: #f8fafc; padding: 20px; border-radius: 8px; line-height: 1.8;">
            <?= nl2br(htmlspecialchars($notice['content'])) ?>
        </div>
    </div>
</div>
