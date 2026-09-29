<!-- Admin page for viewing staff details. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Staff Details</h3>
        <a href="<?= BASE_URL ?>/admin/staff" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            <div><strong>Staff Code:</strong> <?= htmlspecialchars($staff['staff_code']) ?></div>
            <div><strong>Name:</strong> <?= htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']) ?></div>
            <div><strong>Email:</strong> <?= htmlspecialchars($staff['email']) ?></div>
            <div><strong>Phone:</strong> <?= htmlspecialchars($staff['phone'] ?? '-') ?></div>
            <div><strong>Designation:</strong> <?= htmlspecialchars($staff['designation'] ?? '-') ?></div>
            <div><strong>Department:</strong> <?= htmlspecialchars($staff['department'] ?? '-') ?></div>
            <div><strong>Joining Date:</strong> <?= formatDate($staff['hire_date'] ?? '') ?></div>
            <div><strong>Status:</strong> <span class="badge badge-<?= $staff['status'] === 'active' ? 'success' : 'danger' ?>"><?= ucfirst($staff['status']) ?></span></div>
        </div>
    </div>
</div>
