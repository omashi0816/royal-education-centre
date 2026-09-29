<!-- Teacher page for viewing income records. -->
<div class="card">
    <div class="card-header"><h3 class="card-title">My Income</h3></div>
    <div class="card-body">
        <div style="margin-bottom: 30px; padding: 20px; background: #e0f2fe; border-radius: 8px; text-align: center;">
            <h2 style="margin: 0; color: #0369a1;"><?= formatCurrency($totalIncome ?? 0) ?></h2>
            <p style="margin: 10px 0 0 0; color: #0369a1;">Total Income</p>
        </div>
        <h4>Payment History</h4>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Student</th><th>Type</th><th>Amount</th><th>Date</th></tr></thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($payment['student_code']) ?></strong><br><?= htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']) ?></td>
                        <td><?= ucfirst(str_replace('_', ' ', $payment['payment_type'])) ?></td>
                        <td><?= formatCurrency($payment['final_amount']) ?></td>
                        <td><?= formatDate($payment['payment_date']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($payments)): ?>
                    <tr><td colspan="4" style="text-align:center;color:var(--secondary-color);">No payments recorded</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
