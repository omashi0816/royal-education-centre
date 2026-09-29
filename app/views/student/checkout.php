<div style="max-width: 1040px;">
    <div style="display: flex; justify-content: space-between; gap: 20px; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap;">
        <div>
            <p style="color: var(--secondary-color); margin-bottom: 6px;">Student payments</p>
            <h2 style="margin-bottom: 8px;">Checkout</h2>
            <p style="color: var(--secondary-color);">Review your fee and choose a payment method to continue.</p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: minmax(0, 1.35fr) minmax(280px, 0.65fr); gap: 20px; align-items: start;">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Payment details</h3></div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
                    <div><small style="color: var(--secondary-color);">Student</small><div style="font-weight: 700; margin-top: 4px;"><?= htmlspecialchars(trim(($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? '')) ?: currentUserName()) ?></div></div>
                    <div><small style="color: var(--secondary-color);">Student ID</small><div style="font-weight: 700; margin-top: 4px;"><?= htmlspecialchars($student['student_code'] ?? '-') ?></div></div>
                    <div><small style="color: var(--secondary-color);">Course</small><div style="font-weight: 700; margin-top: 4px;"><?= htmlspecialchars($currentEnrollment['course_name'] ?? 'Education fees') ?></div></div>
                </div>

                <form method="post" action="<?= BASE_URL ?>/student/checkout">
                    <?= csrfField() ?>
                    <input type="hidden" name="enrollment_id" value="<?= (int) ($currentEnrollment['id'] ?? 0) ?>">
                    <div class="form-group">
                        <label for="amount">Amount to pay</label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 12px; top: 10px; color: var(--secondary-color);">Rs.</span>
                            <input type="number" id="amount" name="amount" class="form-control" style="padding-left: 42px; font-size: 1.1rem;" min="1" step="0.01" value="<?= htmlspecialchars($dueAmount > 0 ? number_format($dueAmount, 2, '.', '') : '') ?>" required>
                        </div>
                        <small style="color: var(--secondary-color);">Current estimated monthly balance: Rs. <?= number_format($dueAmount, 2) ?></small>
                    </div>

                    <div class="form-group">
                        <label>Payment method</label>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px;">
                            <label style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 14px; font-weight: 400; cursor: pointer;">
                                <input type="radio" name="payment_method" value="card" required> <i class="fas fa-credit-card"></i> Card
                                <small style="display: block; color: var(--secondary-color); margin: 6px 0 0 20px;">Visa or Mastercard</small>
                            </label>
                            <label style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 14px; font-weight: 400; cursor: pointer;">
                                <input type="radio" name="payment_method" value="online_banking"> <i class="fas fa-university"></i> Online banking
                                <small style="display: block; color: var(--secondary-color); margin: 6px 0 0 20px;">Secure bank transfer</small>
                            </label>
                        </div>
                    </div>

                    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 14px; margin: 20px 0; color: #1e40af;">
                        <i class="fas fa-info-circle"></i> Your payment will be submitted for cashier verification. A receipt will be issued after approval.
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-lock"></i> Pay Now</button>
                    <a href="<?= BASE_URL ?>/student/payments" class="btn btn-secondary" style="margin-left: 8px;">Back to payments</a>
                </form>
            </div>
        </div>

        <aside class="card">
            <div class="card-header"><h3 class="card-title">Order summary</h3></div>
            <div class="card-body">
                <div style="display: flex; justify-content: space-between; gap: 16px; margin-bottom: 14px;"><span style="color: var(--secondary-color);">Monthly fee</span><strong>Rs. <?= number_format($dueAmount, 2) ?></strong></div>
                <div style="border-top: 1px solid #e2e8f0; padding-top: 14px; display: flex; justify-content: space-between; gap: 16px; font-size: 1.1rem;"><strong>Total</strong><strong style="color: var(--primary-color);">Rs. <?= number_format($dueAmount, 2) ?></strong></div>
                <p style="color: var(--secondary-color); font-size: 0.85rem; margin-top: 18px;"><i class="fas fa-shield-alt"></i> Payments are securely recorded and reviewed before a receipt is issued.</p>
            </div>
        </aside>
    </div>
</div>
