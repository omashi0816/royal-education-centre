<!-- Student page for viewing personal payment records. -->
<div class="card" style="max-width: 900px; margin-bottom: 20px;">
    <div class="card-header"><h3 class="card-title">Online Payment</h3></div>
    <div class="card-body">
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px 28px;">
                <div><small style="color: var(--secondary-color);">Student Name</small><div style="font-weight: 700;"><?= htmlspecialchars(trim(($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? '')) ?: currentUserName()) ?></div></div>
                <div><small style="color: var(--secondary-color);">Student ID</small><div style="font-weight: 700;"><?= htmlspecialchars($student['student_code'] ?? '-') ?></div></div>
                <div><small style="color: var(--secondary-color);">Courses</small><div style="font-weight: 700;"><?= count($monthlyDues) ?> active course<?= count($monthlyDues) === 1 ? '' : 's' ?></div></div>
                <div><small style="color: var(--secondary-color);">Payment Type</small><div style="font-weight: 700;">Monthly Fee</div></div>
                <div><small style="color: var(--secondary-color);">Monthly Fees</small><div style="font-weight: 700;">Rs. <?= number_format(array_sum(array_column($monthlyDues, 'monthly_fee')), 2) ?></div></div>
                <div><small style="color: var(--secondary-color);">Due Amount</small><div style="font-weight: 700;">Rs. <?= number_format($dueAmount, 2) ?></div></div>
                <div><small style="color: var(--secondary-color);">Discount</small><div style="font-weight: 700;">Rs. 0.00</div></div>
                <div><small style="color: var(--secondary-color);">Amount to Pay</small><div style="font-size: 1.2rem; font-weight: 700; color: var(--primary-color);">Rs. <?= number_format($dueAmount, 2) ?></div></div>
            </div>
        </div>

        <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 18px; margin-bottom: 24px;">
            <h4 style="margin: 0 0 14px;">Phone Verification</h4>
            <form method="post" style="display: grid; grid-template-columns: minmax(220px, 1fr) auto; gap: 12px; align-items: end;">
                <?= csrfField() ?>
                <input type="hidden" name="payment_action" value="send_otp">
                <div class="form-group" style="margin: 0;">
                    <label for="otp_phone">Phone Number</label>
                    <input type="text" id="otp_phone" name="otp_phone" class="form-control" value="<?= htmlspecialchars($otpPhone) ?>" placeholder="07XXXXXXXX" required>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-mobile-alt"></i> Send OTP</button>
            </form>

            <?php if ($otpPhone && !$otpVerified): ?>
            <?php if (!empty($otpDemoCode)): ?>
            <p style="margin: 14px 0 0; color: #92400e; font-weight: 600;">Development demo OTP: <strong><?= htmlspecialchars($otpDemoCode) ?></strong></p>
            <?php endif; ?>
            <form method="post" style="display: grid; grid-template-columns: minmax(180px, 1fr) auto; gap: 12px; align-items: end; margin-top: 14px;">
                <?= csrfField() ?>
                <input type="hidden" name="payment_action" value="verify_otp">
                <div class="form-group" style="margin: 0;">
                    <label for="otp_code">OTP Code</label>
                    <input type="text" id="otp_code" name="otp_code" class="form-control" inputmode="numeric" maxlength="6" placeholder="Enter 6-digit OTP" required>
                </div>
                <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Verify OTP</button>
            </form>
            <?php elseif ($otpVerified): ?>
            <p style="margin: 14px 0 0; color: #166534; font-weight: 600;"><i class="fas fa-check-circle"></i> Phone number verified. You can submit the payment below.</p>
            <?php endif; ?>
        </div>

        <form method="post">
            <?= csrfField() ?>
            <input type="hidden" name="payment_action" value="submit_payment">
            <input type="hidden" name="payment_type" value="monthly_fee">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="enrollment_id">Payment For</label>
                    <select id="enrollment_id" name="enrollment_id" class="form-select" required>
                        <option value="all" data-due="<?= htmlspecialchars(number_format($dueAmount, 2, '.', '')) ?>">All courses due (Rs. <?= number_format($dueAmount, 2) ?>)</option>
                        <?php foreach ($monthlyDues as $monthlyDue): ?>
                        <option value="<?= (int) $monthlyDue['enrollment_id'] ?>" data-due="<?= htmlspecialchars(number_format((float) $monthlyDue['due_amount'], 2, '.', '')) ?>">
                            <?= htmlspecialchars($monthlyDue['course_name']) ?> - Due Rs. <?= number_format((float) $monthlyDue['due_amount'], 2) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="payment_amount">Amount</label>
                    <input type="number" id="payment_amount" name="amount" class="form-control" min="0.01" step="0.01" max="<?= htmlspecialchars(number_format($dueAmount, 2, '.', '')) ?>" value="<?= htmlspecialchars($dueAmount > 0 ? number_format($dueAmount, 2, '.', '') : '') ?>" required>
                </div>

                <div class="form-group">
                    <label>Payment Method</label>
                    <div style="display: flex; gap: 18px; padding-top: 10px;">
                        <label style="font-weight: 400;"><input type="radio" name="payment_method" value="card"> Card Payment</label>
                        <label style="font-weight: 400;"><input type="radio" name="payment_method" value="online_banking" checked> Online Banking</label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Bank Name</label>
                    <input type="text" name="bank_name" class="form-control" placeholder="Bank used for transfer" required>
                </div>

                <div class="form-group">
                    <label>Account Name</label>
                    <input type="text" name="account_name" class="form-control" placeholder="Account holder name" required>
                </div>

                <div class="form-group">
                    <label>Account Number</label>
                    <input type="text" name="account_number" class="form-control" placeholder="Account number" required>
                </div>

                <div class="form-group">
                    <label>Transfer Date</label>
                    <input type="date" name="transfer_date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Transaction / Reference Number</label>
                    <input type="text" name="transaction_id" class="form-control" placeholder="Bank transaction reference" required>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Payment Purpose</label>
                    <input type="text" name="payment_purpose" class="form-control" placeholder="e.g. Course Fee / Monthly Fee / Registration Fee" required>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Payment Status</label>
                    <input type="text" class="form-control" value="Pending - Waiting for Admin/Cashier verification" readonly>
                </div>
            </div>

            <div style="margin-top: 16px; display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-file-invoice-dollar"></i> Submit Payment Request</button>
                <a href="<?= BASE_URL ?>/student/checkout" class="btn btn-success"><i class="fas fa-credit-card"></i> Checkout</a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('enrollment_id').addEventListener('change', function () {
    var selected = this.options[this.selectedIndex];
    var due = selected.getAttribute('data-due') || '0.00';
    var amount = document.getElementById('payment_amount');
    amount.max = due;
    amount.value = parseFloat(due) > 0 ? due : '';
});
</script>

