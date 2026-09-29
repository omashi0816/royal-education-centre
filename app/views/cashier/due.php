<!-- Cashier page for reviewing outstanding balances. -->
<div class="card">
    <div class="card-header"><h3 class="card-title">Due Payments</h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Student</th><th>Course</th><th>Paid</th><th>Total</th><th>Due</th></tr></thead>
                <tbody>
                    <?php foreach ($duePayments as $due): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($due['student_code']) ?></strong><br><?= htmlspecialchars($due['first_name'] . ' ' . $due['last_name']) ?></td>
                        <td><?= htmlspecialchars($due['course_name']) ?></td>
                        <td><?= formatCurrency($due['paid_amount'] ?? 0) ?></td>
                        <td><?= formatCurrency($due['total']) ?></td>
                        <td><strong style="color: #ef4444;"><?= formatCurrency($due['due_amount']) ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($duePayments)): ?>
                    <tr><td colspan="5" style="text-align:center;color:var(--secondary-color);">No due payments</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
