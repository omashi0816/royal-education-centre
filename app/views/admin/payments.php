<!-- Admin page for reviewing payment records. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Payment Management</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>/admin/payments" style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
            <select name="status" class="form-select" style="max-width: 150px;">
                <option value="">All Status</option>
                <option value="completed" <?= $filters['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                <option value="pending" <?= $filters['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="failed" <?= $filters['status'] === 'failed' ? 'selected' : '' ?>>Failed</option>
            </select>
            <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($filters['date_from']) ?>" style="max-width: 150px;">
            <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($filters['date_to']) ?>" style="max-width: 150px;">
            <button type="submit" class="btn btn-secondary">
                <i class="fas fa-filter"></i> Filter
            </button>
            <a href="<?= BASE_URL ?>/admin/payments" class="btn btn-secondary">
                <i class="fas fa-times"></i> Clear
            </a>
        </form>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Receipt</th>
                        <th>Student</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td>
                            <?php if ($payment['receipt_no']): ?>
                            <strong><?= htmlspecialchars($payment['receipt_no']) ?></strong>
                            <?php else: ?>
                            -
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($payment['student_code']) ?></strong><br>
                            <small><?= htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']) ?></small>
                        </td>
                        <td><?= ucfirst(str_replace('_', ' ', $payment['payment_type'])) ?></td>
                        <td><?= formatCurrency($payment['final_amount']) ?></td>
                        <td><?= ucfirst($payment['payment_method']) ?></td>
                        <td><?= formatDate($payment['payment_date']) ?></td>
                        <td>
                            <span class="badge badge-<?= $payment['status'] === 'completed' ? 'success' : ($payment['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                <?= ucfirst($payment['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--secondary-color);">No payments found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