<?php if ($latestPayment): ?>
<div class="card" style="max-width: 900px; margin-bottom: 20px;">
    <div class="card-header"><h3 class="card-title">Latest Payment Status</h3></div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; align-items: end;">
            <div><small style="color: var(--secondary-color);">Payment Status</small><div style="font-weight: 700; color: <?= $latestPayment['status'] === 'completed' ? '#166534' : ($latestPayment['status'] === 'failed' ? '#991b1b' : '#92400e') ?>;"><?= $latestPayment['status'] === 'completed' ? 'Successful' : ($latestPayment['status'] === 'pending' ? 'Pending' : 'Rejected') ?></div></div>
            <div><small style="color: var(--secondary-color);">Transaction ID</small><div style="font-weight: 700;"><?= htmlspecialchars($latestPayment['transaction_id'] ?? '-') ?></div></div>
            <div><small style="color: var(--secondary-color);">Payment Date</small><div style="font-weight: 700;"><?= formatDate($latestPayment['payment_date']) ?></div></div>
            <?php if (!empty($latestPayment['receipt_no'])): ?>
            <div><small style="color: var(--secondary-color);">Receipt</small><div style="font-weight: 700;"><?= htmlspecialchars($latestPayment['receipt_no']) ?></div></div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header"><h3 class="card-title">My Payments</h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Receipt</th><th>Type</th><th>Amount</th><th>Method</th><th>Date</th><th>Status</th></tr></thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($payment['receipt_no'] ?? 'N/A') ?></strong></td>
                        <td><?= ucfirst(str_replace('_', ' ', $payment['payment_type'])) ?></td>
                        <td><?= formatCurrency($payment['final_amount']) ?></td>
                        <td><?= ucfirst($payment['payment_method']) ?></td>
                        <td><?= formatDate($payment['payment_date']) ?></td>
                        <td><span class="badge badge-<?= $payment['status'] === 'completed' ? 'success' : ($payment['status'] === 'failed' ? 'danger' : 'warning') ?>"><?= $payment['status'] === 'completed' ? 'Successful' : ($payment['status'] === 'pending' ? 'Pending' : 'Rejected') ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($payments)): ?>
                    <tr><td colspan="6" style="text-align:center;color:var(--secondary-color);">No payments made</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
