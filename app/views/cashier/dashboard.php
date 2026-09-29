<!-- Cashier dashboard showing payment and collection summaries. -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-info">
            <h4><?= formatCurrency($stats['today_collection']) ?></h4>
            <p>Today Collection</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-calendar"></i>
        </div>
        <div class="stat-info">
            <h4><?= formatCurrency($stats['month_collection']) ?></h4>
            <p>Month Collection</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="stat-info">
            <h4><?= $stats['due_payments'] ?></h4>
            <p>Due Payments</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon danger">
            <i class="fas fa-receipt"></i>
        </div>
        <div class="stat-info">
            <h4><?= $stats['total_transactions'] ?></h4>
            <p>Total Transactions</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Recent Payments</h3>
        <a href="<?= BASE_URL ?>/cashier/payments" class="btn btn-sm btn-secondary">View All</a>
    </div>
    <div class="card-body">
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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentPayments as $payment): ?>
                    <tr>
                        <td>
                            <?php if ($payment['receipt_no'] ?? null): ?>
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
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentPayments)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--secondary-color);">No recent payments</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
