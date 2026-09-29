<!-- Admin page for managing system settings. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">System Settings</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/admin/settings">
            <?= csrfField() ?>
            <?php foreach ($settings as $group => $items): ?>
            <h4 style="margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;"><?= ucfirst($group) ?></h4>
            <?php foreach ($items as $item): ?>
            <div class="form-group">
                <label><?= ucwords(str_replace('_', ' ', $item['setting_key'])) ?></label>
                <input type="text" name="<?= $item['setting_key'] ?>" class="form-control" value="<?= htmlspecialchars($item['setting_value']) ?>">
            </div>
            <?php endforeach; ?>
            <?php endforeach; ?>
            <?php if (empty($settings)): ?>
            <p style="color: var(--secondary-color);">No settings configured yet. Settings will appear here after initial setup.</p>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Settings</button>
        </form>
    </div>
</div>
