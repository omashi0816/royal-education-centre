<!-- Teacher page for reviewing student payments. -->
<div class="card">
    <div class="card-header"><h3 class="card-title">Payments by My Students</h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr><th>Student</th><th>Course</th><th>Batch</th><th>Payment Type</th><th>Amount</th><th>Method</th><th>Date</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                    <?php
                        $statusClass = $payment['status'] === 'completed' ? 'success' : ($payment['status'] === 'failed' ? 'danger' : 'warning');
                        $statusText = $payment['status'] === 'completed' ? 'Successful' : ($payment['status'] === 'pending' ? 'Pending' : 'Rejected');
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($payment['student_code']) ?></strong><br><?= htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']) ?></td>
                        <td><?= htmlspecialchars($payment['course_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($payment['batch_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $payment['payment_type']))) ?></td>
                        <td><?= formatCurrency($payment['final_amount']) ?></td>
                        <td><?= htmlspecialchars($payment['payment_method'] === 'bank_transfer' ? 'Online Banking' : ucfirst($payment['payment_method'])) ?></td>
                        <td><?= formatDate($payment['payment_date']) ?></td>
                        <td><span class="badge badge-<?= $statusClass ?>"><?= $statusText ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($payments)): ?>
                    <tr><td colspan="8" style="text-align:center;color:var(--secondary-color);">No payments found for your students</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
