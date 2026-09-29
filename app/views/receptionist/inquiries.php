<!-- Receptionist page for managing student inquiries. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Inquiries</h3>
        <button onclick="document.getElementById('inquiryForm').style.display='block'" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> New Inquiry</button>
    </div>
    <div class="card-body">
        <div id="inquiryForm" style="display: none; margin-bottom: 20px; padding: 20px; background: #f8fafc; border-radius: 8px;">
            <form method="POST" action="<?= BASE_URL ?>/receptionist/inquiries">
                <?= csrfField() ?>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Phone *</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Subject</label>
                        <select name="subject" class="form-select">
                            <option value="">Select Subject</option>
                            <?php foreach ($courses as $course): ?>
                            <option value="<?= htmlspecialchars($course['name']) ?>"><?= htmlspecialchars($course['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Record Inquiry</button>
                <button type="button" onclick="document.getElementById('inquiryForm').style.display='none'" class="btn btn-secondary">Cancel</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Name</th><th>Phone</th><th>Email</th><th>Course Interest</th><th>Message</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($inquiries as $inquiry): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($inquiry['name']) ?></strong></td>
                        <td><?= htmlspecialchars($inquiry['phone']) ?></td>
                        <td><?= htmlspecialchars($inquiry['email'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($inquiry['subject'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($inquiry['message'] ?? '-') ?></td>
                        <td><span class="badge badge-<?= $inquiry['status'] === 'new' ? 'warning' : 'success' ?>"><?= ucfirst($inquiry['status']) ?></span></td>
                        <td>
                            <?php if ($inquiry['status'] === 'new'): ?>
                            <a href="<?= BASE_URL ?>/receptionist/handle-inquiry/<?= $inquiry['id'] ?>" class="btn btn-sm btn-success" title="Mark Handled"><i class="fas fa-check"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($inquiries)): ?>
                    <tr><td colspan="7" style="text-align:center;color:var(--secondary-color);">No inquiries</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
