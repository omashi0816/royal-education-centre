<!-- Cashier page for viewing financial reports. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Financial Reports</h3>
        <button onclick="window.print()" class="btn btn-sm btn-secondary"><i class="fas fa-print"></i> Print</button>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px;">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Payment Summary</h3></div>
                <div class="card-body">
                    <table class="table">
                        <thead><tr><th>Type</th><th>Count</th><th>Collected</th><th>Pending</th></tr></thead>
                        <tbody>
                            <?php foreach ($summary as $row): ?>
                            <tr>
                                <td><?= ucfirst(str_replace('_', ' ', $row['payment_type'])) ?></td>
                                <td><?= $row['total_payments'] ?></td>
                                <td><?= formatCurrency($row['total_collected']) ?></td>
                                <td><?= formatCurrency($row['total_pending']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($summary)): ?>
                            <tr><td colspan="4" style="text-align:center;color:var(--secondary-color);">No data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3 class="card-title">Daily Collection (30 Days)</h3></div>
                <div class="card-body">
                    <table class="table">
                        <thead><tr><th>Date</th><th>Count</th><th>Total</th></tr></thead>
                        <tbody>
                            <?php foreach ($dailyCollection as $row): ?>
                            <tr><td><?= $row['date'] ?></td><td><?= $row['count'] ?></td><td><?= formatCurrency($row['total']) ?></td></tr>
                            <?php endforeach; ?>
                            <?php if (empty($dailyCollection)): ?>
                            <tr><td colspan="3" style="text-align:center;color:var(--secondary-color);">No data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3 class="card-title">Monthly Collection</h3></div>
                <div class="card-body">
                    <table class="table">
                        <thead><tr><th>Month</th><th>Count</th><th>Total</th></tr></thead>
                        <tbody>
                            <?php foreach ($monthlyCollection as $row): ?>
                            <tr><td><?= $row['month'] ?></td><td><?= $row['count'] ?></td><td><?= formatCurrency($row['total']) ?></td></tr>
                            <?php endforeach; ?>
                            <?php if (empty($monthlyCollection)): ?>
                            <tr><td colspan="3" style="text-align:center;color:var(--secondary-color);">No data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3 class="card-title">Payment Methods</h3></div>
                <div class="card-body">
                    <table class="table">
                        <thead><tr><th>Method</th><th>Count</th><th>Total</th></tr></thead>
                        <tbody>
                            <?php foreach ($paymentMethods as $row): ?>
                            <tr><td><?= ucfirst($row['payment_method']) ?></td><td><?= $row['count'] ?></td><td><?= formatCurrency($row['total']) ?></td></tr>
                            <?php endforeach; ?>
                            <?php if (empty($paymentMethods)): ?>
                            <tr><td colspan="3" style="text-align:center;color:var(--secondary-color);">No data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
