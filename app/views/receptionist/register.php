<!-- Receptionist page for registering a new student. -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Register New Student</h3>
        <a href="<?= BASE_URL ?>/receptionist/dashboard" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/receptionist/register">
            <?= csrfField() ?>
            <h4 style="margin-bottom: 15px;">Account Information</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div class="form-group">
                    <label>Username *</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control">
                </div>
            </div>
            <h4 style="margin-bottom: 15px;">Personal Information</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div class="form-group">
                    <label>First Name *</label>
                    <input type="text" name="first_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Last Name *</label>
                    <input type="text" name="last_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" class="form-select">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control">
                </div>
            </div>
            <h4 style="margin-bottom: 15px;">Class / Course Enrollment</h4>
            <div class="form-group" style="margin-bottom: 20px;">
                <label>Class / Course *</label>
                <select name="batch_id" class="form-select" required>
                    <option value="">Select class/course</option>
                    <?php foreach ($batches as $batch): ?>
                    <option value="<?= (int) $batch['id'] ?>"><?= htmlspecialchars($batch['course_name'] . ' - ' . $batch['batch_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="2"></textarea>
            </div>
            <h4 style="margin-bottom: 15px;">Guardian Information</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div class="form-group">
                    <label>Guardian Name</label>
                    <input type="text" name="guardian_name" class="form-control">
                </div>
                <div class="form-group">
                    <label>Guardian Phone</label>
                    <input type="text" name="guardian_phone" class="form-control">
                </div>
                <div class="form-group">
                    <label>School</label>
                    <input type="text" name="school" class="form-control">
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Register Student</button>
        </form>
    </div>
</div>
