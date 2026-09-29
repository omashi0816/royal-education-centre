<!DOCTYPE html>
<!-- Authentication page for registering a new account. -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Royal Education Center</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .register-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            width: 100%;
            max-width: 1000px;
            display: flex;
            margin: 20px;
        }
        .register-left { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: white; padding: 40px; width: 45%; display: flex; flex-direction: column; justify-content: center; }
        .register-right { padding: 40px; width: 55%; }
        .register-title { font-size: 1.7rem; font-weight: 700; margin-bottom: 12px; }
        .register-subtitle { opacity: 0.9; line-height: 1.6; }
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; margin-bottom: 8px; font-weight: 500; color: var(--dark-bg); }
        .form-control, .form-select { width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem; }
        .form-control:focus, .form-select:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
        .btn-primary { width: 100%; padding: 12px; background-color: var(--primary-color); color: white; border: none; border-radius: 8px; font-size: 0.875rem; font-weight: 500; cursor: pointer; }
        .alert { padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.875rem; }
        .alert-success { background-color: rgba(16, 185, 129, 0.1); color: var(--success-color); border: 1px solid rgba(16, 185, 129, 0.2); }
        .alert-danger { background-color: rgba(239, 68, 68, 0.1); color: var(--danger-color); border: 1px solid rgba(239, 68, 68, 0.2); }
        .register-footer { text-align: center; margin-top: 16px; color: var(--secondary-color); font-size: 0.875rem; }
        .register-footer a { color: var(--primary-color); text-decoration: none; }
        @media (max-width: 768px) { .register-container { flex-direction: column; } .register-left, .register-right { width: 100%; } }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-left">
            <div style="font-size: 2.4rem; margin-bottom: 16px;"><i class="fas fa-graduation-cap"></i></div>
            <h1 class="register-title">Create Your Account</h1>
            <p class="register-subtitle">Create your student account to access courses, classes, and your learning progress.</p>
        </div>

        <div class="register-right">
            <div style="text-align: center; margin-bottom: 20px;">
                <h2 style="margin-bottom: 8px;">Register</h2>
                <p style="color: var(--secondary-color);">Fill in your details below</p>
            </div>

            <?php if (hasFlash('success')): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= getFlash('success') ?></div>
            <?php endif; ?>
            <?php if (hasFlash('error')): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= getFlash('error') ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>/register">
                <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">

                <div class="form-group">
                    <label class="form-label" for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" placeholder="Enter your full name" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" class="form-control" placeholder="Enter your phone number">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm your password" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn-primary"><i class="fas fa-user-plus"></i> Create Account</button>
                </div>
            </form>

            <div class="register-footer">
                <p>Already have an account? <a href="<?= BASE_URL ?>/login">Sign in</a></p>
            </div>
        </div>
    </div>
</body>
</html>
