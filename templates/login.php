<?php
ini_set('session.cookie_path', '/');
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/api/Auth/auth_function.php';

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $errors = validateLogin($_POST);

    if (empty($errors)) {
        if (loginUser($conn, $_POST)) {
            // Redirect to home page after successful login
            // Re-start session to ensure it's active
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $redirectUrl = url('templates/home.php');
            header('Location: ' . $redirectUrl);
            exit();
        } else {
            $_SESSION = [];
            session_destroy();

            $errors['general'] = "Invalid email or password.";
        }
    }
}

include __DIR__ . '/../src/includes/header.php';
?>
<main class="min-h-screen flex items-center justify-center py-12 px-4" style="background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%); min-height: calc(100vh - 80px);">
<div class="auth-container" style="max-width: 500px; width: 100%; margin: 0 auto; padding: 40px; background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);">
    <div class="auth-header text-center" style="margin-bottom: 30px;">
        <h1 class="brand-primary brand" style="font-size: 48px; margin-bottom: 20px; color: var(--brand-primary); font-weight: 700;">
            Prop<span class="brand-secondary" style="color: var(--brand-secondary);">Ex</span>
        </h1>
        <h5 class="auth-title" style="color: var(--brand-primary); margin-bottom: 10px; font-size: 24px; font-weight: 600;">Login to Your Account</h5>
        <p class="auth-subtitle" style="color: var(--text-secondary); font-size: 14px; margin-bottom: 0;">Enter your credentials to continue</p>
    </div>

    <?php if (isset($errors['general'])): ?>
        <div class="message message-error" style="padding: 15px; border-radius: 5px; margin: 20px 0; background: rgba(220, 38, 38, 0.1); border: 1px solid var(--accent-negative); color: var(--accent-negative);"><?= $errors['general'] ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group" style="margin-bottom: 20px;">
            <label for="email" style="display: block; margin-bottom: 8px; color: var(--text-primary); font-weight: 500; font-size: 14px;">Email Address</label>
            <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; box-sizing: border-box;">
            <?php if (isset($errors['email'])): ?>
                <small class="accent-negative" style="display: block; margin-top: 5px; font-size: 12px; color: var(--accent-negative);"><?= $errors['email'] ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label for="password" style="display: block; margin-bottom: 8px; color: var(--text-primary); font-weight: 500; font-size: 14px;">Password</label>
            <input type="password" name="password" id="password" class="form-control" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; box-sizing: border-box;">
            <?php if (isset($errors['password'])): ?>
                <small class="accent-negative" style="display: block; margin-top: 5px; font-size: 12px; color: var(--accent-negative);"><?= $errors['password'] ?></small>
            <?php endif; ?>
        </div>

        <button type="submit" name="login" class="btn btn-primary" style="width: 100%; background: var(--brand-primary); color: white; border: none; padding: 12px 24px; font-size: 16px; font-weight: 600; border-radius: 5px; cursor: pointer; transition: all 0.3s ease;">Login</button>

        <div class="text-center" style="margin-top: 20px;">
            <p style="color: var(--text-secondary);">Don't have an account? <a href="<?php echo url('templates/register.php'); ?>" style="color: var(--brand-primary); text-decoration: none; font-weight: 500;">Register here</a></p>
        </div>
    </form>
</div>
</main>
<?php include __DIR__ . '/../src/includes/footer.php'; ?>