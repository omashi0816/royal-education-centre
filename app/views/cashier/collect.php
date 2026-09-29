<!-- Cashier page for collecting a student payment. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Collect Payment</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/cashier/collect">
            <?= csrfField() ?>
            <div class="form-group">
                <label>Select Student *</label>
                <select name="student_id" class="form-select" required>
                    <option value="">Select Student</option>
                    <?php foreach ($students as $student): ?>
                    <option value="<?= $student['id'] ?>"><?= htmlspecialchars($student['student_code']) ?> - <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Payment Type *</label>
                <select name="payment_type" class="form-select" required>
                    <option value="registration">Registration Fee</option>
                    <option value="course_fee">Course Fee</option>
                    <option value="monthly_fee">Monthly Fee</option>
                    <option value="exam_fee">Exam Fee</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Amount *</label>
                    <input type="number" step="0.01" name="amount" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Discount</label>
                    <input type="number" step="0.01" name="discount" class="form-control" value="0">
                </div>
            </div>
            <div class="form-group">
                <label>Payment Method *</label>
                <select name="payment_method" class="form-select" required>
                    <option value="cash">Cash</option>
                    <option value="online">Online</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="card">Card</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Collect Payment</button>
        </form>
    </div>
</div>
