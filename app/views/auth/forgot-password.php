<!DOCTYPE html>
<!-- Authentication page for requesting a password reset link. -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Royal Education Center</title>
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
        .login-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            display: flex;
            margin: 20px;
        }
        .login-left {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 50px;
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-right { padding: 50px; width: 50%; }
        .login-logo { font-size: 2.5rem; margin-bottom: 20px; }
        .login-title { font-size: 1.75rem; font-weight: 700; margin-bottom: 15px; }
        .login-subtitle { opacity: 0.9; line-height: 1.6; }
        .login-header { text-align: center; margin-bottom: 30px; }
        .login-header h2 { color: var(--dark-bg); font-size: 1.5rem; font-weight: 700; margin-bottom: 10px; }
        .login-header p { color: var(--secondary-color); }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; margin-bottom: 8px; font-weight: 500; color: var(--dark-bg); }
        .input-group { position: relative; }
        .input-group i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--secondary-color); }
        .form-control { width: 100%; padding: 12px 15px 12px 45px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem; transition: all 0.2s; }
        .form-control:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
        .btn-primary { width: 100%; padding: 12px; background-color: var(--primary-color); color: white; border: none; border-radius: 8px; font-size: 0.875rem; font-weight: 500; cursor: pointer; transition: all 0.2s; }
        .btn-primary:hover { background-color: var(--primary-dark); }
        .alert { padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.875rem; }
        .alert-success { background-color: rgba(16, 185, 129, 0.1); color: var(--success-color); border: 1px solid rgba(16, 185, 129, 0.2); }
        .alert-danger { background-color: rgba(239, 68, 68, 0.1); color: var(--danger-color); border: 1px solid rgba(239, 68, 68, 0.2); }
        .alert-info { background-color: rgba(59, 130, 246, 0.1); color: var(--primary-color); border: 1px solid rgba(59, 130, 246, 0.2); }
        .login-footer { text-align: center; margin-top: 20px; color: var(--secondary-color); font-size: 0.875rem; }
        .login-footer a { color: var(--primary-color); text-decoration: none; }
        @media (max-width: 768px) {
            .login-container { flex-direction: column; max-width: 500px; }
            .login-left, .login-right { width: 100%; padding: 30px; }
            .login-left { text-align: center; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <div class="login-logo"><i class="fas fa-graduation-cap"></i></div>
            <h1 class="login-title">Royal Education Center</h1>
            <p class="login-subtitle">Empowering minds, shaping futures. Join our community of learners and educators.</p>
        </div>
        <div class="login-right">
            <div class="login-header">
                <h2>Forgot Password</h2>
                <p>Enter your email to reset your password</p>
            </div>
            <?php if (hasFlash('info')): ?>
                <div class="alert alert-info"><i class="fas fa-info-circle"></i> <?= getFlash('info') ?></div>
            <?php endif; ?>
            <?php if (hasFlash('error')): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= getFlash('error') ?></div>
            <?php endif; ?>
            <form method="POST" action="<?= BASE_URL ?>/forgot-password">
                <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required autofocus>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Reset Link
                    </button>
                </div>
            </form>
            <div class="login-footer">
                <p><a href="<?= BASE_URL ?>/login">Back to Login</a></p>
                <p style="margin-top: 10px;">&copy; <?= date('Y') ?> Royal Education Center. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
