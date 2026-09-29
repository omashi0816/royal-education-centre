<!-- Cashier page for reviewing payment transactions. -->
<div class="card">
    <div class="card-header"><h3 class="card-title">All Payments</h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Receipt</th><th>Student</th><th>Type</th><th>Amount</th><th>Method</th><th>Date</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                    <?php
                        $statusText = match ($payment['status']) {
                            'completed' => 'Verified',
                            'pending' => 'Pending',
                            'failed' => 'Rejected',
                            default => ucfirst($payment['status'])
                        };
                        $statusClass = match ($payment['status']) {
                            'completed' => 'success',
                            'pending' => 'warning',
                            'failed' => 'danger',
                            default => 'secondary'
                        };
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($payment['receipt_no'] ?? 'N/A') ?></strong></td>
                        <td><strong><?= htmlspecialchars($payment['student_code']) ?></strong><br><?= htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']) ?></td>
                        <td><?= ucfirst(str_replace('_', ' ', $payment['payment_type'])) ?></td>
                        <td><?= formatCurrency($payment['final_amount']) ?></td>
                        <td><?= ucfirst($payment['payment_method']) ?></td>
                        <td><?= formatDate($payment['payment_date']) ?></td>
                        <td><span class="badge badge-<?= $statusClass ?>"><?= $statusText ?></span></td>
                        <td>
                            <?php if ($payment['status'] === 'pending'): ?>
                                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                    <form method="post" action="<?= BASE_URL ?>/cashier/approve-payment/<?= (int)$payment['id'] ?>">
                                        <?= csrfField() ?>
                                        <button type="submit" class="btn btn-sm btn-success">Verify</button>
                                    </form>
                                    <form method="post" action="<?= BASE_URL ?>/cashier/reject-payment/<?= (int)$payment['id'] ?>">
                                        <?= csrfField() ?>
                                        <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span style="color: var(--secondary-color);">Done</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($payments)): ?>
                    <tr><td colspan="8" style="text-align:center;color:var(--secondary-color);">No payments found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
